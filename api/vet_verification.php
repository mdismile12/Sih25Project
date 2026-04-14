<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        // Get pending vets
        $stmt = $pdo->prepare("SELECT * FROM veterinarians WHERE status = 'pending'");
        $stmt->execute();
        $pendingVets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get verified vets
        $stmt = $pdo->prepare("SELECT * FROM veterinarians WHERE status = 'verified'");
        $stmt->execute();
        $verifiedVets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        sendResponse([
            'success' => true,
            'pendingVets' => $pendingVets,
            'verifiedVets' => $verifiedVets
        ]);
    } catch (Exception $e) {
        sendResponse([
            'success' => true,
            'pendingVets' => [],
            'verifiedVets' => []
        ]);
    }
} else {
    sendResponse([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>