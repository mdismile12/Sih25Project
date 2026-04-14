<?php
header('Content-Type: application/json');
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // $pdo is already initialized in config.php
    if (!isset($pdo)) {
        throw new Exception("Database connection not available");
    }

    // Fetch all farms with essential information
    $stmt = $pdo->query("
        SELECT 
            farm_id,
            name,
            location,
            state,
            owner_name,
            contact_phone,
            mrl_status
        FROM farms
        ORDER BY name ASC
    ");
    
    $farms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($farms);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
