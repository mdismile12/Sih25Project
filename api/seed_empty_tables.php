<?php
// Database seeding script - Populate empty tables with sample data
header('Content-Type: application/json');
require_once 'config.php';

try {
    $pdo->beginTransaction();
    
    // 1. Insert sample data into ai_analysis table (10 records)
    $ai_stmt = $pdo->prepare("
        INSERT INTO ai_analysis 
        (animal_id, symptoms, recommendation, confidence_score) 
        VALUES (?, ?, ?, ?)
    ");
    
    $ai_data = [
        ['COW-101', 'Fever, loss of appetite', 'Administer Amoxicillin, monitor temperature', 0.92],
        ['BUF-201', 'Cough, nasal discharge', 'Enrofloxacin recommended, isolate animal', 0.88],
        ['GOAT-301', 'Lethargy, reduced milk yield', 'Check for viral infection, supportive care', 0.85],
        ['POULTRY-401', 'Abnormal behavior, weight loss', 'Bacterial infection suspected, culture needed', 0.79],
        ['SHEEP-501', 'Skin lesions, irritation', 'Topical antibiotic application, monitor closely', 0.81],
        ['COW-102', 'Swollen udder, elevated temperature', 'Mastitis confirmed, antibiotic course needed', 0.94],
        ['BUF-202', 'Diarrhea, dehydration', 'Oral rehydration + Tetracycline therapy', 0.83],
        ['GOAT-302', 'Joint swelling, lameness', 'Arthritis suspected, pain management needed', 0.77],
        ['POULTRY-402', 'Respiratory distress, coughing', 'Respiratory infection, improve ventilation', 0.86],
        ['PIG-601', 'Skin infection, pustules', 'Staphylococcal infection, antibiotic needed', 0.80],
    ];
    
    foreach ($ai_data as [$animal_id, $symptoms, $recommendation, $confidence]) {
        $ai_stmt->execute([$animal_id, $symptoms, $recommendation, $confidence]);
    }
    
    // 2. Insert sample data into lab_tests table FIRST (10 records)
    $lab_test_stmt = $pdo->prepare("
        INSERT INTO lab_tests 
        (farm_id, test_type, animal_type, status, created_by) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $lab_test_data = [
        ['FARM-001', 'MRL', 'Cattle', 'completed', 'VET001'],
        ['FARM-002', 'Pathogen', 'Buffalo', 'completed', 'VET002'],
        ['FARM-003', 'Residue', 'Goat', 'pending', 'VET003'],
        ['FARM-004', 'Antibiotic Sensitivity', 'Poultry', 'completed', 'VET001'],
        ['FARM-005', 'MRL', 'Sheep', 'completed', 'VET004'],
        ['FARM-001', 'Pathogen', 'Cattle', 'completed', 'VET002'],
        ['FARM-002', 'Residue', 'Buffalo', 'in_progress', 'VET003'],
        ['FARM-003', 'MRL', 'Goat', 'in_progress', 'VET001'],
        ['FARM-004', 'Antibiotic Sensitivity', 'Poultry', 'completed', 'VET004'],
        ['FARM-005', 'Pathogen', 'Pig', 'completed', 'VET002'],
    ];
    
    foreach ($lab_test_data as [$farm_id, $test_type, $animal_type, $status, $created_by]) {
        $lab_test_stmt->execute([$farm_id, $test_type, $animal_type, $status, $created_by]);
        $lab_test_ids[] = $pdo->lastInsertId();
    }
    
    // 3. Insert sample data into lab_test_samples table (10 records)
    $sample_stmt = $pdo->prepare("
        INSERT INTO lab_test_samples 
        (lab_test_id, sample_type, sample_id, collection_date, collected_by, status) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $sample_data = [
        ['Milk', 'S-2025-001', '2025-12-09', 'Technician A', 'pending'],
        ['Blood', 'S-2025-002', '2025-12-08', 'Technician B', 'analyzed'],
        ['Tissue', 'S-2025-003', '2025-12-07', 'Technician C', 'pending'],
        ['Feces', 'S-2025-004', '2025-12-09', 'Technician A', 'analyzed'],
        ['Urine', 'S-2025-005', '2025-12-06', 'Technician D', 'analyzed'],
        ['Milk', 'S-2025-006', '2025-12-05', 'Technician B', 'analyzed'],
        ['Blood', 'S-2025-007', '2025-12-04', 'Technician C', 'in_transit'],
        ['Tissue', 'S-2025-008', '2025-12-09', 'Technician A', 'pending'],
        ['Feces', 'S-2025-009', '2025-12-03', 'Technician D', 'analyzed'],
        ['Serum', 'S-2025-010', '2025-12-02', 'Technician B', 'analyzed'],
    ];
    
    for ($i = 0; $i < count($sample_data); $i++) {
        $lab_id = ($i < count($lab_test_ids)) ? $lab_test_ids[$i] : ($i + 1);
        list($type, $sample_id, $date, $collected_by, $status) = $sample_data[$i];
        $sample_stmt->execute([$lab_id, $type, $sample_id, $date, $collected_by, $status]);
    }
    
    // 4. Insert sample data into lab_test_reports table (10 records)
    $report_stmt = $pdo->prepare("
        INSERT INTO lab_test_reports 
        (lab_test_id, farm_id, test_parameters, result_status, result_data, mrl_status, report_date, generated_by, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $report_data = [
        ['FARM-001', json_encode(['type' => 'Antibiotic residues', 'items' => ['Amoxicillin', 'Tetracycline']]), 'completed', json_encode(['Amoxicillin' => '2 ppb', 'Tetracycline' => '5 ppb']), 'Pass', '2025-12-09', 'Dr. Sharma', 'completed'],
        ['FARM-002', json_encode(['type' => 'Bacterial culture', 'items' => ['E. coli']]), 'completed', json_encode(['organism' => 'E. coli', 'sensitivity' => 'Enrofloxacin']), 'Alert', '2025-12-08', 'Dr. Patel', 'completed'],
        ['FARM-003', json_encode(['type' => 'Pesticide residues']), 'pending', json_encode(['status' => 'Analysis in progress']), 'Pending', '2025-12-07', 'Dr. Kumar', 'pending'],
        ['FARM-004', json_encode(['type' => 'Antibiotic sensitivity']), 'completed', json_encode(['sensitive_to' => ['Beta-lactams', 'Fluoroquinolones']]), 'Pass', '2025-12-09', 'Dr. Singh', 'completed'],
        ['FARM-005', json_encode(['type' => 'Multi-residue analysis']), 'completed', json_encode(['status' => 'All within limits']), 'Pass', '2025-12-06', 'Dr. Verma', 'completed'],
        ['FARM-001', json_encode(['type' => 'Microbial culture']), 'completed', json_encode(['result' => 'No pathogens detected']), 'Pass', '2025-12-05', 'Dr. Gupta', 'completed'],
        ['FARM-002', json_encode(['type' => 'Pesticide screening']), 'in_progress', json_encode(['traces' => 'DDT detected']), 'Alert', '2025-12-04', 'Dr. Mishra', 'in_progress'],
        ['FARM-003', json_encode(['type' => 'Multi-parameter analysis']), 'in_progress', json_encode(['status' => 'Testing underway']), 'Pending', '2025-12-09', 'Dr. Reddy', 'in_progress'],
        ['FARM-004', json_encode(['type' => 'Resistance panel']), 'completed', json_encode(['result' => 'No MDR strains']), 'Pass', '2025-12-03', 'Dr. Nair', 'completed'],
        ['FARM-005', json_encode(['type' => 'Microbial + chemical']), 'completed', json_encode(['quality' => 'Good', 'status' => 'Ready for use']), 'Pass', '2025-12-02', 'Dr. Iyer', 'completed'],
    ];
    
    for ($i = 0; $i < count($report_data); $i++) {
        $lab_id = ($i < count($lab_test_ids)) ? $lab_test_ids[$i] : ($i + 1);
        list($farm_id, $params, $status, $data, $mrl, $date, $generated_by, $stat) = $report_data[$i];
        $report_stmt->execute([$lab_id, $farm_id, $params, $status, $data, $mrl, $date, $generated_by, $stat]);
    }
    
    $pdo->commit();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Sample data inserted successfully',
        'tables_populated' => [
            'ai_analysis' => 10,
            'lab_tests' => 10,
            'lab_test_samples' => 10,
            'lab_test_reports' => 10
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        try {
            $pdo->rollBack();
        } catch (Exception $rb) {}
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error inserting sample data: ' . $e->getMessage(),
        'error' => $e->getMessage()
    ]);
}
?>
