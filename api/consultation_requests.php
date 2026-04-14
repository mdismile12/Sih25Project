<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        // Return demo consultation requests
        $requests = [
            [
                'farmer' => 'Ramesh Patel',
                'animal' => 'Cow-102',
                'issue' => 'Loss of appetite',
                'time' => '30 mins ago',
                'phone' => '+91 9876543210'
            ],
            [
                'farmer' => 'Suresh Kumar',
                'animal' => 'Poultry-205',
                'issue' => 'Vaccination query',
                'time' => '1 hour ago',
                'phone' => '+91 8765432109'
            ],
            [
                'farmer' => 'Priya Sharma',
                'animal' => 'Goat-301',
                'issue' => 'Digestive issues',
                'time' => '2 hours ago',
                'phone' => '+91 7654321098'
            ]
        ];
        
        sendResponse([
            'success' => true,
            'data' => $requests
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