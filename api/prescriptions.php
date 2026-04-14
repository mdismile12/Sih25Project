<?php
// MUST be at the very top - no output before this
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Set error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Capture any errors/warnings
ob_start();

require_once 'config.php';

function writeLog($message) {
    $log_file = __DIR__ . '/logs/prescriptions.log';
    if (!is_dir(dirname($log_file))) {
        mkdir(dirname($log_file), 0777, true);
    }
    error_log("[" . date('Y-m-d H:i:s') . "] " . $message . "\n", 3, $log_file);
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    writeLog("Request: $method");

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

    // ===== GET: Fetch prescriptions =====
    if ($method === 'GET') {
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;
        $farm_id = isset($_GET['farm_id']) ? $_GET['farm_id'] : null;
        $vet_id = isset($_GET['vet_id']) ? $_GET['vet_id'] : null;
        
        $query = "SELECT * FROM prescriptions WHERE 1=1";
        $params = array();

        if ($id) {
            $query .= " AND id = ?";
            $params[] = $id;
        }
        if ($farm_id) {
            $query .= " AND farm_id = ?";
            $params[] = $farm_id;
        }
        if ($vet_id) {
            $query .= " AND vet_id = ?";
            $params[] = $vet_id;
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $pdo->prepare($query);
        if (!$stmt->execute($params)) {
            throw new Exception("Query failed: " . implode(", ", $stmt->errorInfo()));
        }

        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get medicines for each prescription
        foreach ($prescriptions as &$presc) {
            $med_stmt = $pdo->prepare("
                SELECT medicine_id, dosage_rate, body_weight, calculated_dosage, 
                       dosage_unit, frequency, duration_days, withdrawal_period_days
                FROM prescription_items WHERE prescription_id = ?
            ");
            $med_stmt->execute([$presc['id']]);
            $presc['medicines'] = $med_stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Prescriptions retrieved successfully',
            'data' => $prescriptions,
            'count' => count($prescriptions)
        ]);
    }

    // ===== POST: Create prescription =====
    elseif ($method === 'POST') {
        $farm_id = $input['farm_id'] ?? null;
        $animal_id = $input['animal_id'] ?? null;
        $animal_type = $input['animal_type'] ?? null;
        $animal_weight = floatval($input['animal_weight'] ?? 0);
        $animal_owner = $input['animal_owner'] ?? null;
        $symptoms = $input['symptoms'] ?? null;
        $diagnosis = $input['diagnosis'] ?? null;
        $vet_id = $input['vet_id'] ?? null;
        $medicines = $input['medicines'] ?? [];
        $instructions = $input['instructions'] ?? null;
        $administration_frequency = $input['administration_frequency'] ?? null;
        $administration_time = $input['administration_time'] ?? null;
        $duration_days = intval($input['duration_days'] ?? 7);
        $species = $input['species'] ?? $animal_type;
        $treatment_reason = $input['treatment_reason'] ?? $diagnosis;

        if (!$farm_id || !$animal_id || !$animal_type || !$animal_weight || !$vet_id) {
            sendError('Missing required fields: farm_id, animal_id, animal_type, animal_weight, vet_id', 400);
        }

        try {
            // Get farm details for location
            $farm_stmt = $pdo->prepare("SELECT name, location, latitude, longitude, state FROM farms WHERE farm_id COLLATE utf8mb4_unicode_ci = ? COLLATE utf8mb4_unicode_ci LIMIT 1");
            $farm_stmt->execute([$farm_id]);
            $farm = $farm_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$farm) {
                sendError('Farm not found', 404);
            }

            $farm_location = $farm['location'];
            $farm_lat = $farm['latitude'];
            $farm_lng = $farm['longitude'];
            $farm_state = $farm['state'] ?? null;

            // Generate prescription ID
            $prescription_id = 'PRESC-' . date('YmdHis') . '-' . rand(1000, 9999);

            // Insert prescription
            $prescr_stmt = $pdo->prepare("
                INSERT INTO prescriptions 
                (prescription_id, animal_id, animal_type, animal_owner, animal_weight, farm_id, farm_location, farm_lat, farm_lng, 
                 symptoms, diagnosis, instructions, administration_frequency, administration_time, duration_days, vet_id, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            if (!$prescr_stmt->execute([
                $prescription_id,
                $animal_id,
                $animal_type,
                $animal_owner,
                $animal_weight,
                $farm_id,
                $farm_location,
                $farm_lat,
                $farm_lng,
                $symptoms,
                $diagnosis,
                $instructions,
                $administration_frequency,
                $administration_time,
                $duration_days,
                $vet_id,
                'active'
            ])) {
                throw new Exception("Failed to insert prescription: " . implode(", ", $prescr_stmt->errorInfo()));
            }

            $prescription_db_id = $pdo->lastInsertId();

            // Insert medicines with calculated dosages
            $medicine_responses = [];
            foreach ($medicines as $medicine) {
                $medicine_id = $medicine['medicine_id'] ?? null;
                $dosage_rate = floatval($medicine['dosage_rate'] ?? 0);
                $frequency = $medicine['frequency'] ?? null;
                $med_duration = intval($medicine['duration_days'] ?? $duration_days);

                if (!$medicine_id || !$dosage_rate) {
                    continue;
                }

                // Calculate dosage: dosage_rate * body_weight
                $calculated_dosage = round($dosage_rate * $animal_weight, 2);

                // Get medicine meta (name, type, withdrawal_period, dosage_unit)
                $med_stmt = $pdo->prepare("SELECT name, type, withdrawal_period_days, dosage_unit FROM medicines WHERE medicine_id = ? LIMIT 1");
                $med_stmt->execute([$medicine_id]);
                $med_data = $med_stmt->fetch(PDO::FETCH_ASSOC);
                $withdrawal_period = intval($med_data['withdrawal_period_days'] ?? 0);
                $medicine_name = $med_data['name'] ?? ($medicine['medicine_name'] ?? '');
                $medicine_type = $med_data['type'] ?? '';
                $medicine_unit = $med_data['dosage_unit'] ?? ($medicine['dosage_unit'] ?? 'mg');

                // Insert prescription item
                $item_stmt = $pdo->prepare("
                    INSERT INTO prescription_items 
                    (prescription_id, medicine_id, dosage_rate, body_weight, calculated_dosage, 
                     dosage_unit, frequency, duration_days, withdrawal_period_days)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $item_stmt->execute([
                    $prescription_db_id,
                    $medicine_id,
                    $dosage_rate,
                    $animal_weight,
                    $calculated_dosage,
                    $medicine['dosage_unit'] ?? $medicine_unit,
                    $frequency,
                    $med_duration,
                    $withdrawal_period
                ]);

                // Insert AMU tracking record
                try {
                    // Classify AMU category (simple mapping)
                    $t = strtolower($medicine_type);
                    $name_l = strtolower($medicine_name);
                    $amu_category = 'VIA';
                    $vcia = ['fluoroquinolone','cephalosporin','macrolide','polymyxin','carbapenem'];
                    foreach ($vcia as $kw) { if (strpos($t,$kw)!==false || strpos($name_l,$kw)!==false) { $amu_category = 'VCIA'; break; } }
                    if ($amu_category === 'VIA') {
                        $vhia = ['tetracycline','aminoglycoside','sulfonamide','beta-lactam','penicillin'];
                        foreach ($vhia as $kw) { if (strpos($t,$kw)!==false || strpos($name_l,$kw)!==false) { $amu_category = 'VHIA'; break; } }
                    }

                    $amu_stmt = $pdo->prepare("INSERT INTO amu_records
                        (prescription_id, prescription_item_id, medicine_id, medicine_name, medicine_type, amu_category, amount, unit, farm_id, location, state, latitude, longitude, species, reason, frequency_per_month)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                    $prescription_item_id = $pdo->lastInsertId();
                    $amu_stmt->execute([
                        $prescription_db_id,
                        $prescription_item_id,
                        $medicine_id,
                        $medicine_name,
                        $medicine_type,
                        $amu_category,
                        $calculated_dosage,
                        $medicine_unit,
                        $farm_id,
                        $farm_location,
                        $farm_state,
                        $farm_lat,
                        $farm_lng,
                        $species,
                        $treatment_reason,
                        1
                    ]);
                } catch (Exception $e) {
                    // Log but don't fail prescription creation
                    writeLog('AMU insert failed: ' . $e->getMessage());
                }

                $medicine_responses[] = [
                    'medicine_id' => $medicine_id,
                    'medicine_name' => $medicine['medicine_name'] ?? '',
                    'dosage_rate' => $dosage_rate,
                    'body_weight' => $animal_weight,
                    'calculated_dosage' => $calculated_dosage,
                    'dosage_unit' => $medicine['dosage_unit'] ?? 'mg',
                    'frequency' => $frequency,
                    'duration_days' => $med_duration,
                    'withdrawal_period_days' => $withdrawal_period
                ];
            }

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Prescription created successfully',
                'data' => [
                    'id' => $prescription_db_id,
                    'prescription_id' => $prescription_id,
                    'farm_id' => $farm_id,
                    'farm_location' => $farm_location,
                    'farm_lat' => $farm_lat,
                    'farm_lng' => $farm_lng,
                    'animal_id' => $animal_id,
                    'animal_type' => $animal_type,
                    'animal_weight' => $animal_weight,
                    'symptoms' => $symptoms,
                    'diagnosis' => $diagnosis,
                    'medicines' => $medicine_responses,
                    'status' => 'active'
                ]
            ]);
        } catch (Exception $e) {
            throw new Exception("Prescription creation failed: " . $e->getMessage());
        }
    }

    // ===== PUT: Update prescription =====
    elseif ($method === 'PUT') {
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;
        if (!$id) {
            sendError('Prescription ID required for update', 400);
        }

        try {
            $updates = [];
            $params = [];

            $updateFields = ['animal_id', 'animal_type', 'animal_weight', 'symptoms', 'diagnosis', 
                           'instructions', 'administration_frequency', 'administration_time', 'duration_days', 'status'];
            
            foreach ($updateFields as $field) {
                if (isset($input[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $input[$field];
                }
            }

            if (empty($updates)) {
                sendError('No fields to update', 400);
            }

            $params[] = $id;
            $query = "UPDATE prescriptions SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $pdo->prepare($query);

            if (!$stmt->execute($params)) {
                throw new Exception("Update failed: " . implode(", ", $stmt->errorInfo()));
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Prescription updated successfully',
                'rows_affected' => $stmt->rowCount()
            ]);
        } catch (Exception $e) {
            throw new Exception("Update failed: " . $e->getMessage());
        }
    }

    // ===== DELETE: Delete prescription =====
    elseif ($method === 'DELETE') {
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;
        if (!$id) {
            sendError('Prescription ID required for deletion', 400);
        }

        try {
            // Delete prescription items first
            $pdo->prepare("DELETE FROM prescription_items WHERE prescription_id = ?")->execute([$id]);
            
            // Delete prescription
            $stmt = $pdo->prepare("DELETE FROM prescriptions WHERE id = ?");
            if (!$stmt->execute([$id])) {
                throw new Exception("Delete failed");
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Prescription deleted successfully',
                'rows_affected' => $stmt->rowCount()
            ]);
        } catch (Exception $e) {
            throw new Exception("Delete failed: " . $e->getMessage());
        }
    }

} catch (Exception $e) {
    // Clear any buffered output
    ob_clean();
    
    writeLog("Error: " . $e->getMessage());
    writeLog("Stack Trace: " . $e->getTraceAsString());
    
    // Force JSON response
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => 500,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Flush any remaining output
ob_end_flush();
?>

