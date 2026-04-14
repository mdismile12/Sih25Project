<?php
// Simulate POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';

// Put JSON data in php://input
$data = '{"vetId":"VET001","password":"demo"}';
$f = fopen('php://memory', 'w+');
fwrite($f, $data);
rewind($f);

// Now include the actual vet_login.php
$_ENV['test_mode'] = true;

// Override file_get_contents to return our test data
if (!function_exists('file_get_contents_original')) {
    function getTestData() {
        return '{"vetId":"VET001","password":"demo"}';
    }
}

// Test directly
require 'api/config.php';

$request_data = getRequestData();
echo "Request data: " . json_encode($request_data) . "\n";

$vetId = $request_data['vetId'] ?? '';
$password = $request_data['password'] ?? '';

echo "Vet ID from request: '$vetId'\n";
echo "Password from request: '$password'\n";

if ($vetId === 'VET001' && $password === 'demo') {
    echo "\n✅ LOGIN WOULD SUCCEED\n";
} else {
    echo "\n❌ LOGIN WOULD FAIL\n";
    echo "Condition 1 (VET001): " . ($vetId === 'VET001' ? 'PASS' : 'FAIL') . "\n";
    echo "Condition 2 (demo): " . ($password === 'demo' ? 'PASS' : 'FAIL') . "\n";
}
?>
