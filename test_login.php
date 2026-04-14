<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';
$input = '{"vetId":"VET001","password":"demo"}';

// Simulate getRequestData from config.php
function getRequestData() {
    $input = file_get_contents('php://input');
    if (empty($input)) {
        global $argv;
        $input = '{"vetId":"VET001","password":"demo"}';
    }
    return json_decode($input, true) ?? [];
}

function sendResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode(array_merge($data, ['timestamp' => date('Y-m-d H:i:s')]));
    exit;
}

function sendError($message, $status = 400) {
    sendResponse(['success' => false, 'message' => $message], $status);
}

// Test the login logic
$request_data = getRequestData();
$vetId = $request_data['vetId'] ?? '';
$password = $request_data['password'] ?? '';

echo "VetID: $vetId\n";
echo "Password: $password\n";
echo "Match VET001: " . ($vetId === 'VET001' ? 'YES' : 'NO') . "\n";
echo "Match demo: " . ($password === 'demo' ? 'YES' : 'NO') . "\n";

if ($vetId === 'VET001' && $password === 'demo') {
    echo "SUCCESS - Demo login triggered\n";
    $user = [
        'id' => 1,
        'vet_id' => 'VET001',
        'name' => 'Dr. Sharma',
        'email' => 'dr.sharma@agrisense.local',
        'specialization' => 'Livestock Specialist',
        'experience' => 8,
        'status' => 'active'
    ];
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo "FAIL - Did not match\n";
}
?>
