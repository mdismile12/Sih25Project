<?php
header('Content-Type: application/json');
require_once 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Log function for debugging
function writeLog($message) {
    $log_file = __DIR__ . '/logs/lab_tests.log';
    if (!is_dir(dirname($log_file))) {
        mkdir(dirname($log_file), 0777, true);
    }
    error_log("[" . date('Y-m-d H:i:s') . "] " . $message . "\n", 3, $log_file);
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $request_uri = $_SERVER['REQUEST_URI'];
    
    writeLog("Request: $method $request_uri");

    // Parse query parameters
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    $action = isset($_GET['action']) ? $_GET['action'] : null;
    $farm_id = isset($_GET['farm_id']) ? $_GET['farm_id'] : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;

    // Get request body for POST/PUT
    $input = json_decode(file_get_contents('php://input'), true);
    
    // CORS Headers
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    
    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    // ===== GET: Fetch lab tests =====
    if ($method === 'GET') {
        // Get all lab tests or filtered
        $query = "SELECT * FROM lab_tests WHERE 1=1";
        $params = array();

        if ($id) {
            $query .= " AND id = ?";
            $params[] = $id;
        }
        if ($farm_id) {
            $query .= " AND farm_id = ?";
            $params[] = $farm_id;
        }
        if ($status) {
            $query .= " AND status = ?";
            $params[] = $status;
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $pdo->prepare($query);
        if (!$stmt->execute($params)) {
            throw new Exception("Query failed: " . implode(", ", $stmt->errorInfo()));
        }

        $lab_tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Lab tests retrieved successfully',
            'data' => $lab_tests,
            'count' => count($lab_tests)
        ]);
    }

    // ===== POST: Create lab test =====
    elseif ($method === 'POST') {
        $test_type = $input['test_type'] ?? null;
        $farm_id = $input['farm_id'] ?? null;
        $animal_id = $input['animal_id'] ?? null;
        $description = $input['description'] ?? null;
        $priority = $input['priority'] ?? 'normal';
        $vet_id = $input['vet_id'] ?? null;

        if (!$farm_id || !$test_type) {
            throw new Exception("Missing required fields: farm_id, test_type");
        }

        // Insert into lab_tests table
        $stmt = $pdo->prepare("
            INSERT INTO lab_tests 
            (farm_id, animal_id, test_type, description, priority, vet_id, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
        ");

        if (!$stmt->execute([$farm_id, $animal_id, $test_type, $description, $priority, $vet_id])) {
            throw new Exception("Failed to create lab test: " . implode(", ", $stmt->errorInfo()));
        }

        $test_id = $pdo->lastInsertId();

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Lab test created successfully',
            'id' => $test_id,
            'test_id' => $test_id
        ]);
    }

    // ===== PUT: Update lab test =====
    elseif ($method === 'PUT') {
        $id = $input['id'] ?? $id;
        $status = $input['status'] ?? null;
        $notes = $input['notes'] ?? null;
        $results = $input['results'] ?? null;

        if (!$id) {
            throw new Exception("Missing required parameter: id");
        }

        // Build dynamic update query
        $updates = [];
        $params = [];

        if ($status) {
            $updates[] = "status = ?";
            $params[] = $status;
        }
        if ($notes) {
            $updates[] = "notes = ?";
            $params[] = $notes;
        }
        if ($results) {
            $updates[] = "test_results = ?";
            $params[] = is_array($results) ? json_encode($results) : $results;
        }
        
        $updates[] = "updated_at = NOW()";

        if (count($updates) <= 1) {
            throw new Exception("No fields to update");
        }

        $params[] = $id;

        $query = "UPDATE lab_tests SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($query);

        if (!$stmt->execute($params)) {
            throw new Exception("Failed to update lab test: " . implode(", ", $stmt->errorInfo()));
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Lab test updated successfully',
            'id' => $id
        ]);
    }

    // ===== DELETE: Delete lab test =====
    elseif ($method === 'DELETE') {
        $id = $input['id'] ?? $id;

        if (!$id) {
            throw new Exception("Missing required parameter: id");
        }

        $stmt = $pdo->prepare("DELETE FROM lab_tests WHERE id = ?");

        if (!$stmt->execute([$id])) {
            throw new Exception("Failed to delete lab test: " . implode(", ", $stmt->errorInfo()));
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Lab test deleted successfully',
            'id' => $id
        ]);
    }

    else {
        throw new Exception("Method not allowed: $method");
    }

} catch (Exception $e) {
    writeLog("Error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => true
    ]);
}
?>
