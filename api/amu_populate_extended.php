<?php
// Helper: Populate extended AMU fields from existing prescriptions
header('Content-Type: application/json');
require_once 'config.php';

try {
    // First ensure extended columns exist
    $pdo->exec("ALTER TABLE `amu_records` 
    ADD COLUMN IF NOT EXISTS `species` VARCHAR(100) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `reason` VARCHAR(255) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `frequency_per_month` INT DEFAULT 0,
    ADD COLUMN IF NOT EXISTS `usage_rate` VARCHAR(50) DEFAULT 'low',
    ADD COLUMN IF NOT EXISTS `trend` VARCHAR(50) DEFAULT 'stable',
    ADD COLUMN IF NOT EXISTS `notes` TEXT DEFAULT NULL;");

    // Update amu_records from prescriptions data
    $sql = "UPDATE amu_records ar
            JOIN prescriptions p ON ar.prescription_id = p.id
            SET ar.species = COALESCE(ar.species, p.animal_type),
                ar.reason = COALESCE(ar.reason, p.diagnosis)
            WHERE ar.species IS NULL OR ar.reason IS NULL";
    
    $pdo->exec($sql);

    // Determine usage_rate based on amount
    $pdo->exec("UPDATE amu_records 
    SET usage_rate = 
        CASE 
            WHEN amount > 1000 THEN 'very_high'
            WHEN amount > 500 THEN 'high'
            WHEN amount > 100 THEN 'medium'
            ELSE 'low'
        END
    WHERE usage_rate IS NULL");

    echo json_encode([
        'success' => true,
        'message' => 'Extended AMU fields populated successfully',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => $e->getTraceAsString()
    ]);
}
?>
