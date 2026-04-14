<?php
// Database configuration for XAMPP Local Development
// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database configuration for XAMPP
$host = 'localhost';        // XAMPP default host
$dbname = 'agrisense_db';   // Create this database in phpMyAdmin
$username = 'root';         // XAMPP default user
$password = '';             // XAMPP default (no password)
$port = 3306;               // MySQL default port

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    
    // Set error mode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database connection failed: ' . $e->getMessage(),
        'error' => $e->getMessage(),
        'debug' => [
            'host' => $host,
            'dbname' => $dbname,
            'port' => $port
        ]
    ]);
    exit();
}

// Log all API requests (for debugging)
$logFile = __DIR__ . '/api_logs.txt';
function logRequest($endpoint, $method, $data = null) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $method $endpoint - " . ($data ? json_encode($data) : 'No data') . "\n";
    error_log($logMessage, 3, $logFile);
}

// Helper function to send JSON response
if (!function_exists('sendResponse')) {
    function sendResponse($data) {
        echo json_encode($data);
        exit();
    }
}

// Helper function for error responses
if (!function_exists('sendError')) {
    function sendError($message, $statusCode = 400, $data = null) {
        http_response_code($statusCode);
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        if ($data) {
            $response['data'] = $data;
        }
        echo json_encode($response);
        exit();
    }
}

// Helper function to get request data
function getRequestData() {
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET') {
        return $_GET;
    } elseif ($method === 'POST' || $method === 'PUT' || $method === 'DELETE' || $method === 'PATCH') {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }
    
    return [];
}

// Database initialization functions
function initializeDatabase($pdo) {
    try {
        // Check if tables exist, if not create them
        $tables = [
            'veterinarians',
            'government_users',
            'medicines',
            'symptoms',
            'medicine_symptoms',
            'prescriptions',
            'prescription_items',
            'mrl_lab_tests',
            'lab_tests',
            'lab_test_samples',
            'lab_test_reports',
            'alerts',
            'audit_logs',
            'farms',
            'policies',
            'consultation_requests',
            'health_history',
            'safety_alerts',
            'farm_alerts',
            'treatment_history',
            'batches',
            'ai_analysis',
            'product_info'
        ];
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() == 0) {
                createTable($pdo, $table);
            }
        }
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function createTable($pdo, $tableName) {
    $createTableQueries = [
        'veterinarians' => "
            CREATE TABLE veterinarians (
                id INT PRIMARY KEY AUTO_INCREMENT,
                vet_id VARCHAR(50) UNIQUE NOT NULL,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100),
                phone VARCHAR(20),
                license_number VARCHAR(100) UNIQUE,
                specialization VARCHAR(100),
                experience INT,
                password VARCHAR(255),
                status VARCHAR(20) DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'government_users' => "
            CREATE TABLE government_users (
                id INT PRIMARY KEY AUTO_INCREMENT,
                gov_id VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255),
                tier VARCHAR(20),
                region VARCHAR(100),
                department VARCHAR(100),
                name VARCHAR(100),
                email VARCHAR(100),
                status VARCHAR(20) DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'medicines' => "
            CREATE TABLE medicines (
                id INT PRIMARY KEY AUTO_INCREMENT,
                medicine_id VARCHAR(50) UNIQUE NOT NULL,
                name VARCHAR(100) NOT NULL,
                generic_name VARCHAR(100),
                type VARCHAR(50),
                dosage_rate DECIMAL(10,2),
                dosage_unit VARCHAR(20),
                mrl_limit DECIMAL(10,2),
                withdrawal_period_days INT,
                approved BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_name (name),
                INDEX idx_type (type)
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'symptoms' => "
            CREATE TABLE symptoms (
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
        ",
        'medicine_symptoms' => "
            CREATE TABLE medicine_symptoms (
                id INT PRIMARY KEY AUTO_INCREMENT,
                medicine_id VARCHAR(50) NOT NULL,
                symptom_id VARCHAR(50) NOT NULL,
                effectiveness DECIMAL(5,2),
                animal_type VARCHAR(50),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (medicine_id, symptom_id, animal_type),
                INDEX idx_symptom_id (symptom_id)
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'prescription_items' => "
            CREATE TABLE prescription_items (
                id INT PRIMARY KEY AUTO_INCREMENT,
                prescription_id INT NOT NULL,
                medicine_id VARCHAR(50) NOT NULL,
                dosage_rate DECIMAL(10,2),
                body_weight DECIMAL(8,2),
                calculated_dosage DECIMAL(10,2),
                dosage_unit VARCHAR(20),
                frequency VARCHAR(50),
                duration_days INT,
                withdrawal_period_days INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (prescription_id) REFERENCES prescriptions(id),
                INDEX idx_prescription_id (prescription_id),
                INDEX idx_medicine_id (medicine_id)
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'prescriptions' => "
            CREATE TABLE prescriptions (
                id INT PRIMARY KEY AUTO_INCREMENT,
                prescription_id VARCHAR(50) UNIQUE NOT NULL,
                animal_id VARCHAR(50) NOT NULL,
                animal_type VARCHAR(50),
                animal_owner VARCHAR(100),
                animal_weight DECIMAL(8,2),
                farm_id VARCHAR(50),
                farm_location VARCHAR(200),
                farm_lat DECIMAL(10,8),
                farm_lng DECIMAL(10,8),
                symptoms TEXT,
                diagnosis TEXT,
                instructions TEXT,
                administration_frequency VARCHAR(50),
                administration_time VARCHAR(50),
                duration_days INT,
                withdrawal_period_days INT,
                vet_id VARCHAR(50),
                status VARCHAR(20) DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_farm_id (farm_id),
                INDEX idx_animal_id (animal_id),
                INDEX idx_vet_id (vet_id)
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'mrl_lab_tests' => "
            CREATE TABLE mrl_lab_tests (
                id INT PRIMARY KEY AUTO_INCREMENT,
                test_id VARCHAR(50) UNIQUE NOT NULL,
                sample_type VARCHAR(50),
                sample_id VARCHAR(100),
                lab_id VARCHAR(50),
                test_parameters JSON,
                result_status VARCHAR(20),
                result_data JSON,
                certificate_number VARCHAR(100),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'lab_tests' => "
            CREATE TABLE lab_tests (
                id INT PRIMARY KEY AUTO_INCREMENT,
                farm_id VARCHAR(50),
                test_type VARCHAR(100),
                animal_type VARCHAR(50),
                status VARCHAR(50) DEFAULT 'pending',
                created_by VARCHAR(50),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_farm_id (farm_id),
                INDEX idx_status (status)
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'lab_test_samples' => "
            CREATE TABLE lab_test_samples (
                id INT PRIMARY KEY AUTO_INCREMENT,
                lab_test_id INT NOT NULL,
                sample_type VARCHAR(50),
                sample_id VARCHAR(100),
                collection_date DATE,
                collection_location VARCHAR(200),
                collected_by VARCHAR(100),
                status VARCHAR(50) DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_lab_test_id (lab_test_id),
                INDEX idx_sample_id (sample_id),
                FOREIGN KEY (lab_test_id) REFERENCES lab_tests(id)
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'lab_test_reports' => "
            CREATE TABLE lab_test_reports (
                id INT PRIMARY KEY AUTO_INCREMENT,
                lab_test_id INT NOT NULL,
                farm_id VARCHAR(50),
                test_parameters JSON,
                result_status VARCHAR(50),
                result_data JSON,
                mrl_status VARCHAR(50),
                report_date DATE,
                generated_by VARCHAR(100),
                status VARCHAR(50) DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_lab_test_id (lab_test_id),
                INDEX idx_farm_id (farm_id),
                INDEX idx_result_status (result_status),
                FOREIGN KEY (lab_test_id) REFERENCES lab_tests(id)
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'alerts' => "
            CREATE TABLE alerts (
                id INT PRIMARY KEY AUTO_INCREMENT,
                description TEXT,
                severity VARCHAR(20),
                resolved BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'audit_logs' => "
            CREATE TABLE audit_logs (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id VARCHAR(50),
                action VARCHAR(100),
                entity_type VARCHAR(50),
                entity_id VARCHAR(100),
                old_value JSON,
                new_value JSON,
                ip_address VARCHAR(45),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'farms' => "
            CREATE TABLE farms (
                id INT PRIMARY KEY AUTO_INCREMENT,
                farm_id VARCHAR(50) UNIQUE NOT NULL,
                name VARCHAR(100),
                location VARCHAR(100),
                state VARCHAR(50),
                latitude DECIMAL(10,8),
                longitude DECIMAL(10,8),
                owner_name VARCHAR(100),
                contact_phone VARCHAR(20),
                mrl_status VARCHAR(20) DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'policies' => "
            CREATE TABLE policies (
                id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(200),
                category VARCHAR(50),
                content LONGTEXT,
                effective_date DATE,
                tier VARCHAR(50),
                created_by VARCHAR(50),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'consultation_requests' => "
            CREATE TABLE consultation_requests (
                id INT PRIMARY KEY AUTO_INCREMENT,
                farmer_phone VARCHAR(20),
                animal_id VARCHAR(50),
                issue_description TEXT,
                status VARCHAR(20) DEFAULT 'pending',
                vet_id VARCHAR(50),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'health_history' => "
            CREATE TABLE health_history (
                id INT PRIMARY KEY AUTO_INCREMENT,
                animal_id VARCHAR(50) NOT NULL,
                treatment_date DATE,
                treatment_description TEXT,
                vet_id VARCHAR(50),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'safety_alerts' => "
            CREATE TABLE safety_alerts (
                id INT PRIMARY KEY AUTO_INCREMENT,
                farm_id VARCHAR(50),
                alert_type VARCHAR(50),
                description TEXT,
                severity VARCHAR(20),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'farm_alerts' => "
            CREATE TABLE farm_alerts (
                id INT PRIMARY KEY AUTO_INCREMENT,
                farm_id VARCHAR(50),
                alert_message TEXT,
                alert_type VARCHAR(50),
                status VARCHAR(20) DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'treatment_history' => "
            CREATE TABLE treatment_history (
                id INT PRIMARY KEY AUTO_INCREMENT,
                animal_id VARCHAR(50),
                treatment_date DATE,
                medicine_used VARCHAR(100),
                dosage VARCHAR(50),
                vet_id VARCHAR(50),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'batches' => "
            CREATE TABLE batches (
                id INT PRIMARY KEY AUTO_INCREMENT,
                batch_id VARCHAR(50) UNIQUE NOT NULL,
                farm_id VARCHAR(50),
                product_type VARCHAR(50),
                mrl_status VARCHAR(20),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'ai_analysis' => "
            CREATE TABLE ai_analysis (
                id INT PRIMARY KEY AUTO_INCREMENT,
                animal_id VARCHAR(50),
                symptoms TEXT,
                recommendation TEXT,
                confidence_score DECIMAL(5,2),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ",
        'product_info' => "
            CREATE TABLE product_info (
                id INT PRIMARY KEY AUTO_INCREMENT,
                product_id VARCHAR(100) UNIQUE NOT NULL,
                farm_id VARCHAR(50),
                product_type VARCHAR(50),
                batch_number VARCHAR(100),
                mrl_compliant BOOLEAN,
                lab_test_date DATE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        "
    ];
    
    if (isset($createTableQueries[$tableName])) {
        try {
            $pdo->exec($createTableQueries[$tableName]);
        } catch (Exception $e) {
            // Table might already exist
        }
    }
}

// Initialize database on first load
initializeDatabase($pdo);

?>