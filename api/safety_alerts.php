<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $batchId = $_GET['batchId'] ?? '';
    
    try {
        // Return safety alerts based on batch
        $alerts = [];
        
        if ($batchId == 'M20240615-102') {
            $alerts = [
                [
                    'title' => 'Quality Check',
                    'description' => 'Batch passed all quality tests',
                    'severity' => 'low'
                ]
            ];
        } elseif ($batchId == 'M20240614-205') {
            $alerts = [
                [
                    'title' => 'Temperature Alert',
                    'description' => 'Storage temperature maintained at 4°C',
                    'severity' => 'low'
                ]
            ];
        } else {
            $alerts = [
                [
                    'title' => 'No Active Alerts',
                    'description' => 'This farm has no current safety alerts',
                    'severity' => 'low'
                ],
                [
                    'title' => 'Last AMU Check',
                    'description' => 'Within acceptable limits (2024-06-10)',
                    'severity' => 'low'
                ]
            ];
        }
        
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
} else {
    sendResponse([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>