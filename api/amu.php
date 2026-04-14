<?php
// AMU Aggregation API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();
require_once 'config.php';

// Ensure amu_records table exists (create if missing) to avoid 500 when migration not run
try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `amu_records` (
            `id` INT PRIMARY KEY AUTO_INCREMENT,
            `prescription_id` INT DEFAULT NULL,
            `prescription_item_id` INT DEFAULT NULL,
            `medicine_id` VARCHAR(50) NOT NULL,
            `medicine_name` VARCHAR(255) DEFAULT NULL,
            `medicine_type` VARCHAR(255) DEFAULT NULL,
            `amu_category` VARCHAR(50) DEFAULT NULL,
            `amount` DECIMAL(14,4) DEFAULT 0,
            `unit` VARCHAR(50) DEFAULT NULL,
            `farm_id` VARCHAR(50) DEFAULT NULL,
            `location` VARCHAR(255) DEFAULT NULL,
            `state` VARCHAR(100) DEFAULT NULL,
            `latitude` DECIMAL(10,8) DEFAULT NULL,
            `longitude` DECIMAL(10,8) DEFAULT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
} catch (Exception $e) {
        // If creation fails, log and continue; queries will provide errors that are returned as JSON
        error_log("amu table create failed: " . $e->getMessage());
}

// Utility: classify medicine into AMU category (VCIA / VHIA / VIA)
function classify_amu_category($medicine_type, $medicine_name = '') {
    $t = strtolower($medicine_type);
    // Simple mapping - adjust as needed
    $vcia_keywords = ['fluoroquinolone', '3rd generation', 'cephalosporin', 'macrolide', 'polymyxin', 'carbapenem'];
    foreach ($vcia_keywords as $kw) {
        if (strpos($t, $kw) !== false || stripos($medicine_name, $kw) !== false) return 'VCIA';
    }
    $vhia_keywords = ['tetracycline', 'aminoglycoside', 'sulfonamide', 'beta-lactam', 'penicillin'];
    foreach ($vhia_keywords as $kw) {
        if (strpos($t, $kw) !== false || stripos($medicine_name, $kw) !== false) return 'VHIA';
    }
    return 'VIA';
}

// GET parameters
$action = $_GET['action'] ?? 'aggregate';
$level = $_GET['level'] ?? 'state'; // district | state | national
$state = $_GET['state'] ?? null;
$district = $_GET['district'] ?? null;
$category = $_GET['category'] ?? null; // VCIA | VHIA | VIA | null
$unit = $_GET['unit'] ?? null; // mg | ml | null
$start = $_GET['start_date'] ?? null;
$end = $_GET['end_date'] ?? null;

try {
    if ($action === 'aggregate') {
        // Build base query
        $params = [];
        $where = "WHERE 1=1";
        if ($state) { $where .= " AND state = ?"; $params[] = $state; }
        if ($district) { $where .= " AND location LIKE ?"; $params[] = "%$district%"; }
        if ($category) { $where .= " AND amu_category = ?"; $params[] = $category; }
        if ($unit) { $where .= " AND unit LIKE ?"; $params[] = "%$unit%"; }
        if ($start) { $where .= " AND created_at >= ?"; $params[] = $start; }
        if ($end) { $where .= " AND created_at <= ?"; $params[] = $end; }

        if ($level === 'district') {
            $sql = "SELECT location AS district, SUM(amount) AS total_amount, unit FROM amu_records $where GROUP BY location, unit ORDER BY total_amount DESC";
        } elseif ($level === 'national' || $level === 'india' || $level === 'all') {
            $sql = "SELECT 'India' AS region, SUM(amount) AS total_amount, unit FROM amu_records $where GROUP BY unit";
        } else {
            // default state
            $sql = "SELECT state, SUM(amount) AS total_amount, unit FROM amu_records $where GROUP BY state, unit ORDER BY total_amount DESC";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Normalize output to { label, total_amount, unit }
        $out = [];
        foreach ($rows as $r) {
            if (isset($r['state'])) {
                $label = $r['state'];
                $amount = $r['total_amount'] ?? $r['total'] ?? $r['amount'] ?? 0;
                $unit = $r['unit'] ?? null;
            } elseif (isset($r['district']) || isset($r['location'])) {
                $label = $r['district'] ?? $r['location'];
                $amount = $r['total_amount'] ?? $r['total'] ?? $r['amount'] ?? 0;
                $unit = $r['unit'] ?? null;
            } else {
                $label = $r['region'] ?? ($r['state'] ?? 'Region');
                $amount = $r['total_amount'] ?? $r['total'] ?? $r['amount'] ?? 0;
                $unit = $r['unit'] ?? null;
            }
            $out[] = ['label' => $label, 'amount' => (float)$amount, 'unit' => $unit];
        }

        ob_clean();
        echo json_encode(['success' => true, 'data' => $out]);
        exit;
    }

    if ($action === 'timeseries') {
        // Return monthly series for a given state/category
        $params = [];
        $where = "WHERE 1=1";
        if ($state) { $where .= " AND state = ?"; $params[] = $state; }
        if ($category) { $where .= " AND amu_category = ?"; $params[] = $category; }
        if ($unit) { $where .= " AND unit LIKE ?"; $params[] = "%$unit%"; }
        if ($start) { $where .= " AND created_at >= ?"; $params[] = $start; }
        if ($end) { $where .= " AND created_at <= ?"; $params[] = $end; }

        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(amount) AS total_amount, unit FROM amu_records $where GROUP BY month, unit ORDER BY month ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r) {
            $out[] = ['month' => $r['month'], 'amount' => (float)($r['total_amount'] ?? $r['amount'] ?? 0), 'unit' => $r['unit'] ?? null];
        }

        ob_clean();
        echo json_encode(['success' => true, 'data' => $out]);
        exit;
    }

    // Unknown action
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
