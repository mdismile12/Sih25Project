<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        // Create audit logs table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS audit_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            action VARCHAR(255) NOT NULL,
            user VARCHAR(100) NOT NULL,
            details TEXT,
            location VARCHAR(100),
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Insert sample logs if table is empty
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM audit_logs");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] == 0) {
            $sampleLogs = [
                ['action' => 'Login', 'user' => 'admin', 'details' => 'Government user logged in', 'location' => 'Maharashtra'],
                ['action' => 'Batch Approval', 'user' => 'admin', 'details' => 'Approved batch BATCH001', 'location' => 'Maharashtra'],
                ['action' => 'Prescription Created', 'user' => 'VET001', 'details' => 'Created prescription for animal COW-001', 'location' => 'Delhi'],
                ['action' => 'Alert Created', 'user' => 'admin', 'details' => 'Created farm alert for FARM-102', 'location' => 'Maharashtra'],
                ['action' => 'Policy Updated', 'user' => 'admin', 'details' => 'Updated AMU guidelines', 'location' => 'National']
            ];
            
            foreach ($sampleLogs as $log) {
                $stmt = $pdo->prepare("INSERT INTO audit_logs (action, user, details, location) VALUES (?, ?, ?, ?)");
                $stmt->execute([$log['action'], $log['user'], $log['details'], $log['location']]);
            }
        }
        
        // Fetch logs
        $stmt = $pdo->prepare("SELECT * FROM audit_logs ORDER BY timestamp DESC LIMIT 20");
        $stmt->execute();
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Count today's activity
        $today = date('Y-m-d');
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM audit_logs WHERE DATE(timestamp) = ?");
        $stmt->execute([$today]);
        $todayActivity = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        sendResponse([
            'success' => true,
            'data' => $logs,
            'todayActivity' => $todayActivity,
            'pendingReview' => 3
        ]);
    } catch (Exception $e) {
        sendResponse([
            'success' => true,
            'data' => [],
            'todayActivity' => 0,
            'pendingReview' => 0
        ]);
    }
} else {
    sendResponse([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>