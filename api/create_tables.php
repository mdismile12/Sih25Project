<?php
header('Content-Type: application/json');
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    if (!isset($pdo)) {
        throw new Exception("Database connection not available");
    }

    // Create symptoms table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS symptoms (
            id INT PRIMARY KEY AUTO_INCREMENT,
            symptom_id VARCHAR(50) UNIQUE NOT NULL,
            name VARCHAR(100) NOT NULL,
            category VARCHAR(50),
            description TEXT,
            severity VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_name (name),
            INDEX idx_category (category)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
    ");

    // Create medicine_symptoms table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS medicine_symptoms (
            id INT PRIMARY KEY AUTO_INCREMENT,
            medicine_id VARCHAR(50) NOT NULL,
            symptom_id VARCHAR(50) NOT NULL,
            effectiveness DECIMAL(5,2),
            animal_type VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_med_sym (medicine_id, symptom_id, animal_type),
            INDEX idx_symptom_id (symptom_id)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
    ");

    // Create lab_tests table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS lab_tests (
            id INT PRIMARY KEY AUTO_INCREMENT,
            farm_id VARCHAR(100) DEFAULT NULL,
            animal_id VARCHAR(100) DEFAULT NULL,
            animal_type VARCHAR(50) DEFAULT NULL,
            test_type VARCHAR(100) DEFAULT NULL,
            description TEXT,
            priority VARCHAR(50) DEFAULT 'normal',
            vet_id VARCHAR(50) DEFAULT NULL,
            created_by VARCHAR(100) DEFAULT NULL,
            status VARCHAR(50) DEFAULT 'pending',
            test_results JSON DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL,
            INDEX idx_farm_id (farm_id),
            INDEX idx_status (status)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
    ");

    // Create lab_test_samples table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS lab_test_samples (
            id INT PRIMARY KEY AUTO_INCREMENT,
            lab_test_id INT NOT NULL,
            sample_id VARCHAR(100) UNIQUE NOT NULL,
            sample_type VARCHAR(100) DEFAULT NULL,
            collection_date DATE DEFAULT NULL,
            collector_name VARCHAR(150) DEFAULT NULL,
            collected_by VARCHAR(150) DEFAULT NULL,
            quantity DECIMAL(10,3) DEFAULT NULL,
            unit VARCHAR(20) DEFAULT 'ml',
            status VARCHAR(50) DEFAULT 'collected',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL,
            INDEX idx_lab_test_id (lab_test_id),
            FOREIGN KEY (lab_test_id) REFERENCES lab_tests(id) ON DELETE CASCADE
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
    ");

    // Create lab_test_reports table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS lab_test_reports (
            id INT PRIMARY KEY AUTO_INCREMENT,
            lab_test_id INT NOT NULL,
            farm_id VARCHAR(100) DEFAULT NULL,
            sample_id VARCHAR(100) DEFAULT NULL,
            lab_name VARCHAR(200) DEFAULT NULL,
            technician VARCHAR(150) DEFAULT NULL,
            generated_by VARCHAR(150) DEFAULT NULL,
            test_parameters JSON DEFAULT NULL,
            result_status VARCHAR(50) DEFAULT NULL,
            result_data JSON DEFAULT NULL,
            test_results JSON DEFAULT NULL,
            mrl_status VARCHAR(50) DEFAULT NULL,
            report_date DATE DEFAULT NULL,
            approval_notes TEXT,
            approved_by VARCHAR(150) DEFAULT NULL,
            status VARCHAR(50) DEFAULT 'generated',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL,
            INDEX idx_lab_test_id (lab_test_id),
            INDEX idx_sample_id (sample_id),
            FOREIGN KEY (lab_test_id) REFERENCES lab_tests(id) ON DELETE CASCADE
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
    ");

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Tables created successfully'
    ]);

    // Ensure expected columns exist (backfill for older schemas)
    $expectedLabCols = [
        'description' => "TEXT",
        'animal_id' => "VARCHAR(100) DEFAULT NULL",
        'animal_type' => "VARCHAR(50) DEFAULT NULL",
        'vet_id' => "VARCHAR(50) DEFAULT NULL",
        'priority' => "VARCHAR(50) DEFAULT 'normal'",
        'test_results' => "JSON DEFAULT NULL",
        'updated_at' => "TIMESTAMP NULL DEFAULT NULL"
    ];

    $colsStmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lab_tests'");
    $colsStmt->execute();
    $existingCols = $colsStmt->fetchAll(PDO::FETCH_COLUMN, 0);

    foreach ($expectedLabCols as $col => $definition) {
        if (!in_array($col, $existingCols)) {
            $pdo->exec("ALTER TABLE lab_tests ADD COLUMN $col $definition");
        }
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
