<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$request_data = getRequestData();

if ($method == 'POST') {
    $govId = $request_data['govId'] ?? '';
    $password = $request_data['password'] ?? '';
    $tier = $request_data['tier'] ?? '';
    $region = $request_data['region'] ?? '';
    
    if (empty($govId) || empty($password)) {
        sendError('Government ID and password required', 400);
    }
    
    try {
        // Demo credentials for testing (always available as fallback)
        $demoUsers = [
            'GOV001' => ['password' => 'demo', 'tier' => 'national', 'name' => 'Ramesh Sharma', 'department' => 'FSSAI'],
            'GOV002' => ['password' => 'demo', 'tier' => 'state', 'name' => 'Sanjana Desai', 'department' => 'State Livestock'],
            'GOV003' => ['password' => 'demo', 'tier' => 'district', 'name' => 'Vikram Kulkarni', 'department' => 'District Agricultural']
        ];
        
        // Try database first if table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'government_users'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->prepare("SELECT * FROM government_users WHERE gov_id = ? LIMIT 1");
            $stmt->execute([$govId]);
            $user = $stmt->fetch();
            
            if ($user && ($user['password'] === md5($password) || $user['password'] === $password)) {
                unset($user['password']);
                sendResponse([
                    'success' => true,
                    'user' => $user,
                    'message' => 'Login successful'
                ]);
            } else if ($user) {
                // User found but password doesn't match
                sendError('Invalid password', 401);
            } else {
                // User not found in database - try demo credentials
                if (isset($demoUsers[$govId]) && $demoUsers[$govId]['password'] === $password) {
                    sendResponse([
                        'success' => true,
                        'user' => [
                            'gov_id' => $govId,
                            'name' => $demoUsers[$govId]['name'],
                            'tier' => $demoUsers[$govId]['tier'],
                            'region' => $region ?: 'All',
                            'department' => $demoUsers[$govId]['department'],
                            'status' => 'active'
                        ],
                        'message' => 'Demo login successful'
                    ]);
                } else {
                    sendError('Invalid credentials. Try GOV001, GOV002, or GOV003 with password: demo', 401);
                }
            }
        } else {
            // Table doesn't exist - use demo credentials only
            if (isset($demoUsers[$govId]) && $demoUsers[$govId]['password'] === $password) {
                sendResponse([
                    'success' => true,
                    'user' => [
                        'gov_id' => $govId,
                        'name' => $demoUsers[$govId]['name'],
                        'tier' => $demoUsers[$govId]['tier'],
                        'region' => $region ?: 'All',
                        'department' => $demoUsers[$govId]['department'],
                        'status' => 'active'
                    ],
                    'message' => 'Demo login successful'
                ]);
            } else {
                sendError('Demo users: GOV001, GOV002, GOV003 (password: demo)', 401);
            }
        }
    } catch (Exception $e) {
        sendError('Login error: ' . $e->getMessage(), 500);
    }
} else {
    sendError('Invalid request method. Only POST allowed', 405);
}
?>