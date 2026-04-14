<?php
// Direct database fix script
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

try {
    require_once 'config.php';
    
    $fixes = [];
    
    // Add missing columns to prescriptions table
    $columns_to_add = [
        'animal_type' => 'VARCHAR(50)',
        'diagnosis' => 'TEXT',
        'symptoms' => 'TEXT',
        'administration_frequency' => 'VARCHAR(100)',
        'administration_time' => 'VARCHAR(100)',
        'duration_days' => 'INT DEFAULT 7',
        'farm_location' => 'VARCHAR(255)',
        'farm_lat' => 'DECIMAL(10,8)',
        'farm_lng' => 'DECIMAL(10,8)'
    ];
    
    // Get current columns
    $result = $pdo->query("DESCRIBE prescriptions");
    $existing_cols = [];
    foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $col) {
        $existing_cols[$col['Field']] = true;
    }
    
    // Add missing columns
    foreach ($columns_to_add as $col_name => $col_type) {
        if (!isset($existing_cols[$col_name])) {
            try {
                $sql = "ALTER TABLE prescriptions ADD COLUMN `{$col_name}` {$col_type}";
                $pdo->exec($sql);
                $fixes[] = "✓ Added column: $col_name";
            } catch (Exception $e) {
                $fixes[] = "⚠ Column $col_name: Could not add";
            }
        }
    }
    
    // Create prescription_items table
    $create_sql = "CREATE TABLE IF NOT EXISTS prescription_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        prescription_id INT NOT NULL,
        medicine_id VARCHAR(50) NOT NULL,
        dosage_rate DECIMAL(8,2),
        body_weight DECIMAL(8,2),
        calculated_dosage DECIMAL(8,2),
        dosage_unit VARCHAR(20),
        frequency VARCHAR(100),
        duration_days INT,
        withdrawal_period_days INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE,
        INDEX idx_prescription_id (prescription_id),
        INDEX idx_medicine_id (medicine_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    try {
        $pdo->exec($create_sql);
        $fixes[] = "✓ Created prescription_items table";
    } catch (Exception $e) {
        // Table might exist
        $fixes[] = "⚠ prescription_items table already exists";
    }
    
    // Verify the fix
    $presc_result = $pdo->query("DESCRIBE prescriptions");
    $presc_cols = $presc_result->fetchAll(PDO::FETCH_ASSOC);
    
    $items_result = $pdo->query("DESCRIBE prescription_items");
    $items_cols = $items_result->fetchAll(PDO::FETCH_ASSOC);
    
    $fixes[] = "";
    $fixes[] = "Database Status:";
    $fixes[] = "- Prescriptions table: " . count($presc_cols) . " columns";
    $fixes[] = "- Prescription_items table: " . count($items_cols) . " columns";
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Database fixed successfully',
        'fixes' => $fixes,
        'prescriptions_columns' => count($presc_cols),
        'items_columns' => count($items_cols)
    ]);
    
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'error_code' => 500
    ]);
}
