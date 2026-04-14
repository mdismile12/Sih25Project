<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$request_data = getRequestData();

if ($method == 'POST') {
    $currentPassword = $request_data['currentPassword'] ?? $request_data['current_password'] ?? '';
    $newPassword = $request_data['newPassword'] ?? $request_data['new_password'] ?? '';
    $confirmPassword = $request_data['confirmNewPassword'] ?? $request_data['confirm_password'] ?? '';
    $vetId = $request_data['vetId'] ?? $request_data['vet_id'] ?? '';
    
    if (empty($vetId) || empty($currentPassword) || empty($newPassword)) {
        sendError('All fields required', 400);
    }
    
    if ($newPassword !== $confirmPassword) {
        sendError('New passwords do not match', 400);
    }
    
    if (strlen($newPassword) < 6) {
        sendError('Password must be at least 6 characters', 400);
    }
    
    try {
        // Get current password
        $stmt = $pdo->prepare("SELECT password FROM veterinarians WHERE vet_id = ?");
        $stmt->execute([$vetId]);
        $vet = $stmt->fetch();
        
        if (!$vet) {
            sendError('Veterinarian not found', 404);
        }
        
        // Verify current password
        if ($vet['password'] !== md5($currentPassword) && $vet['password'] !== $currentPassword) {
            sendError('Current password is incorrect', 401);
        }
        
        // Update password
        $newHashedPassword = md5($newPassword);
        $stmt = $pdo->prepare("UPDATE veterinarians SET password = ? WHERE vet_id = ?");
        $stmt->execute([$newHashedPassword, $vetId]);
        
        sendResponse([
            'success' => true,
            'message' => 'Password reset successfully'
        ]);
    } catch (Exception $e) {
        sendError('Error resetting password: ' . $e->getMessage(), 500);
    }
} else {
    sendError('Invalid request method. Only POST allowed', 405);
}
?>
?>