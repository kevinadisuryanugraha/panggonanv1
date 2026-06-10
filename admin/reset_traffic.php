<?php
/**
 * Reset Traffic Analytics Database
 * Empty visitor_logs and traffic_conversions tables to start logging fresh from zero.
 */

session_start();
header("Content-Type: application/json");

// Security: Only logged in admins can reset data
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Forbidden']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

try {
    // Truncate tables to empty them completely and reset auto-increments
    $pdo->exec("TRUNCATE TABLE `visitor_logs`");
    $pdo->exec("TRUNCATE TABLE `traffic_conversions`");
    
    echo json_encode(['status' => 'success', 'message' => 'Database cleared successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
