<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $farmId = $input['farmId'] ?? '';
    $alertType = $input['alertType'] ?? '';
    $description = $input['description'] ?? '';
    $severity = $input['severity'] ?? '';
    
    if (empty($farmId) || empty($description)) {
        sendResponse([
            'success' => false,
            'message' => 'Farm ID and description are required'
        ]);
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO farm_alerts (farm_id, alert_type, description, severity, status, created_at) VALUES (?, ?, ?, ?, 'active', NOW())");
        $stmt->execute([$farmId, $alertType, $description, $severity]);
        
        sendResponse([
            'success' => true,
            'id' => $pdo->lastInsertId(),
            'message' => 'Farm alert added successfully'
        ]);
    } catch (Exception $e) {
        sendResponse([
            'success' => false,
            'message' => 'Failed to add farm alert: ' . $e->getMessage()
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM farm_alerts ORDER BY created_at DESC");
        $stmt->execute();
        $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        sendResponse([
            'success' => true,
            'data' => $alerts
        ]);
    } catch (Exception $e) {
        sendResponse([
            'success' => true,
            'data' => []
        ]);
    }
} 
else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $alertId = $input['id'] ?? '';
    
    if (empty($alertId)) {
        sendResponse([
            'success' => false,
            'message' => 'Alert ID is required'
        ]);
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM farm_alerts WHERE id = ?");
        $stmt->execute([$alertId]);
        
        sendResponse([
            'success' => true,
            'message' => 'Farm alert deleted successfully'
        ]);
    } catch (Exception $e) {
        sendResponse([
            'success' => false,
            'message' => 'Failed to delete farm alert: ' . $e->getMessage()
        ]);
    }
}

else {
    sendResponse([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>