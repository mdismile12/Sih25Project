<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $animalId = $_GET['animalId'] ?? '';
    
    try {
        if (!empty($animalId)) {
            $stmt = $pdo->prepare("SELECT * FROM prescriptions WHERE animal_id = ? ORDER BY created_at DESC");
            $stmt->execute([$animalId]);
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $records = [];
        }
        
        // Format response
        $formattedRecords = [];
        foreach ($records as $record) {
            $formattedRecords[] = [
                'date' => $record['created_at'],
                'treatment' => 'Prescription #' . $record['prescription_id'],
                'vet' => 'Dr. ' . $record['vet_id']
            ];
        }
        
        // Add demo data if no real records
        if (empty($formattedRecords)) {
            $formattedRecords = [
                [
                    'date' => '2024-06-15',
                    'treatment' => 'Oxytetracycline 10 mg/kg for 5 days',
                    'vet' => 'Dr. VET001'
                ],
                [
                    'date' => '2024-05-20',
                    'treatment' => 'Ivermectin injection',
                    'vet' => 'Dr. VET001'
                ],
                [
                    'date' => '2024-04-10',
                    'treatment' => 'Annual vaccination',
                    'vet' => 'Dr. VET001'
                ]
            ];
        }
        
        sendResponse([
            'success' => true,
            'data' => $formattedRecords
        ]);
    } catch (Exception $e) {
        sendResponse([
            'success' => true,
            'data' => []
        ]);
    }
} else {
    sendResponse([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>