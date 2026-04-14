<?php
/**
 * Extended AMU Seeding - Populate 50+ Records with Animal IDs
 */
header('Content-Type: application/json');
require_once 'config.php';

try {
    // Define farm data with locations and states
    $farms_data = [
        ['FARM-001', 'Pune', 'Maharashtra', 18.5204, 73.8567],
        ['FARM-002', 'Vadodara', 'Gujarat', 22.3072, 73.1812],
        ['FARM-003', 'Jalandhar', 'Punjab', 31.8261, 75.5762],
        ['FARM-004', 'Chennai', 'Tamil Nadu', 13.0827, 80.2707],
        ['FARM-005', 'Bengaluru', 'Karnataka', 12.9716, 77.5946],
    ];

    // Define animal prefixes by type
    $animal_types = ['Cattle', 'Buffalo', 'Poultry', 'Goat', 'Sheep'];
    $animal_prefixes = [
        'Cattle' => 'COW',
        'Buffalo' => 'BUF',
        'Poultry' => 'BIRD',
        'Goat' => 'GOAT',
        'Sheep' => 'SHEEP'
    ];

    // Medicine types and categories
    $medicines = [
        ['MED-AMOX', 'Amoxicillin 250mg', 'beta-lactam', 'VCIA', 300],
        ['MED-CIPRO', 'Ciprofloxacin 250mg', 'fluoroquinolone', 'VCIA', 250],
        ['MED-DOXY', 'Doxycycline 100mg', 'tetracycline', 'VHIA', 100],
        ['MED-OXY', 'Oxytetracycline 50ml', 'tetracycline', 'VHIA', 400],
        ['MED-COTR', 'Co-trimoxazole', 'sulfonamide', 'VHIA', 200],
        ['MED-AMOXI', 'Amoxicillin+Clavulanic Acid', 'beta-lactam', 'VCIA', 350],
        ['MED-METRO', 'Metronidazole', 'other', 'VIA', 150],
        ['MED-PENI', 'Penicillin G', 'beta-lactam', 'VHIA', 500],
        ['MED-CEF', 'Cephalexin', 'cephalosporin', 'VCIA', 300],
        ['MED-GENTAM', 'Gentamicin', 'aminoglycoside', 'VHIA', 80],
    ];

    // Sample diagnoses/reasons
    $reasons = [
        'Respiratory infection',
        'Gastrointestinal infection',
        'Mastitis',
        'Pneumonia',
        'Bacterial infection',
        'Wound infection',
        'Cough and fever',
        'Diarrhea',
        'Skin infection',
        'Urinary tract infection',
    ];

    // Clear existing records to start fresh (optional)
    // $pdo->exec("TRUNCATE TABLE amu_records");

    $insert_count = 0;
    $animal_count = 0;
    $vets = ['VET001', 'VET002', 'VET003', 'VET004', 'VET005'];

    // Insert 50+ AMU records with varied animals and medicines
    for ($i = 1; $i <= 55; $i++) {
        // Select random farm
        $farm = $farms_data[($i - 1) % count($farms_data)];
        list($farm_id, $location, $state, $lat, $lng) = $farm;

        // Select random animal type
        $animal_type = $animal_types[($i - 1) % count($animal_types)];
        $animal_prefix = $animal_prefixes[$animal_type];
        $animal_id = $animal_prefix . '-' . str_pad(101 + $i, 4, '0', STR_PAD_LEFT);
        $animal_count++;

        // Select random medicine
        $med = $medicines[($i - 1) % count($medicines)];
        list($med_id, $med_name, $med_type, $amu_cat, $dosage_base) = $med;

        // Vary the amount based on animal type
        $multiplier = match($animal_type) {
            'Cattle' => 2,
            'Buffalo' => 1.8,
            'Poultry' => 0.3,
            'Goat' => 0.8,
            'Sheep' => 0.7,
            default => 1
        };
        $amount = $dosage_base * $multiplier;

        $reason = $reasons[($i - 1) % count($reasons)];
        $vet_id = $vets[($i - 1) % count($vets)];
        $prescription_id = 100 + $i; // Simulated prescription IDs
        $unit = ($i % 3 == 0) ? 'IU' : (($i % 2 == 0) ? 'ml' : 'mg');

        $sql = "INSERT INTO amu_records (
            prescription_id, animal_id, medicine_id, medicine_name, medicine_type, 
            amu_category, amount, unit, farm_id, location, state, latitude, longitude, 
            species, reason, frequency_per_month, usage_rate, trend, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $prescription_id,
            $animal_id,
            $med_id,
            $med_name,
            $med_type,
            $amu_cat,
            $amount,
            $unit,
            $farm_id,
            $location,
            $state,
            $lat,
            $lng,
            $animal_type,
            $reason,
            1,
            'medium',
            'stable'
        ]);

        $insert_count++;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Extended AMU data seeded successfully',
        'records_inserted' => $insert_count,
        'distinct_animals' => $animal_count,
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error seeding AMU data: ' . $e->getMessage(),
        'error' => $e->getTraceAsString()
    ]);
}
?>
