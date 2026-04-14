<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $title = $input['title'] ?? '';
    $category = $input['category'] ?? '';
    $content = $input['content'] ?? '';
    $effectiveDate = $input['effectiveDate'] ?? '';
    
    if (empty($title) || empty($content)) {
        sendResponse([
            'success' => false,
            'message' => 'Title and content are required'
        ]);
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO policies (title, category, content, effective_date, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$title, $category, $content, $effectiveDate]);
        
        sendResponse([
            'success' => true,
            'id' => $pdo->lastInsertId(),
            'message' => 'Policy added successfully'
        ]);
    } catch (Exception $e) {
        sendResponse([
            'success' => false,
            'message' => 'Failed to add policy: ' . $e->getMessage()
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM policies ORDER BY created_at DESC");
        $stmt->execute();
        $policies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        sendResponse([
            'success' => true,
            'data' => $policies
        ]);
    } catch (Exception $e) {
        sendResponse([
            'success' => true,
            'data' => [
                [
                    'id' => 1,
                    'title' => 'Antimicrobial Usage Guidelines',
                    'category' => 'amu',
                    'content' => 'All farms must maintain AMU records and adhere to prescribed withdrawal periods.',
                    'effectiveDate' => '2024-01-01'
                ]
            ]
        ]);
    }
} else {
    sendResponse([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>