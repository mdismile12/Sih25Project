<?php
header('Content-Type: application/json');
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);

function writeLog($message) {
    $log_file = __DIR__ . '/logs/lab_reports.log';
    if (!is_dir(dirname($log_file))) {
        mkdir(dirname($log_file), 0777, true);
    }
    error_log("[" . date('Y-m-d H:i:s') . "] " . $message . "\n", 3, $log_file);
}

function checkMRLCompliance($chemical_name, $detected_value, $mrl_limit) {
    return floatval($detected_value) <= floatval($mrl_limit);
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    $test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : null;
    $sample_id = isset($_GET['sample_id']) ? intval($_GET['sample_id']) : null;

    $input = json_decode(file_get_contents('php://input'), true);
    
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    
    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    // ===== GET: Fetch lab test reports =====
    if ($method === 'GET') {
        $query = "SELECT * FROM lab_test_reports WHERE 1=1";
        $params = array();

        if ($id) {
            $query .= " AND id = ?";
            $params[] = $id;
        }
        if ($test_id) {
            $query .= " AND lab_test_id = ?";
            $params[] = $test_id;
        }
        if ($sample_id) {
            $query .= " AND sample_id = ?";
            $params[] = $sample_id;
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $pdo->prepare($query);
        if (!$stmt->execute($params)) {
            throw new Exception("Query failed: " . implode(", ", $stmt->errorInfo()));
        }

        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($reports as &$row) {
            // Parse JSON fields
            if (isset($row['test_results']) && is_string($row['test_results'])) {
                $row['test_results'] = json_decode($row['test_results'], true);
            }
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $reports,
            'count' => count($reports)
        ]);
    }

    // ===== POST: Generate lab report =====
    elseif ($method === 'POST') {
        $test_id = $input['lab_test_id'] ?? null;
        $sample_id = $input['sample_id'] ?? null;
        $lab_name = $input['lab_name'] ?? 'Agrisense Reference Lab';
        $technician = $input['technician'] ?? null;
        $test_results = $input['test_results'] ?? [];
        $mrl_status = $input['mrl_status'] ?? 'pending';

        if (!$test_id || !$sample_id) {
            throw new Exception("Missing required fields: lab_test_id, sample_id");
        }

        // Analyze MRL compliance
        if (!empty($test_results) && is_array($test_results)) {
            $all_compliant = true;
            foreach ($test_results as $result) {
                if (isset($result['detected_value']) && isset($result['mrl_limit'])) {
                    if (!checkMRLCompliance($result['chemical'] ?? '', $result['detected_value'], $result['mrl_limit'])) {
                        $all_compliant = false;
                        break;
                    }
                }
            }
            $mrl_status = $all_compliant ? 'approved' : 'rejected';
        }

        $test_results_json = is_array($test_results) ? json_encode($test_results) : $test_results;
        $report_date = date('Y-m-d');

            $stmt = $pdo->prepare("
            INSERT INTO lab_test_reports 
            (lab_test_id, sample_id, lab_name, technician, test_results, mrl_status, report_date, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        if (!$stmt->execute([$test_id, $sample_id, $lab_name, $technician, $test_results_json, $mrl_status, $report_date])) {
            throw new Exception("Failed to create report: " . implode(", ", $stmt->errorInfo()));
        }

        $report_id = $pdo->lastInsertId();

        // Update lab test status
        $update_stmt = $pdo->prepare("UPDATE lab_tests SET status = 'report_generated' WHERE id = ?");
        $update_stmt->execute([$test_id]);

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Lab report generated successfully',
            'report_id' => $report_id,
            'mrl_status' => $mrl_status
        ]);
    }

    // ===== PUT: Update report status (approve/reject) =====
    elseif ($method === 'PUT') {
        $id = $input['id'] ?? $id;
        $mrl_status = $input['mrl_status'] ?? null;
        $approval_notes = $input['approval_notes'] ?? null;
        $approved_by = $input['approved_by'] ?? null;

        if (!$id) {
            throw new Exception("Missing required parameter: id");
        }

        $updates = [];
        $params = [];
        $types = '';

        if ($mrl_status) {
            $updates[] = "mrl_status = ?";
            $params[] = $mrl_status;
            $types .= 's';
        }
        if ($approval_notes) {
            $updates[] = "approval_notes = ?";
            $params[] = $approval_notes;
            $types .= 's';
        }
        if ($approved_by) {
            $updates[] = "approved_by = ?";
            $params[] = $approved_by;
        }

        $updates[] = "approved_at = NOW()";
        $params[] = $id;

        $query = "UPDATE lab_test_reports SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($query);

        if (!$stmt->execute($params)) {
            throw new Exception("Failed to update report: " . implode(", ", $stmt->errorInfo()));
        }

        // Also update the lab test
        $test_id_stmt = $pdo->prepare("SELECT lab_test_id FROM lab_test_reports WHERE id = ?");
        $test_id_stmt->execute([$id]);
        $test_row = $test_id_stmt->fetch(PDO::FETCH_ASSOC);

        if ($test_row) {
            $new_status = ($mrl_status === 'approved') ? 'approved' : 'rejected';
            $update_test = $pdo->prepare("UPDATE lab_tests SET status = ? WHERE id = ?");
            $update_test->execute([$new_status, $test_row['lab_test_id']]);
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Report status updated successfully'
        ]);
    }

    else {
        throw new Exception("Method not allowed: $method");
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
