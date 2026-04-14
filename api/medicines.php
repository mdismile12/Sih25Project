<?php
header('Content-Type: application/json');
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    // CORS Headers
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    
    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    // ===== GET: Fetch medicines or suggest for symptoms =====
    if ($method === 'GET') {
        $symptoms = isset($_GET['symptoms']) ? explode(',', $_GET['symptoms']) : [];
        $animal_type = isset($_GET['animal_type']) ? $_GET['animal_type'] : null;
        
        if (!empty($symptoms) && $animal_type) {
            // Suggest medicines based on symptoms and animal type
            $placeholders = implode(',', array_fill(0, count($symptoms), '?'));
            $query = "
                SELECT DISTINCT 
                    m.medicine_id,
                    m.name as medicine_name,
                    m.generic_name,
                    m.type,
                    m.dosage_rate,
                    m.dosage_unit,
                    m.mrl_limit,
                    m.withdrawal_period_days,
                    SUM(ms.effectiveness) as total_effectiveness
                FROM medicines m
                LEFT JOIN medicine_symptoms ms ON m.medicine_id = ms.medicine_id
                LEFT JOIN symptoms s ON s.symptom_id = ms.symptom_id
                WHERE (s.symptom_id IN ($placeholders) OR s.name IN ($placeholders))
                AND (ms.animal_type = ? OR ms.animal_type IS NULL)
                AND m.approved = TRUE
                GROUP BY m.medicine_id
                ORDER BY total_effectiveness DESC, m.name ASC
            ";
            
            $params = array_merge($symptoms, $symptoms, [$animal_type]);
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            
        } else {
            // Get all medicines
            $query = "SELECT * FROM medicines WHERE approved = TRUE ORDER BY type, name ASC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
        }

        $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        // Return as array directly for compatibility with frontend
        echo json_encode($medicines);
    }

    // ===== POST: Create new medicine =====
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $name = $input['name'] ?? null;
        $generic_name = $input['generic_name'] ?? null;
        $type = $input['type'] ?? null;
        $dosage_rate = floatval($input['dosage_rate'] ?? 0);
        $dosage_unit = $input['dosage_unit'] ?? 'mg';
        $mrl_limit = floatval($input['mrl_limit'] ?? 0);
        $withdrawal_period_days = intval($input['withdrawal_period_days'] ?? 0);
        
        if (!$name || !$type || !$dosage_rate) {
            sendError('Missing required fields: name, type, dosage_rate', 400);
        }

        try {
            $medicine_id = 'MED-' . date('YmdHis') . '-' . rand(100, 999);
            
            $stmt = $pdo->prepare("
                INSERT INTO medicines (medicine_id, name, generic_name, type, dosage_rate, dosage_unit, mrl_limit, withdrawal_period_days, approved)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, TRUE)
            ");

            if (!$stmt->execute([$medicine_id, $name, $generic_name, $type, $dosage_rate, $dosage_unit, $mrl_limit, $withdrawal_period_days])) {
                throw new Exception("Failed to create medicine: " . implode(", ", $stmt->errorInfo()));
            }

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Medicine created successfully',
                'data' => [
                    'medicine_id' => $medicine_id,
                    'name' => $name,
                    'type' => $type,
                    'dosage_rate' => $dosage_rate,
                    'dosage_unit' => $dosage_unit,
                    'withdrawal_period_days' => $withdrawal_period_days
                ]
            ]);
        } catch (Exception $e) {
            throw new Exception("Medicine creation failed: " . $e->getMessage());
        }
    }

} catch (Exception $e) {
    sendError($e->getMessage(), 500);
}

?>
