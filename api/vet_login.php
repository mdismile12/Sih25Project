<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$request_data = getRequestData();

if ($method == 'POST') {
    $vetId = $request_data['vetId'] ?? '';
    $password = $request_data['password'] ?? '';
    
    if (empty($vetId) || empty($password)) {
        sendError('Vet ID and password are required', 400);
    }
    
    try {
        // Check if table exists first
        $stmt = $pdo->query("SHOW TABLES LIKE 'veterinarians'");
        if ($stmt->rowCount() > 0) {
            // Try to query the database
            $stmt = $pdo->prepare("SELECT * FROM veterinarians WHERE vet_id = ? LIMIT 1");
            $stmt->execute([$vetId]);
            $vet = $stmt->fetch();
            
            if ($vet) {
                // Verify password (in production, use password_verify with bcrypt)
                if ($vet['password'] === md5($password) || $vet['password'] === $password) {
                    // Remove sensitive data
                    unset($vet['password']);
                    
                    sendResponse([
                        'success' => true,
                        'user' => $vet,
                        'message' => 'Login successful'
                    ]);
                } else {
                    sendError('Invalid credentials', 401);
                }
            } else {
                // Table exists but user not found - try demo credentials as fallback
                if ($vetId === 'VET001' && ($password === 'demo' || $password === md5('demo'))) {
                    sendResponse([
                        'success' => true,
                        'user' => [
                            'id' => 1,
                            'vet_id' => 'VET001',
                            'name' => 'Dr. Rajesh Kumar',
                            'email' => 'rajesh@agrisense.com',
                            'specialization' => 'Dairy Cattle',
                            'experience' => 12,
                            'status' => 'active'
                        ],
                        'message' => 'Demo login successful'
                    ]);
                } else if ($vetId === 'VET002' && ($password === 'demo' || $password === md5('demo'))) {
                    sendResponse([
                        'success' => true,
                        'user' => [
                            'id' => 2,
                            'vet_id' => 'VET002',
                            'name' => 'Dr. Priya Singh',
                            'email' => 'priya@agrisense.com',
                            'specialization' => 'Poultry',
                            'experience' => 8,
                            'status' => 'active'
                        ],
                        'message' => 'Demo login successful'
                    ]);
                } else if ($vetId === 'VET003' && ($password === 'demo' || $password === md5('demo'))) {
                    sendResponse([
                        'success' => true,
                        'user' => [
                            'id' => 3,
                            'vet_id' => 'VET003',
                            'name' => 'Dr. Amit Patel',
                            'email' => 'amit@agrisense.com',
                            'specialization' => 'Small Ruminants',
                            'experience' => 5,
                            'status' => 'active'
                        ],
                        'message' => 'Demo login successful'
                    ]);
                } else {
                    sendError('Invalid credentials. Try VET001, VET002, or VET003 with password: demo', 401);
                }
            }
        } else {
            // Demo mode - table doesn't exist yet
            // Accept demo credentials
            if ($vetId === 'VET001' && ($password === 'demo' || $password === md5('demo'))) {
                sendResponse([
                    'success' => true,
                    'user' => [
                        'id' => 1,
                        'vet_id' => 'VET001',
                        'name' => 'Dr. Rajesh Kumar',
                        'email' => 'rajesh@agrisense.com',
                        'specialization' => 'Dairy Cattle',
                        'experience' => 12,
                        'status' => 'active'
                    ],
                    'message' => 'Demo login successful'
                ]);
            } else if ($vetId === 'VET002' && ($password === 'demo' || $password === md5('demo'))) {
                sendResponse([
                    'success' => true,
                    'user' => [
                        'id' => 2,
                        'vet_id' => 'VET002',
                        'name' => 'Dr. Priya Singh',
                        'email' => 'priya@agrisense.com',
                        'specialization' => 'Poultry',
                        'experience' => 8,
                        'status' => 'active'
                    ],
                    'message' => 'Demo login successful'
                ]);
            } else if ($vetId === 'VET003' && ($password === 'demo' || $password === md5('demo'))) {
                sendResponse([
                    'success' => true,
                    'user' => [
                        'id' => 3,
                        'vet_id' => 'VET003',
                        'name' => 'Dr. Amit Patel',
                        'email' => 'amit@agrisense.com',
                        'specialization' => 'Small Ruminants',
                        'experience' => 5,
                        'status' => 'active'
                    ],
                    'message' => 'Demo login successful'
                ]);
            } else {
                sendError('Invalid demo credentials. Try VET001, VET002, or VET003 with password: demo', 401);
            }
        }
    } catch (Exception $e) {
        sendError('Login error: ' . $e->getMessage(), 500);
    }
} else {
    sendError('Invalid request method. Only POST allowed', 405);
}
?>
