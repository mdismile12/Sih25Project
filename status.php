<?php
// Quick Database & API Status Check
// Access at: http://localhost/update_after_mentoring_1/status.php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

$status = [
    'timestamp' => date('Y-m-d H:i:s'),
    'application' => 'Agrisense Portal',
    'version' => '1.0.0',
    'checks' => []
];

// 1. PHP Version
$status['checks']['php_version'] = [
    'name' => 'PHP Version',
    'required' => '7.4.0',
    'current' => phpversion(),
    'status' => version_compare(phpversion(), '7.4.0', '>=') ? 'OK' : 'FAIL'
];

// 2. Check if .htaccess exists
$htaccess_path = __DIR__ . '/.htaccess';
$status['checks']['htaccess'] = [
    'name' => '.htaccess file',
    'path' => $htaccess_path,
    'exists' => file_exists($htaccess_path) ? true : false,
    'status' => file_exists($htaccess_path) ? 'OK' : 'MISSING'
];

// 3. Check if api directory exists
$api_dir = __DIR__ . '/api';
$status['checks']['api_directory'] = [
    'name' => 'API Directory',
    'path' => $api_dir,
    'exists' => is_dir($api_dir) ? true : false,
    'status' => is_dir($api_dir) ? 'OK' : 'MISSING'
];

// 4. Check if key API files exist
$api_files = [
    'config.php',
    'index.php',
    'vet_login.php',
    'prescriptions.php',
    'farm_details.php'
];

$status['checks']['api_files'] = [
    'name' => 'API Files',
    'files_checked' => count($api_files),
    'files_found' => 0,
    'status' => 'OK'
];

foreach ($api_files as $file) {
    $file_path = $api_dir . '/' . $file;
    if (file_exists($file_path)) {
        $status['checks']['api_files']['files_found']++;
    }
}

if ($status['checks']['api_files']['files_found'] < count($api_files)) {
    $status['checks']['api_files']['status'] = 'PARTIAL';
}

// 5. Try database connection
$status['checks']['database'] = [
    'name' => 'Database Connection',
    'status' => 'CHECKING'
];

try {
    $host = 'localhost';
    $dbname = 'agrisense_db';
    $username = 'root';
    $password = '';
    
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    
    // Check tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $status['checks']['database']['status'] = 'OK';
    $status['checks']['database']['tables_found'] = count($tables);
    $status['checks']['database']['required_tables'] = 18;
    
    if (count($tables) < 18) {
        $status['checks']['database']['status'] = 'PARTIAL';
        $status['checks']['database']['message'] = 'Some tables missing. Please import database_setup.sql';
    }
    
} catch (Exception $e) {
    $status['checks']['database']['status'] = 'FAIL';
    $status['checks']['database']['error'] = $e->getMessage();
}

// 6. Check if config.php can be included
$status['checks']['api_config'] = [
    'name' => 'API Configuration',
    'status' => 'CHECKING'
];

$config_file = __DIR__ . '/api/config.php';
if (file_exists($config_file)) {
    $status['checks']['api_config']['status'] = 'OK';
    $status['checks']['api_config']['path'] = $config_file;
} else {
    $status['checks']['api_config']['status'] = 'FAIL';
    $status['checks']['api_config']['path'] = 'NOT FOUND';
}

// 7. Calculate overall status
$status['overall_status'] = 'OK';
foreach ($status['checks'] as $check => $result) {
    if (isset($result['status']) && $result['status'] === 'FAIL') {
        $status['overall_status'] = 'FAIL';
        break;
    } elseif (isset($result['status']) && $result['status'] === 'PARTIAL') {
        if ($status['overall_status'] !== 'FAIL') {
            $status['overall_status'] = 'PARTIAL';
        }
    }
}

// 8. Add recommendations
$status['recommendations'] = [];

if (!file_exists($htaccess_path)) {
    $status['recommendations'][] = '❌ .htaccess file not found. Create it with API routing rules.';
}

if (!is_dir($api_dir)) {
    $status['recommendations'][] = '❌ API directory not found. Check folder structure.';
}

if ($status['checks']['database']['status'] === 'FAIL') {
    $status['recommendations'][] = '❌ Database connection failed. Check MySQL is running and credentials are correct.';
} elseif ($status['checks']['database']['status'] === 'PARTIAL') {
    $status['recommendations'][] = '⚠️ Some database tables missing. Import database_setup.sql through phpMyAdmin.';
}

if (empty($status['recommendations'])) {
    $status['recommendations'][] = '✅ All systems operational. You\'re ready to go!';
}

// 9. Add quick links
$status['quick_links'] = [
    'application' => 'http://localhost/update_after_mentoring_1/',
    'phpmyadmin' => 'http://localhost/phpmyadmin',
    'api_test_panel' => 'http://localhost/update_after_mentoring_1/api_test_panel.html',
    'api_status' => 'http://localhost/update_after_mentoring_1/api/index.php',
    'setup_guide' => 'SETUP_GUIDE.md',
    'fix_summary' => 'FIX_SUMMARY.txt'
];

echo json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
