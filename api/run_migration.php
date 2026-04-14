<?php
// Migration runner - execute pending database migrations
// This file applies the database schema changes needed for the prescription system

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

require_once 'config.php';

function runMigration() {
    global $pdo;
    $log = [];

    try {
        $log[] = "Starting database migration...";

        // 1. Add missing columns to prescriptions table
        $migrations = [
            "ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS animal_type VARCHAR(50) DEFAULT NULL",
            "ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS diagnosis TEXT DEFAULT NULL",
            "ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS symptoms TEXT DEFAULT NULL",
            "ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS administration_frequency VARCHAR(100) DEFAULT NULL",
            "ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS administration_time VARCHAR(100) DEFAULT NULL",
            "ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS duration_days INT DEFAULT 7",
        ];

        foreach ($migrations as $migration) {
            try {
                $pdo->exec($migration);
                $log[] = "✓ " . $migration;
            } catch (Exception $e) {
                $log[] = "⚠ " . $migration . " (might already exist)";
            }
        }

        // 2. Create prescription_items table
        $create_items_table = "
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
            $pdo->exec($create_items_table);
            $log[] = "✓ Created prescription_items table";
        } catch (Exception $e) {
            $log[] = "⚠ prescription_items table (might already exist): " . $e->getMessage();
        }

        // 3. Create indexes for performance
        $indexes = [
            "CREATE INDEX IF NOT EXISTS idx_prescriptions_farm_id ON prescriptions(farm_id)",
            "CREATE INDEX IF NOT EXISTS idx_prescriptions_vet_id ON prescriptions(vet_id)",
            "CREATE INDEX IF NOT EXISTS idx_prescriptions_status ON prescriptions(status)",
            "CREATE INDEX IF NOT EXISTS idx_prescriptions_created_at ON prescriptions(created_at)",
        ];

        foreach ($indexes as $index) {
            try {
                $pdo->exec($index);
                $log[] = "✓ " . $index;
            } catch (Exception $e) {
                $log[] = "⚠ Index (might already exist)";
            }
        }

        // 4. Verify tables exist
        $check_prescriptions = $pdo->query("DESCRIBE prescriptions");
        $prescriptions_cols = $check_prescriptions->fetchAll(PDO::FETCH_ASSOC);
        
        $check_items = $pdo->query("DESCRIBE prescription_items");
        $items_cols = $check_items->fetchAll(PDO::FETCH_ASSOC);

        $log[] = "✓ Migration completed successfully!";
        $log[] = "Prescriptions table has " . count($prescriptions_cols) . " columns";
        $log[] = "Prescription_items table has " . count($items_cols) . " columns";

        ob_clean();
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Database migration completed successfully',
            'log' => $log,
            'prescriptions_columns' => count($prescriptions_cols),
            'items_columns' => count($items_cols)
        ]);

    } catch (Exception $e) {
        ob_clean();
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Migration failed: ' . $e->getMessage(),
            'log' => $log
        ]);
    }
}

// Handle request
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' || $method === 'GET') {
    runMigration();
} else {
    ob_clean();
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Only GET and POST methods are allowed'
    ]);
}
