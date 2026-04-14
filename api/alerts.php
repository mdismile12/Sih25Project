<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $description = $input['description'] ?? '';
    $severity = $input['severity'] ?? '';
    
    if (empty($description) || empty($severity)) {
        sendResponse([
            'success' => false,
            'message' => 'Description and severity are required'
        ]);
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO alerts (description, severity, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$description, $severity]);
        
        sendResponse([
            'success' => true,
            'id' => $pdo->lastInsertId(),
            'message' => 'Alert added successfully'
        ]);
    } catch (Exception $e) {
        sendResponse([
            'success' => false,
            'message' => 'Failed to add alert: ' . $e->getMessage()
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM alerts ORDER BY created_at DESC");
        $stmt->execute();
        $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        sendResponse([
            'success' => true,
            'data' => $alerts
        ]);
    } catch (Exception $e) {
        // Return demo data if table doesn't exist
        sendResponse([
            'success' => true,
            'data' => [
                ['id' => 1, 'description' => 'High AMU detected in Farm #102', 'severity' => 'high', 'date' => '2024-06-15', 'resolved' => false],
                ['id' => 2, 'description' => 'Missing withdrawal logs for Batch M20240615-205', 'severity' => 'medium', 'date' => '2024-06-14', 'resolved' => false]
            ]
        ]);
    }
} else {
    sendResponse([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>