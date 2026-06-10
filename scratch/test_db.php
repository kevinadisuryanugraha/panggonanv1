<?php
require_once __DIR__ . '/../config/db.php';
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'reservations'");
    $exists = $stmt->rowCount() > 0;
    echo "Reservations table exists: " . ($exists ? "YES" : "NO") . "\n";
    if ($exists) {
        $desc = $pdo->query("DESCRIBE reservations")->fetchAll();
        print_r($desc);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
