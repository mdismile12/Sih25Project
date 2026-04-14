<?php
header('Content-Type: application/json');
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    $pdo = $GLOBALS['pdo'] ?? null;
    if (!isset($pdo)) {
        throw new Exception("Database connection not available");
    }

    // Insert symptoms
    $symptoms = [
        ['symptom_id' => 'SYM-FEVER', 'name' => 'fever', 'category' => 'Temperature', 'description' => 'High body temperature', 'severity' => 'high'],
        ['symptom_id' => 'SYM-COUGH', 'name' => 'cough', 'category' => 'Respiratory', 'description' => 'Persistent coughing', 'severity' => 'high'],
        ['symptom_id' => 'SYM-LOSS-APP', 'name' => 'loss of appetite', 'category' => 'Appetite', 'description' => 'Reduced food intake', 'severity' => 'high'],
        ['symptom_id' => 'SYM-DIARRHEA', 'name' => 'diarrhea', 'category' => 'Digestive', 'description' => 'Loose bowel movements', 'severity' => 'medium'],
        ['symptom_id' => 'SYM-LETHARGY', 'name' => 'lethargy', 'category' => 'General', 'description' => 'Lack of energy', 'severity' => 'high'],
        ['symptom_id' => 'SYM-DISCHARGE', 'name' => 'nasal discharge', 'category' => 'Respiratory', 'description' => 'Discharge from nostrils', 'severity' => 'medium'],
        ['symptom_id' => 'SYM-INFLAMMATION', 'name' => 'inflammation', 'category' => 'Joint', 'description' => 'Joint or tissue inflammation', 'severity' => 'medium'],
        ['symptom_id' => 'SYM-MASTITIS', 'name' => 'mastitis', 'category' => 'Udder', 'description' => 'Udder inflammation', 'severity' => 'high'],
        ['symptom_id' => 'SYM-WOUND', 'name' => 'wound', 'category' => 'Injury', 'description' => 'Open wound or injury', 'severity' => 'high'],
        ['symptom_id' => 'SYM-PAIN', 'name' => 'pain', 'category' => 'Pain', 'description' => 'General pain or discomfort', 'severity' => 'medium'],
    ];

    foreach ($symptoms as $symptom) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO symptoms (symptom_id, name, category, description, severity)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE name = VALUES(name)
            ");
            $stmt->execute([
                $symptom['symptom_id'],
                $symptom['name'],
                $symptom['category'],
                $symptom['description'],
                $symptom['severity']
            ]);
        } catch (Exception $e) {
            // Silently skip duplicate symptoms
        }
    }

    // Insert medicine_symptoms mappings
    $mappings = [
        // Fever treatments
        ['medicine_id' => 'MED-AMOX-001', 'symptom_id' => 'SYM-FEVER', 'effectiveness' => 0.8, 'animal_type' => 'Cattle'],
        ['medicine_id' => 'MED-AMOX-001', 'symptom_id' => 'SYM-FEVER', 'effectiveness' => 0.8, 'animal_type' => 'Buffalo'],
        ['medicine_id' => 'MED-AMOX-001', 'symptom_id' => 'SYM-FEVER', 'effectiveness' => 0.75, 'animal_type' => 'Poultry'],
        
        ['medicine_id' => 'MED-OXY-001', 'symptom_id' => 'SYM-FEVER', 'effectiveness' => 0.85, 'animal_type' => 'Cattle'],
        ['medicine_id' => 'MED-OXY-001', 'symptom_id' => 'SYM-FEVER', 'effectiveness' => 0.85, 'animal_type' => 'Buffalo'],
        ['medicine_id' => 'MED-OXY-001', 'symptom_id' => 'SYM-FEVER', 'effectiveness' => 0.8, 'animal_type' => 'Goat'],
        
        ['medicine_id' => 'MED-PAR-001', 'symptom_id' => 'SYM-FEVER', 'effectiveness' => 0.7, 'animal_type' => null],
        ['medicine_id' => 'MED-ASP-001', 'symptom_id' => 'SYM-FEVER', 'effectiveness' => 0.65, 'animal_type' => null],
        ['medicine_id' => 'MED-IBU-001', 'symptom_id' => 'SYM-FEVER', 'effectiveness' => 0.7, 'animal_type' => null],

        // Cough treatments
        ['medicine_id' => 'MED-OXY-001', 'symptom_id' => 'SYM-COUGH', 'effectiveness' => 0.8, 'animal_type' => null],
        ['medicine_id' => 'MED-ENR-001', 'symptom_id' => 'SYM-COUGH', 'effectiveness' => 0.85, 'animal_type' => 'Poultry'],
        ['medicine_id' => 'MED-AMOX-001', 'symptom_id' => 'SYM-COUGH', 'effectiveness' => 0.75, 'animal_type' => null],

        // Loss of appetite
        ['medicine_id' => 'MED-VIT-B', 'symptom_id' => 'SYM-LOSS-APP', 'effectiveness' => 0.7, 'animal_type' => null],
        ['medicine_id' => 'MED-CAL-001', 'symptom_id' => 'SYM-LOSS-APP', 'effectiveness' => 0.6, 'animal_type' => null],

        // Diarrhea treatments
        ['medicine_id' => 'MED-AMOX-001', 'symptom_id' => 'SYM-DIARRHEA', 'effectiveness' => 0.8, 'animal_type' => null],
        ['medicine_id' => 'MED-SUL-001', 'symptom_id' => 'SYM-DIARRHEA', 'effectiveness' => 0.75, 'animal_type' => null],
        ['medicine_id' => 'MED-ENR-001', 'symptom_id' => 'SYM-DIARRHEA', 'effectiveness' => 0.8, 'animal_type' => null],

        // Lethargy
        ['medicine_id' => 'MED-VIT-B', 'symptom_id' => 'SYM-LETHARGY', 'effectiveness' => 0.75, 'animal_type' => null],
        ['medicine_id' => 'MED-CAL-001', 'symptom_id' => 'SYM-LETHARGY', 'effectiveness' => 0.65, 'animal_type' => null],

        // Nasal discharge / Respiratory
        ['medicine_id' => 'MED-OXY-001', 'symptom_id' => 'SYM-DISCHARGE', 'effectiveness' => 0.8, 'animal_type' => null],
        ['medicine_id' => 'MED-ENR-001', 'symptom_id' => 'SYM-DISCHARGE', 'effectiveness' => 0.85, 'animal_type' => null],
        ['medicine_id' => 'MED-AMOX-001', 'symptom_id' => 'SYM-DISCHARGE', 'effectiveness' => 0.75, 'animal_type' => null],

        // Inflammation
        ['medicine_id' => 'MED-IBU-001', 'symptom_id' => 'SYM-INFLAMMATION', 'effectiveness' => 0.8, 'animal_type' => null],
        ['medicine_id' => 'MED-ASP-001', 'symptom_id' => 'SYM-INFLAMMATION', 'effectiveness' => 0.7, 'animal_type' => null],
        ['medicine_id' => 'MED-PAR-001', 'symptom_id' => 'SYM-INFLAMMATION', 'effectiveness' => 0.6, 'animal_type' => null],

        // Mastitis (udder infection)
        ['medicine_id' => 'MED-AMOX-001', 'symptom_id' => 'SYM-MASTITIS', 'effectiveness' => 0.85, 'animal_type' => 'Cattle'],
        ['medicine_id' => 'MED-AMOX-001', 'symptom_id' => 'SYM-MASTITIS', 'effectiveness' => 0.85, 'animal_type' => 'Buffalo'],
        ['medicine_id' => 'MED-OXY-001', 'symptom_id' => 'SYM-MASTITIS', 'effectiveness' => 0.8, 'animal_type' => 'Cattle'],
        ['medicine_id' => 'MED-ENR-001', 'symptom_id' => 'SYM-MASTITIS', 'effectiveness' => 0.85, 'animal_type' => 'Cattle'],

        // Wounds
        ['medicine_id' => 'MED-AMOX-001', 'symptom_id' => 'SYM-WOUND', 'effectiveness' => 0.8, 'animal_type' => null],
        ['medicine_id' => 'MED-CEP-001', 'symptom_id' => 'SYM-WOUND', 'effectiveness' => 0.85, 'animal_type' => null],

        // Pain
        ['medicine_id' => 'MED-PAR-001', 'symptom_id' => 'SYM-PAIN', 'effectiveness' => 0.7, 'animal_type' => null],
        ['medicine_id' => 'MED-IBU-001', 'symptom_id' => 'SYM-PAIN', 'effectiveness' => 0.8, 'animal_type' => null],
        ['medicine_id' => 'MED-ASP-001', 'symptom_id' => 'SYM-PAIN', 'effectiveness' => 0.65, 'animal_type' => null],
    ];

    foreach ($mappings as $mapping) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO medicine_symptoms (medicine_id, symptom_id, effectiveness, animal_type)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE effectiveness = VALUES(effectiveness)
            ");
            $stmt->execute([
                $mapping['medicine_id'],
                $mapping['symptom_id'],
                $mapping['effectiveness'],
                $mapping['animal_type']
            ]);
        } catch (Exception $e) {
            // Silently skip duplicates
        }
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Symptoms and medicine mappings inserted successfully',
        'symptoms_inserted' => count($symptoms),
        'mappings_inserted' => count($mappings)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
