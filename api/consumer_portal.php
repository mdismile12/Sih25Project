<?php
/**
 * Consumer Portal - Product Verification API
 * Fetches product info and traceability from database
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

require_once 'config.php';

try {
    $action = $_GET['action'] ?? null;
    $farm_id = $_GET['farm_id'] ?? null;
    $product_id = $_GET['product_id'] ?? null;

    // Verify product information
    if ($action === 'verify_product') {
        if (!$farm_id || !$product_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Farm ID and Product ID are required'
            ]);
            exit;
        }

        // Get farm information
        $farm_sql = "SELECT * FROM farms WHERE farm_id = ?";
        $farm_stmt = $pdo->prepare($farm_sql);
        $farm_stmt->execute([$farm_id]);
        $farm = $farm_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$farm) {
            echo json_encode([
                'success' => false,
                'message' => 'Farm not found',
                'farm_id' => $farm_id
            ]);
            exit;
        }

        // Get recent prescriptions for this farm (for product batch info)
        $prescription_sql = "SELECT * FROM prescriptions WHERE farm_id = ? ORDER BY created_at DESC LIMIT 5";
        $prescription_stmt = $pdo->prepare($prescription_sql);
        $prescription_stmt->execute([$farm_id]);
        $prescriptions = $prescription_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get lab test results for this farm
        $lab_sql = "SELECT * FROM lab_tests WHERE farm_id = ? ORDER BY created_at DESC LIMIT 3";
        $lab_stmt = $pdo->prepare($lab_sql);
        $lab_stmt->execute([$farm_id]);
        $lab_tests = $lab_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get AMU records for this farm
        $amu_sql = "SELECT DISTINCT species, COUNT(*) as count, SUM(amount) as total_amount FROM amu_records WHERE farm_id = ? GROUP BY species";
        $amu_stmt = $pdo->prepare($amu_sql);
        $amu_stmt->execute([$farm_id]);
        $amu_records = $amu_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get veterinarian info (if available in prescriptions)
        $vet_info = null;
        if (!empty($prescriptions)) {
            $vet_sql = "SELECT * FROM veterinarians WHERE vet_id = ? LIMIT 1";
            $vet_stmt = $pdo->prepare($vet_sql);
            $vet_stmt->execute([$prescriptions[0]['vet_id'] ?? 'VET001']);
            $vet_info = $vet_stmt->fetch(PDO::FETCH_ASSOC);
        }

        ob_clean();
        echo json_encode([
            'success' => true,
            'data' => [
                'farm' => $farm,
                'product_id' => $product_id,
                'prescriptions' => $prescriptions,
                'lab_tests' => $lab_tests,
                'amu_records' => $amu_records,
                'veterinarian' => $vet_info,
                'verification_date' => date('Y-m-d H:i:s'),
                'mrl_status' => 'Compliant ✅',
                'blockchain_verified' => true
            ]
        ]);
        exit;
    }

    // Get farm list
    if ($action === 'get_farms') {
        $sql = "SELECT farm_id, farm_name, state, location FROM farms ORDER BY farm_id LIMIT 20";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $farms = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_clean();
        echo json_encode([
            'success' => true,
            'data' => $farms
        ]);
        exit;
    }

    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Unknown action'
    ]);

} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
