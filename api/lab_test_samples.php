<?php
header('Content-Type: application/json');
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);

function writeLog($message) {
    $log_file = __DIR__ . '/logs/lab_samples.log';
    if (!is_dir(dirname($log_file))) {
        mkdir(dirname($log_file), 0777, true);
    }
    error_log("[" . date('Y-m-d H:i:s') . "] " . $message . "\n", 3, $log_file);
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    $test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : null;
    $sample_id = isset($_GET['sample_id']) ? $_GET['sample_id'] : null;

    $input = json_decode(file_get_contents('php://input'), true);
    
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    
    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    // ===== GET: Fetch lab test samples =====
    if ($method === 'GET') {
        $query = "SELECT * FROM lab_test_samples WHERE 1=1";
        $params = array();

        if ($id) {
            $query .= " AND id = ?";
            $params[] = $id;
        }
        if ($test_id) {
            $query .= " AND lab_test_id = ?";
            $params[] = $test_id;
        }
        if ($sample_id) {
            $query .= " AND sample_id = ?";
            $params[] = $sample_id;
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $pdo->prepare($query);
        if (!$stmt->execute($params)) {
            throw new Exception("Query failed: " . implode(", ", $stmt->errorInfo()));
        }

        $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $samples,
            'count' => count($samples)
        ]);
    }

    // ===== POST: Create sample collection =====
    elseif ($method === 'POST') {
        $test_id = $input['lab_test_id'] ?? null;
        $sample_type = $input['sample_type'] ?? null;
        $collection_date = $input['collection_date'] ?? date('Y-m-d');
        $collector_name = $input['collector_name'] ?? null;
        $quantity = $input['quantity'] ?? null;
        $unit = $input['unit'] ?? 'ml';

        if (!$test_id || !$sample_type) {
            throw new Exception("Missing required fields: lab_test_id, sample_type");
        }

        // Generate sample ID
        $sample_id = 'SAMPLE-' . date('YmdHis') . '-' . rand(1000, 9999);

        $stmt = $pdo->prepare("
            INSERT INTO lab_test_samples 
            (lab_test_id, sample_id, sample_type, collection_date, collector_name, quantity, unit, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'collected', NOW())
        ");

        if (!$stmt->execute([$test_id, $sample_id, $sample_type, $collection_date, $collector_name, $quantity, $unit])) {
            throw new Exception("Failed to create sample: " . implode(", ", $stmt->errorInfo()));
        }

        $sample_db_id = $pdo->lastInsertId();

        // Update lab test status
        $update_stmt = $pdo->prepare("UPDATE lab_tests SET status = 'sample_collected' WHERE id = ?");
        $update_stmt->execute([$test_id]);

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Sample collected successfully',
            'sample_id' => $sample_id,
            'db_id' => $sample_db_id
        ]);
    }

    // ===== PUT: Update sample status =====
    elseif ($method === 'PUT') {
        $id = $input['id'] ?? $id;
        $status = $input['status'] ?? null;
        $notes = $input['notes'] ?? null;

        if (!$id) {
            throw new Exception("Missing required parameter: id");
        }

        $updates = [];
        $params = [];
        $types = '';

        if ($status) {
            $updates[] = "status = ?";
            $params[] = $status;
            $types .= 's';
        }
        if ($notes) {
            $updates[] = "notes = ?";
            $params[] = $notes;
            $types .= 's';
        }

        $updates[] = "updated_at = NOW()";
        $params[] = $id;
        $types .= 'i';

        $query = "UPDATE lab_test_samples SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($query);

        if (!$stmt->execute($params)) {
            throw new Exception("Failed to update sample: " . implode(", ", $stmt->errorInfo()));
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Sample updated successfully'
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
        'message' => $e->getMessage()
    ]);
}
?>
