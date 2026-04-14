<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$request_data = getRequestData();

if ($method == 'POST') {
    $name = $request_data['name'] ?? '';
    $email = $request_data['email'] ?? '';
    $phone = $request_data['phone'] ?? '';
    $license = $request_data['license'] ?? '';
    $specialization = $request_data['specialization'] ?? '';
    $experience = $request_data['experience'] ?? 0;
    $password = $request_data['password'] ?? '';
    $confirmPassword = $request_data['confirmPassword'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($license) || empty($password)) {
        sendError('All required fields must be filled', 400);
    }
    
    if ($password !== $confirmPassword) {
        sendError('Passwords do not match', 400);
    }
    
    if (strlen($password) < 6) {
        sendError('Password must be at least 6 characters', 400);
    }
    
    try {
        $vetId = 'VET' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $hashedPassword = md5($password); // Use proper bcrypt in production
        
        $stmt = $pdo->prepare("
            INSERT INTO veterinarians (vet_id, name, email, phone, license_number, specialization, experience, password, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')
        ");
        
        $result = $stmt->execute([
            $vetId,
            $name,
            $email,
            $phone,
            $license,
            $specialization,
            $experience,
            $hashedPassword
        ]);
        
        if ($result) {
            sendResponse([
                'success' => true,
                'message' => 'Registration successful. You can now login.',
                'data' => [
                    'vet_id' => $vetId,
                    'name' => $name,
                    'status' => 'active'
                ]
            ]);
        } else {
            sendError('Registration failed', 500);
        }
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false) {
            sendError('Email or license number already registered', 409);
        }
        sendError('Registration error: ' . $e->getMessage(), 500);
    }
} else {
    sendError('Invalid request method. Only POST allowed', 405);
}
?>