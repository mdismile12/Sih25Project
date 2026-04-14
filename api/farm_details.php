<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $batchId = $_GET['batchId'] ?? '';
    
    try {
        // Return farm details based on batch
        if (strpos($batchId, 'M20240615-102') !== false) {
            $details = [
                'name' => 'Green Pastures Dairy',
                'location' => 'Maharashtra, India',
                'type' => 'Dairy Farm',
                'certification' => 'Organic Certified',
                'lastInspection' => '2024-06-10',
                'inspectionResult' => 'Passed with 98.7% score'
            ];
        } elseif (strpos($batchId, 'M20240614-205') !== false) {
            $details = [
                'name' => 'Happy Hens Poultry',
                'location' => 'Delhi, India',
                'type' => 'Poultry Farm',
                'certification' => 'FSSAI Certified',
                'lastInspection' => '2024-06-05',
                'inspectionResult' => 'Passed with 95.2% score'
            ];
        } else {
            $details = [
                'name' => 'Unknown Farm',
                'location' => 'Location not specified',
                'type' => 'Livestock Farm',
                'certification' => 'Not certified',
                'lastInspection' => 'N/A',
                'inspectionResult' => 'No inspection data'
            ];
        }
        
        sendResponse([
            'success' => true,
            'data' => $details
        ]);
    } catch (Exception $e) {
        sendResponse([
            'success' => true,
            'data' => [
                'name' => 'Sample Farm',
                'location' => 'Sample Location',
                'type' => 'Sample Type',
                'certification' => 'Sample Certification',
                'lastInspection' => '2024-01-01',
                'inspectionResult' => 'Sample Result'
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