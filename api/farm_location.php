<?php
header('Content-Type: application/json');
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    // CORS Headers
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    
    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    // ===== GET: Fetch farm location by farm_id =====
    if ($method === 'GET') {
        $farm_id = isset($_GET['farm_id']) ? $_GET['farm_id'] : null;
        
        if (!$farm_id) {
            sendError('farm_id parameter is required', 400);
        }

        $stmt = $pdo->prepare("
            SELECT 
                farm_id,
                name,
                location,
                state,
                latitude,
                longitude,
                owner_name,
                contact_phone,
                mrl_status,
                created_at
            FROM farms 
            WHERE farm_id COLLATE utf8mb4_unicode_ci = ? COLLATE utf8mb4_unicode_ci
            LIMIT 1
        ");

        if (!$stmt->execute([$farm_id])) {
            throw new Exception("Query failed: " . implode(", ", $stmt->errorInfo()));
        }

        $farm = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$farm) {
            sendError('Farm not found', 404);
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Farm details retrieved successfully',
            'data' => [
                'farm_id' => $farm['farm_id'],
                'name' => $farm['name'],
                'location' => $farm['location'],
                'state' => $farm['state'],
                'latitude' => floatval($farm['latitude']),
                'longitude' => floatval($farm['longitude']),
                'owner_name' => $farm['owner_name'],
                'contact_phone' => $farm['contact_phone'],
                'mrl_status' => $farm['mrl_status']
            ]
        ]);
    }

} catch (Exception $e) {
    sendError($e->getMessage(), 500);
}

?>
