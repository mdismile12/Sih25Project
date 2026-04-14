<?php
// Run AMU seed SQL file using existing DB config
header('Content-Type: application/json');
require_once 'config.php';

$seedFile = __DIR__ . '/../database_seed_amu_trends.sql';
if (!file_exists($seedFile)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Seed file not found: ' . $seedFile]);
    exit;
}

try {
    $sql = file_get_contents($seedFile);
    if (!$sql) throw new Exception('Failed to read seed file');

    // Split statements on semicolon; naive but OK for this seed file
    $stmts = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));

    $inTransaction = false;
    try {
        $pdo->beginTransaction();
        $inTransaction = true;
        
        foreach ($stmts as $stmt) {
            if ($stmt === '') continue;
            $pdo->exec($stmt);
        }
        $pdo->commit();
        $inTransaction = false;
    } catch (Exception $e) {
        if ($inTransaction) {
            try {
                $pdo->rollBack();
            } catch (Exception $rb) {
                // Ignore rollback errors
            }
        }
        throw $e;
    }

    echo json_encode(['success' => true, 'message' => 'Seed executed successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
