<?php
/**
 * Agrisense Portal - Database Setup & Status Check
 * Run this file once after placing files on XAMPP
 */

header('Content-Type: application/json; charset=utf-8');

// Check PHP version
$php_version_ok = version_compare(PHP_VERSION, '7.0.0', '>=');

// Check required extensions
$extensions = [
    'mysqli' => extension_loaded('mysqli'),
    'pdo' => extension_loaded('pdo'),
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'json' => extension_loaded('json')
];

// Check file permissions
$files_writable = [
    'config.php' => is_writable(__DIR__ . '/config.php'),
    'api_logs.txt' => is_writable(__DIR__) // Check if directory is writable
];

// Attempt database connection
$db_status = 'not-connected';
$db_error = '';

try {
    $pdo = new PDO('mysql:host=localhost;port=3306', 'root', '');
    $db_status = 'connected';
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'agrisense_db'");
    if ($stmt->rowCount() == 0) {
        // Create database
        $pdo->exec("CREATE DATABASE agrisense_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $db_status = 'database-created';
    } else {
        $db_status = 'database-exists';
    }
} catch (PDOException $e) {
    $db_status = 'failed';
    $db_error = $e->getMessage();
}

// Return status
echo json_encode([
    'status' => 'ready',
    'php_version_ok' => $php_version_ok,
    'php_version' => PHP_VERSION,
    'extensions' => $extensions,
    'files_writable' => $files_writable,
    'database' => [
        'status' => $db_status,
        'error' => $db_error
    ],
    'setup_instructions' => [
        '1. Create database: Run setup or create manually in phpMyAdmin',
        '2. Import tables: Use config.php initialization',
        '3. Test connection: Check database status above',
        '4. Demo users: VET001/VET002 (pass: demo), GOV001/GOV002 (pass: demo)'
    ]
]);
?>
