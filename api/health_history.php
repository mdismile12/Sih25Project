<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$request_data = getRequestData();

if ($method == 'GET') {
    $animalId = $request_data['animalId'] ?? $request_data['animal_id'] ?? '';
    
    try {
        if (!empty($animalId)) {
            $stmt = $pdo->prepare("SELECT * FROM health_history WHERE animal_id = ? ORDER BY created_at DESC");
            $stmt->execute([$animalId]);
            $records = $stmt->fetchAll();
            
            // Format response
            $formattedRecords = [];
            foreach ($records as $record) {
                $formattedRecords[] = [
                    'date' => $record['treatment_date'],
                    'treatment' => $record['treatment_description'],
                    'vet' => 'Dr. ' . ($record['vet_id'] ?? 'Unknown'),
                    'notes' => $record['treatment_description']
                ];
            }
            
            sendResponse([
                'success' => true,
                'data' => $formattedRecords
            ]);
        } else {
            // Return demo data
            sendResponse([
                'success' => true,
                'data' => [
                    [
                        'date' => '2024-06-15',
                        'treatment' => 'Oxytetracycline, Vitamin Complex',
                        'vet' => 'Dr. VET001',
                        'notes' => 'Bacterial infection treatment'
                    ],
                    [
                        'date' => '2024-05-20',
                        'treatment' => 'Ivermectin',
                        'vet' => 'Dr. VET001',
                        'notes' => 'Parasite control'
                    ]
                ]
            ]);
        }
    } catch (Exception $e) {
        sendError('Error fetching health history: ' . $e->getMessage(), 500);
    }
} else {
    sendError('Invalid request method', 405);
}
?>