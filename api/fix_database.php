<?php
// Direct database fix - add missing columns to prescriptions table
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

require_once 'config.php';

$log = [];
$success = true;

try {
    // List of columns to add
    $columns = [
        'animal_type' => "VARCHAR(50) DEFAULT NULL",
        'diagnosis' => "TEXT DEFAULT NULL",
        'symptoms' => "TEXT DEFAULT NULL",
        'administration_frequency' => "VARCHAR(100) DEFAULT NULL",
        'administration_time' => "VARCHAR(100) DEFAULT NULL",
        'duration_days' => "INT DEFAULT 7"
    ];

    // Check and add each column
    foreach ($columns as $col_name => $col_def) {
        try {
            $pdo->exec("ALTER TABLE prescriptions ADD COLUMN `$col_name` $col_def");
            $log[] = "✓ Added column: $col_name";
        } catch (Exception $e) {
            // Column might already exist
            $log[] = "⚠ Column $col_name: " . $e->getMessage();
        }
    }

    // Create prescription_items table
    $create_table_sql = "
        CREATE TABLE IF NOT EXISTS `prescription_items` (
          `id` INT PRIMARY KEY AUTO_INCREMENT,
          `prescription_id` INT NOT NULL,
          `medicine_id` VARCHAR(50) NOT NULL,
          `dosage_rate` DECIMAL(8,2) NOT NULL,
          `body_weight` DECIMAL(8,2) NOT NULL,
          `calculated_dosage` DECIMAL(8,2) NOT NULL,
          `dosage_unit` VARCHAR(20) DEFAULT 'mg',
          `frequency` VARCHAR(100) NOT NULL,
          `duration_days` INT DEFAULT 7,
          `withdrawal_period_days` INT DEFAULT 0,
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions`(`id`) ON DELETE CASCADE,
          INDEX idx_prescription_id (prescription_id),
          INDEX idx_medicine_id (medicine_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    try {
        $pdo->exec($create_table_sql);
        $log[] = "✓ Created prescription_items table";
    } catch (Exception $e) {
        $log[] = "⚠ prescription_items table: " . $e->getMessage();
    }

    // Verify columns
    $result = $pdo->query("DESCRIBE prescriptions");
    $prescriptions_cols = $result->fetchAll(PDO::FETCH_ASSOC);
    
    $result = $pdo->query("DESCRIBE prescription_items");
    $items_cols = $result->fetchAll(PDO::FETCH_ASSOC);

    $log[] = "✓ Prescriptions table: " . count($prescriptions_cols) . " columns";
    $log[] = "✓ Prescription_items table: " . count($items_cols) . " columns";

    ob_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Database fixed successfully',
        'log' => $log,
        'prescriptions_columns' => count($prescriptions_cols),
        'items_columns' => count($items_cols)
    ]);

} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'log' => $log
    ]);
}
