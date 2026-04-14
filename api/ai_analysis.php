<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $symptoms = $input['symptoms'] ?? '';
    $animalId = $input['animalId'] ?? '';
    
    if (empty($symptoms)) {
        sendResponse([
            'success' => false,
            'message' => 'Symptoms are required'
        ]);
    }
    
    try {
        // AI Analysis logic (demo version)
        $symptomsLower = strtolower($symptoms);
        $recommendation = '';
        
        if (strpos($symptomsLower, 'fever') !== false && strpos($symptomsLower, 'cough') !== false) {
            $recommendation = "Based on symptoms (fever and cough), recommend:\n1. Oxytetracycline 10 mg/kg for 5 days\n2. Anti-inflammatory medication\n3. Monitor temperature twice daily\n4. Ensure proper hydration\n\nWithdrawal period: 7 days milk, 28 days meat";
        } elseif (strpos($symptomsLower, 'diarrhea') !== false) {
            $recommendation = "Based on symptoms (diarrhea), recommend:\n1. Electrolyte solution\n2. Probiotics for gut health\n3. Antibiotic if bacterial infection suspected\n4. Fasting for 12-24 hours\n\nWithdrawal period: 3 days milk, 7 days meat";
        } elseif (strpos($symptomsLower, 'loss of appetite') !== false) {
            $recommendation = "Based on symptoms (loss of appetite), recommend:\n1. Vitamin B complex injection\n2. Appetite stimulants\n3. Check for dental issues\n4. Deworming if needed\n\nWithdrawal period: 0 days";
        } else {
            $recommendation = "Based on symptoms, recommend:\n1. General health checkup\n2. Supportive care\n3. Monitor symptoms for 24 hours\n4. Contact if symptoms worsen\n\nStandard protocol applies";
        }
        
        sendResponse([
            'success' => true,
            'recommendation' => $recommendation,
            'suggestedDrugs' => [
                ['name' => 'Oxytetracycline', 'dosage' => '10 mg/kg', 'duration' => '5 days'],
                ['name' => 'Vitamin Complex', 'dosage' => '5 ml', 'duration' => '3 days']
            ]
        ]);
    } catch (Exception $e) {
        sendResponse([
            'success' => false,
            'message' => 'Analysis failed: ' . $e->getMessage()
        ]);
    }
} else {
    sendResponse([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>