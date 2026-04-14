<?php
header('Content-Type: application/json');
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);

function writeLog($message) {
    $log_file = __DIR__ . '/logs/heatmap.log';
    if (!is_dir(dirname($log_file))) {
        mkdir(dirname($log_file), 0777, true);
    }
    error_log("[" . date('Y-m-d H:i:s') . "] " . $message . "\n", 3, $log_file);
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $region = isset($_GET['region']) ? $_GET['region'] : 'all';
    $time_period = isset($_GET['time_period']) ? $_GET['time_period'] : '30days';
    $metric = isset($_GET['metric']) ? $_GET['metric'] : 'amu';

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    
    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    // ===== GET: Fetch heatmap data =====
    if ($method === 'GET') {
        
        // Get date range
        $end_date = new DateTime();
        $start_date = clone $end_date;
        
        switch ($time_period) {
            case '7days':
                $start_date->modify('-7 days');
                break;
            case '90days':
                $start_date->modify('-90 days');
                break;
            default: // 30days
                $start_date->modify('-30 days');
        }

        $start_date_str = $start_date->format('Y-m-d');
        $end_date_str = $end_date->format('Y-m-d');

        // Build query based on metric
        if ($metric === 'amu') {
            // AMU Heatmap Query
            $query = "
                SELECT 
                    f.farm_id,
                    f.name as farm_name,
                    f.latitude,
                    f.longitude,
                    f.state,
                    COUNT(p.id) as prescription_count,
                    AVG(CASE 
                        WHEN p.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 
                        ELSE NULL 
                    END) as amu_index,
                    (SELECT COUNT(*) FROM farm_alerts fa WHERE fa.farm_id COLLATE utf8mb4_unicode_ci = f.farm_id COLLATE utf8mb4_unicode_ci) as alert_count
                FROM farms f
                LEFT JOIN prescriptions p ON f.farm_id COLLATE utf8mb4_unicode_ci = p.farm_id COLLATE utf8mb4_unicode_ci 
                    AND p.created_at BETWEEN ? AND ?
                WHERE 1=1
            ";

            if ($region !== 'all') {
                $query .= " AND f.state = ?";
            }

            $query .= " GROUP BY f.farm_id, f.name, f.latitude, f.longitude, f.state
                       ORDER BY prescription_count DESC";

        } else if ($metric === 'mrl') {
            // MRL Compliance Heatmap
            $query = "
                SELECT 
                    f.farm_id,
                    f.name as farm_name,
                    f.latitude,
                    f.longitude,
                    f.state,
                    COUNT(CASE WHEN ltr.mrl_status = 'approved' THEN 1 END) as approved_tests,
                    COUNT(CASE WHEN ltr.mrl_status = 'rejected' THEN 1 END) as rejected_tests,
                    ROUND(COUNT(CASE WHEN ltr.mrl_status = 'approved' THEN 1 END) * 100.0 / 
                        NULLIF(COUNT(ltr.id), 0), 2) as compliance_rate
                FROM farms f
                LEFT JOIN lab_test_reports ltr ON f.farm_id COLLATE utf8mb4_unicode_ci = ltr.farm_id COLLATE utf8mb4_unicode_ci
                    AND ltr.created_at BETWEEN ? AND ?
                WHERE 1=1
            ";

            if ($region !== 'all') {
                $query .= " AND f.state = ?";
            }

            $query .= " GROUP BY f.farm_id, f.name, f.latitude, f.longitude, f.state
                       ORDER BY compliance_rate DESC";
        }

        $stmt = $pdo->prepare($query);
        
        $params = [$start_date_str, $end_date_str];
        if ($region !== 'all') {
            $params[] = $region;
        }

        if (!$stmt->execute($params)) {
            throw new Exception("Query failed: " . implode(", ", $stmt->errorInfo()));
        }

        $heatmap_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $heatmap_data,
            'count' => count($heatmap_data),
            'region' => $region,
            'metric' => $metric,
            'period' => $time_period,
            'date_range' => [
                'start' => $start_date_str,
                'end' => $end_date_str
            ]
        ]);
    }

    // ===== POST: Generate heatmap summary =====
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $region = $input['region'] ?? 'all';
        $metric = $input['metric'] ?? 'amu';

        // Get regional summary
        $query = "
            SELECT 
                state,
                COUNT(*) as farm_count,
                AVG(CASE WHEN status = 'compliant' THEN 1 ELSE 0 END) as compliance_percentage,
                COUNT(CASE WHEN has_alert = 1 THEN 1 END) as farms_with_alerts
            FROM farms
            WHERE 1=1
        ";

        if ($region !== 'all') {
            $query .= " AND state = ?";
        }

        $query .= " GROUP BY state";

        $stmt = $pdo->prepare($query);
        
        $params = [];
        if ($region !== 'all') {
            $params[] = $region;
        }

        if (!$stmt->execute($params)) {
            throw new Exception("Query failed: " . implode(", ", $stmt->errorInfo()));
        }

        $summary = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'summary' => $summary,
            'region' => $region,
            'metric' => $metric
        ]);
    }

} catch (Exception $e) {
    writeLog("Error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
