<?php
require_once 'config.php';

// Generic API endpoint handler
$method = $_SERVER['REQUEST_METHOD'];
$request_data = getRequestData();

try {
    if ($method === 'GET') {
        // Return sample data
        sendResponse([
            'success' => true,
            'data' => [],
            'message' => 'No data available'
        ]);
    } elseif ($method === 'POST') {
        sendResponse([
            'success' => true,
            'message' => 'Resource created',
            'id' => rand(1000, 9999)
        ]);
    } elseif ($method === 'PUT' || $method === 'PATCH') {
        sendResponse([
            'success' => true,
            'message' => 'Resource updated'
        ]);
    } elseif ($method === 'DELETE') {
        sendResponse([
            'success' => true,
            'message' => 'Resource deleted'
        ]);
    } else {
        sendError('Invalid request method', 405);
    }
} catch (Exception $e) {
    sendError('Error: ' . $e->getMessage(), 500);
}
?>
