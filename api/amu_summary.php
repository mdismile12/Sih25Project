<?php
// AMU Summary API - Total counts, animal types, biomass analysis
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();
require_once 'config.php';

$action = $_GET['action'] ?? 'summary';
$level = $_GET['level'] ?? 'national';
$state = $_GET['state'] ?? null;
$district = $_GET['district'] ?? null;
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;

try {
    // Summary statistics
    if ($action === 'summary') {
        $params = [];
        $where = "WHERE 1=1";
        
        if ($state) { $where .= " AND state = ?"; $params[] = $state; }
        if ($start_date) { $where .= " AND created_at >= ?"; $params[] = $start_date; }
        if ($end_date) { $where .= " AND created_at <= ?"; $params[] = $end_date; }

        // Total AMU count and biomass
        $sql = "SELECT 
                COUNT(*) as total_prescriptions,
                COUNT(DISTINCT medicine_id) as unique_medicines,
                SUM(amount) as total_amu_amount,
                COUNT(DISTINCT species) as animal_types,
                COUNT(DISTINCT animal_id) as animals_prescribed_amu,
                COUNT(DISTINCT farm_id) as farms_involved,
                COUNT(DISTINCT state) as states_involved
                FROM amu_records $where";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get average amount and unit
        $sql2 = "SELECT unit, AVG(amount) as avg_amount FROM amu_records $where GROUP BY unit";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute($params);
        $units = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        ob_clean();
        echo json_encode([
            'success' => true,
            'data' => array_merge($summary, ['units' => $units])
        ]);
        exit;
    }

    // Animal count and type breakdown
    if ($action === 'animal_stats') {
        $params = [];
        $where = "WHERE 1=1";
        
        if ($state) { $where .= " AND state = ?"; $params[] = $state; }
        if ($district) { $where .= " AND location LIKE ?"; $params[] = "%$district%"; }
        if ($start_date) { $where .= " AND created_at >= ?"; $params[] = $start_date; }
        if ($end_date) { $where .= " AND created_at <= ?"; $params[] = $end_date; }

        // Count by species
        $sql = "SELECT 
                COALESCE(species, 'Unknown') as species,
                COUNT(*) as animal_count,
                COUNT(DISTINCT farm_id) as farms,
                SUM(amount) as total_amu,
                AVG(amount) as avg_amu,
                COUNT(DISTINCT medicine_id) as medicines_used
                FROM amu_records 
                $where 
                GROUP BY species 
                ORDER BY animal_count DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_clean();
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    // Regional biomass analysis (state/district level)
    if ($action === 'biomass_by_region') {
        $params = [];
        $groupBy = 'state';
        if ($level === 'district') { $groupBy = 'location'; }
        if ($level === 'national') { $groupBy = 'NULL'; }

        $where = "WHERE 1=1";
        // Only filter by state if level is not national and state is provided
        if ($state && $level !== 'national') { $where .= " AND state = ?"; $params[] = $state; }
        if ($start_date) { $where .= " AND created_at >= ?"; $params[] = $start_date; }
        if ($end_date) { $where .= " AND created_at <= ?"; $params[] = $end_date; }

        // For national level, don't group by anything (show aggregate)
        if ($level === 'national') {
            $sql = "SELECT 
                    'All India' as region,
                    SUM(amount) as total_biomass,
                    COUNT(*) as prescription_count,
                    COUNT(DISTINCT farm_id) as farm_count,
                    COUNT(DISTINCT species) as species_count,
                    COUNT(DISTINCT medicine_id) as medicine_count,
                    COUNT(DISTINCT state) as state_count,
                    MAX(unit) as unit
                    FROM amu_records 
                    $where";
        } else {
            $sql = "SELECT 
                    COALESCE($groupBy, 'Unknown') as region,
                    SUM(amount) as total_biomass,
                    COUNT(*) as prescription_count,
                    COUNT(DISTINCT farm_id) as farm_count,
                    COUNT(DISTINCT species) as species_count,
                    COUNT(DISTINCT medicine_id) as medicine_count,
                    MAX(unit) as unit
                    FROM amu_records 
                    $where 
                    GROUP BY $groupBy 
                    ORDER BY total_biomass DESC";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_clean();
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    // AMU by category and animal type
    if ($action === 'category_animal_matrix') {
        $params = [];
        $where = "WHERE 1=1";
        
        if ($state) { $where .= " AND state = ?"; $params[] = $state; }
        if ($start_date) { $where .= " AND created_at >= ?"; $params[] = $start_date; }
        if ($end_date) { $where .= " AND created_at <= ?"; $params[] = $end_date; }

        $sql = "SELECT 
                COALESCE(amu_category, 'Unknown') as category,
                COALESCE(species, 'Unknown') as species,
                COUNT(*) as count,
                SUM(amount) as total_amount
                FROM amu_records 
                $where 
                GROUP BY amu_category, species 
                ORDER BY category, total_amount DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_clean();
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    // Total AMU trend by month and animal
    if ($action === 'monthly_animal_trend') {
        $params = [];
        $where = "WHERE 1=1";
        
        if ($state) { $where .= " AND state = ?"; $params[] = $state; }
        if ($start_date) { $where .= " AND created_at >= ?"; $params[] = $start_date; }
        if ($end_date) { $where .= " AND created_at <= ?"; $params[] = $end_date; }

        $sql = "SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COALESCE(species, 'Unknown') as species,
                SUM(amount) as total_amount,
                COUNT(*) as usage_count
                FROM amu_records 
                $where 
                GROUP BY month, species 
                ORDER BY month DESC, total_amount DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_clean();
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    // Get distinct species list
    if ($action === 'species_list') {
        $sql = "SELECT DISTINCT COALESCE(species, 'Unknown') as species FROM amu_records WHERE species IS NOT NULL OR species IS NULL ORDER BY species";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_COLUMN);

        ob_clean();
        echo json_encode(['success' => true, 'data' => $data ?? []]);
        exit;
    }

    // Get distinct states
    if ($action === 'states_list') {
        $sql = "SELECT DISTINCT state FROM amu_records WHERE state IS NOT NULL ORDER BY state";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_COLUMN);

        ob_clean();
        echo json_encode(['success' => true, 'data' => $data ?? []]);
        exit;
    }

    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Unknown action']);

} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
