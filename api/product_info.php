<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $productId = $_GET['productId'] ?? '';
    $farmId = $_GET['farmId'] ?? '';
    
    try {
        // Return product information based on ID
        if ($productId == 'M20240615-102' || $farmId == 'FARM-102') {
            $productInfo = [
                'product' => 'Organic A2 Cow Milk',
                'farmRating' => '98.7%',
                'residue' => 'Confirmed',
                'withdrawal' => 'No violations',
                'location' => 'Maharashtra, India',
                'batch' => 'M20240615-102',
                'farmName' => 'Green Pastures Dairy',
                'certification' => 'Organic Certified',
                'lastInspection' => '2024-06-10',
                'inspectionResult' => 'Passed'
            ];
        } elseif ($productId == 'M20240614-205' || $farmId == 'FARM-205') {
            $productInfo = [
                'product' => 'Free Range Eggs',
                'farmRating' => '95.2%',
                'residue' => 'Confirmed',
                'withdrawal' => 'No violations',
                'location' => 'Delhi, India',
                'batch' => 'M20240614-205',
                'farmName' => 'Happy Hens Poultry',
                'certification' => 'FSSAI Certified',
                'lastInspection' => '2024-06-05',
                'inspectionResult' => 'Passed'
            ];
        } else {
            $productInfo = [
                'product' => 'Farm Fresh Product',
                'farmRating' => '92.5%',
                'residue' => 'Pending verification',
                'withdrawal' => 'No data',
                'location' => 'India',
                'batch' => $productId ?: 'Unknown',
                'farmName' => 'Unknown Farm',
                'certification' => 'Not specified',
                'lastInspection' => 'N/A',
                'inspectionResult' => 'Pending'
            ];
        }
        
        sendResponse([
            'success' => true,
            'data' => $productInfo
        ]);
    } catch (Exception $e) {
        sendResponse([
            'success' => false,
            'message' => 'Failed to fetch product information'
        ]);
    }
} else {
    sendResponse([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>