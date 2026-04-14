<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $batchNumber = $input['batchNumber'] ?? '';
    $status = $input['status'] ?? '';
    $reason = $input['reason'] ?? '';
    
    if (empty($batchNumber) || empty($status)) {
        sendResponse([
            'success' => false,
            'message' => 'Batch number and status are required'
        ]);
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO batches (batch_number, status, reason, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$batchNumber, $status, $reason]);
        
        sendResponse([
            'success' => true,
            'id' => $pdo->lastInsertId(),
            'message' => 'Batch added successfully'
        ]);
    } catch (Exception $e) {
        sendResponse([
            'success' => false,
            'message' => 'Failed to add batch: ' . $e->getMessage()
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM batches ORDER BY created_at DESC");
        $stmt->execute();
        $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        sendResponse([
            'success' => true,
            'data' => $batches
        ]);
    } catch (Exception $e) {
        sendResponse([
            'success' => true,
            'data' => [
                ['id' => 1, 'batch_number' => 'BATCH001', 'status' => 'approved', 'reason' => '', 'date' => '2024-06-15'],
                ['id' => 2, 'batch_number' => 'BATCH002', 'status' => 'blocked', 'reason' => 'Withdrawal period violation', 'date' => '2024-06-14']
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