<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    echo "Starting prescriptions test...\n";
    
    require_once 'config.php';
    echo "Config loaded...\n";
    
    $method = $_SERVER['REQUEST_METHOD'];
    echo "Method: $method\n";
    
    if ($method === 'GET') {
        $query = "SELECT * FROM prescriptions";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'message' => 'Prescriptions retrieved',
            'count' => count($prescriptions),
            'data' => $prescriptions
        ]);
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
?>
