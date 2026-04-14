<?php
require_once 'config.php';

sendResponse([
    'success' => true,
    'message' => 'Agrisense API is running',
    'version' => '1.0.0',
    'endpoints' => [
        'POST /vet_login.php' => 'Veterinarian login',
        'POST /vet_register.php' => 'Veterinarian registration',
        'POST /gov_login.php' => 'Government login',
        'POST /prescriptions.php' => 'Create prescription',
        'GET /prescriptions.php' => 'Get prescriptions',
        'POST /batches.php' => 'Add batch',
        'GET /batches.php' => 'Get batches',
        'POST /alerts.php' => 'Add alert',
        'GET /alerts.php' => 'Get alerts',
        'POST /farm_alerts.php' => 'Add farm alert',
        'GET /farm_alerts.php' => 'Get farm alerts',
        'POST /policies.php' => 'Add policy',
        'GET /policies.php' => 'Get policies',
        'GET /vet_verification.php' => 'Get vet verification data',
        'GET /product_info.php' => 'Get product information',
        'GET /farm_details.php' => 'Get farm details',
        'GET /safety_alerts.php' => 'Get safety alerts',
        'GET /health_history.php' => 'Get health history',
        'POST /ai_analysis.php' => 'AI symptom analysis',
        'GET /consultation_requests.php' => 'Get consultation requests',
        'GET /audit_logs.php' => 'Get audit logs'
    ]
]);
?>