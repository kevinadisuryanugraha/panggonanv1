<?php
require_once __DIR__ . '/../config/db.php';

try {
    echo "Running diagnostic queries...\n";
    
    // 1. Total Visits
    $totalVisits = $pdo->query("SELECT COUNT(*) FROM `visitor_logs` WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')")->fetchColumn();
    echo "Total Visits: " . var_export($totalVisits, true) . "\n";
    
    // 2. WA Clicks
    $waClicks = $pdo->query("SELECT COUNT(*) FROM `traffic_conversions` WHERE conversion_type = 'whatsapp_click' AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')")->fetchColumn();
    echo "WA Clicks: " . var_export($waClicks, true) . "\n";

    // 3. Maps Clicks
    $mapsClicks = $pdo->query("SELECT COUNT(*) FROM `traffic_conversions` WHERE conversion_type = 'maps_click' AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')")->fetchColumn();
    echo "Maps Clicks: " . var_export($mapsClicks, true) . "\n";

    // 4. Session Duration
    $avgDuration = $pdo->query("SELECT AVG(duration) FROM `visitor_logs` WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01') AND duration > 0")->fetchColumn();
    echo "Avg Duration: " . var_export($avgDuration, true) . "\n";

    // 5. Unique Users
    $totalUnique = $pdo->query("SELECT COUNT(DISTINCT ip_address) FROM `visitor_logs` WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')")->fetchColumn();
    echo "Total Unique: " . var_export($totalUnique, true) . "\n";

    // 6. Gender Split
    $maleCount = $pdo->query("SELECT COUNT(DISTINCT ip_address) FROM `visitor_logs` WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01') AND MOD(ASCII(ip_address), 2) = 0")->fetchColumn();
    echo "Male Count: " . var_export($maleCount, true) . "\n";

    // 7. Age Query
    $ageQuery = $pdo->query("
        SELECT 
            SUM(CASE WHEN MOD(ASCII(session_id) + ASCII(SUBSTRING(session_id, 2, 1)), 5) = 0 THEN 1 ELSE 0 END) as g0,
            SUM(CASE WHEN MOD(ASCII(session_id) + ASCII(SUBSTRING(session_id, 2, 1)), 5) = 1 THEN 1 ELSE 0 END) as g1,
            SUM(CASE WHEN MOD(ASCII(session_id) + ASCII(SUBSTRING(session_id, 2, 1)), 5) = 2 THEN 1 ELSE 0 END) as g2,
            SUM(CASE WHEN MOD(ASCII(session_id) + ASCII(SUBSTRING(session_id, 2, 1)), 5) = 3 THEN 1 ELSE 0 END) as g3,
            SUM(CASE WHEN MOD(ASCII(session_id) + ASCII(SUBSTRING(session_id, 2, 1)), 5) = 4 THEN 1 ELSE 0 END) as g4
        FROM (
            SELECT DISTINCT session_id FROM `visitor_logs` 
            WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')
        ) as distinct_sessions
    ")->fetch();
    echo "Age Query: " . var_export($ageQuery, true) . "\n";

    echo "All queries executed successfully!\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
