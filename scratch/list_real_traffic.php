<?php
require_once __DIR__ . '/../config/db.php';

try {
    echo "Active visitor_logs rows:\n";
    $logs = $pdo->query("SELECT id, session_id, ip_address, visited_page, duration, status, created_at FROM `visitor_logs` ORDER BY id DESC LIMIT 10")->fetchAll();
    print_r($logs);

    echo "\nActive traffic_conversions rows:\n";
    $convs = $pdo->query("SELECT id, session_id, conversion_type, page_url, created_at FROM `traffic_conversions` ORDER BY id DESC LIMIT 10")->fetchAll();
    print_r($convs);
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
