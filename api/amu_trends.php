<?php
// AMU Trends Analysis API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();
require_once 'config.php';

// Ensure extended tables exist
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `medicine_species_stats` (
          `id` INT PRIMARY KEY AUTO_INCREMENT,
          `medicine_id` VARCHAR(50) NOT NULL,
          `medicine_name` VARCHAR(255) NOT NULL,
          `species` VARCHAR(100) NOT NULL,
          `total_usage_count` INT DEFAULT 0,
          `total_amount_used` DECIMAL(14,4) DEFAULT 0,
          `unit` VARCHAR(50) DEFAULT NULL,
          `state` VARCHAR(100) DEFAULT NULL,
          `region` VARCHAR(100) DEFAULT NULL,
          `last_used` TIMESTAMP NULL,
          `avg_usage_per_prescription` DECIMAL(10,2) DEFAULT 0,
          `trend_direction` VARCHAR(20) DEFAULT 'stable',
          `trend_percentage` DECIMAL(5,2) DEFAULT 0,
          `primary_reason` VARCHAR(255) DEFAULT NULL,
          `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          INDEX idx_medicine_species (medicine_id, species),
          INDEX idx_state (state),
          INDEX idx_trend_direction (trend_direction),
          UNIQUE KEY unique_med_species_state (medicine_id, species, state)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `medicine_usage_trends` (
          `id` INT PRIMARY KEY AUTO_INCREMENT,
          `medicine_id` VARCHAR(50) NOT NULL,
          `medicine_name` VARCHAR(255) NOT NULL,
          `species` VARCHAR(100) NOT NULL,
          `month` VARCHAR(7) NOT NULL,
          `state` VARCHAR(100) DEFAULT NULL,
          `usage_count` INT DEFAULT 0,
          `total_amount` DECIMAL(14,4) DEFAULT 0,
          `unit` VARCHAR(50) DEFAULT NULL,
          `avg_dosage` DECIMAL(10,2) DEFAULT 0,
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          INDEX idx_medicine_month (medicine_id, month),
          INDEX idx_species_month (species, month),
          INDEX idx_state_month (state, month),
          UNIQUE KEY unique_med_species_month_state (medicine_id, species, month, state)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
} catch (Exception $e) {
    error_log("Extended AMU table creation failed: " . $e->getMessage());
}

$action = $_GET['action'] ?? 'high_usage';
$level = $_GET['level'] ?? 'national'; // national | state | district
$state = $_GET['state'] ?? null;
$district = $_GET['district'] ?? null;
$species = $_GET['species'] ?? null;
$medicine_id = $_GET['medicine_id'] ?? null;
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;
$limit = intval($_GET['limit'] ?? 10);

try {
    // Refresh stats from amu_records
    if ($action === 'refresh_stats') {
        refreshMedicineStats($pdo, $state);
        ob_clean();
        echo json_encode(['success' => true, 'message' => 'Statistics refreshed']);
        exit;
    }

    // Get medicines with highest usage rate
    if ($action === 'high_usage') {
        $params = [];
        $where = "WHERE 1=1";
        
        if ($state) { $where .= " AND state = ?"; $params[] = $state; }
        if ($species) { $where .= " AND species = ?"; $params[] = $species; }
        if ($medicine_id) { $where .= " AND medicine_id = ?"; $params[] = $medicine_id; }

        $sql = "SELECT 
                    medicine_id, medicine_name, species, 
                    SUM(amount) as total_amount, unit, 
                    COUNT(*) as usage_count, state,
                    AVG(amount) as avg_usage
                FROM amu_records 
                $where ";
        
        if ($start_date) { $where .= " AND created_at >= ?"; $params[] = $start_date; }
        if ($end_date) { $where .= " AND created_at <= ?"; $params[] = $end_date; }

        $sql .= " GROUP BY medicine_id, species, state 
                 ORDER BY total_amount DESC LIMIT $limit";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_clean();
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    // Get species-wise medicine usage
    if ($action === 'species_usage') {
        $params = [];
        $where = "WHERE 1=1";
        
        if ($state) { $where .= " AND state = ?"; $params[] = $state; }
        if ($species) { $where .= " AND species = ?"; $params[] = $species; }
        
        if ($start_date) { $where .= " AND created_at >= ?"; $params[] = $start_date; }
        if ($end_date) { $where .= " AND created_at <= ?"; $params[] = $end_date; }

        $sql = "SELECT 
                    species, medicine_name, medicine_id, amu_category,
                    SUM(amount) as total_amount, COUNT(*) as usage_count,
                    AVG(amount) as avg_amount, unit, state
                FROM amu_records 
                $where 
                GROUP BY species, medicine_id, state
                ORDER BY species, total_amount DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_clean();
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    // Get trend analysis (month over month)
    if ($action === 'trend_analysis') {
        $params = [];
        $where = "WHERE 1=1";
        
        if ($medicine_id) { $where .= " AND medicine_id = ?"; $params[] = $medicine_id; }
        if ($state) { $where .= " AND state = ?"; $params[] = $state; }
        if ($species) { $where .= " AND species = ?"; $params[] = $species; }
        
        if ($start_date) { $where .= " AND created_at >= ?"; $params[] = $start_date; }
        if ($end_date) { $where .= " AND created_at <= ?"; $params[] = $end_date; }

        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    medicine_name, medicine_id, species,
                    SUM(amount) as total_amount, COUNT(*) as usage_count,
                    AVG(amount) as avg_dosage, unit, state
                FROM amu_records 
                $where 
                GROUP BY month, medicine_id, species, state
                ORDER BY month DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate trend direction (comparing last 2 months)
        $trendData = calculateTrends($data);

        ob_clean();
        echo json_encode(['success' => true, 'data' => $trendData]);
        exit;
    }

    // Get top medicines by region
    if ($action === 'top_medicines') {
        $params = [];
        $where = "WHERE 1=1";
        
        if ($state) { $where .= " AND state = ?"; $params[] = $state; }
        if ($species) { $where .= " AND species = ?"; $params[] = $species; }

        $sql = "SELECT 
                    CASE WHEN state IS NOT NULL THEN state ELSE 'National' END as region,
                    medicine_name, medicine_id, amu_category,
                    SUM(amount) as total_usage, COUNT(*) as prescription_count,
                    AVG(amount) as avg_dosage, unit
                FROM amu_records 
                $where 
                GROUP BY region, medicine_id
                ORDER BY region, total_usage DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_clean();
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    // Get distinct species
    if ($action === 'species_list') {
        $sql = "SELECT DISTINCT species FROM amu_records WHERE species IS NOT NULL ORDER BY species ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_COLUMN);

        ob_clean();
        echo json_encode(['success' => true, 'data' => $data ?? []]);
        exit;
    }

    // Get medicine reasons
    if ($action === 'reasons') {
        $params = [];
        $where = "WHERE reason IS NOT NULL";
        
        if ($state) { $where .= " AND state = ?"; $params[] = $state; }
        if ($species) { $where .= " AND species = ?"; $params[] = $species; }
        if ($medicine_id) { $where .= " AND medicine_id = ?"; $params[] = $medicine_id; }

        $sql = "SELECT reason, COUNT(*) as count 
                FROM amu_records 
                $where 
                GROUP BY reason 
                ORDER BY count DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_clean();
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function refreshMedicineStats($pdo, $state = null) {
    try {
        $where = "";
        $params = [];
        if ($state) { $where = "WHERE state = ?"; $params[] = $state; }

        $sql = "INSERT INTO medicine_species_stats 
                (medicine_id, medicine_name, species, total_usage_count, total_amount_used, unit, state, avg_usage_per_prescription, trend_direction, primary_reason, last_used)
                SELECT 
                    medicine_id, medicine_name, species,
                    COUNT(*) as count, SUM(amount) as total, unit, state,
                    AVG(amount) as avg, 'stable', reason, MAX(created_at)
                FROM amu_records 
                $where 
                GROUP BY medicine_id, species, state
                ON DUPLICATE KEY UPDATE
                    total_usage_count = VALUES(total_usage_count),
                    total_amount_used = VALUES(total_amount_used),
                    avg_usage_per_prescription = VALUES(avg_usage_per_prescription),
                    last_used = VALUES(last_used)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } catch (Exception $e) {
        error_log("Stats refresh failed: " . $e->getMessage());
    }
}

function calculateTrends($data) {
    // Group by medicine and species
    $grouped = [];
    foreach ($data as $row) {
        $key = $row['medicine_id'] . '_' . $row['species'] . '_' . ($row['state'] ?? 'national');
        if (!isset($grouped[$key])) {
            $grouped[$key] = [];
        }
        $grouped[$key][] = $row;
    }

    $result = [];
    foreach ($grouped as $key => $items) {
        usort($items, fn($a, $b) => strcmp($b['month'], $a['month']));
        
        $trend = 'stable';
        $percentage = 0;
        if (count($items) >= 2) {
            $latest = (float)$items[0]['total_amount'];
            $previous = (float)$items[1]['total_amount'];
            if ($previous != 0) {
                $percentage = round((($latest - $previous) / $previous) * 100, 2);
                $trend = $percentage > 5 ? 'increasing' : ($percentage < -5 ? 'decreasing' : 'stable');
            }
        }

        foreach ($items as $item) {
            $item['trend'] = $trend;
            $item['trend_percentage'] = $percentage;
            $result[] = $item;
        }
    }

    return $result;
}
?>
