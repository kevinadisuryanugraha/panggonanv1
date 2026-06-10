<?php
require_once __DIR__ . '/../config/db.php';
$query = "CREATE TABLE IF NOT EXISTS `reservations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `branch` VARCHAR(50) NOT NULL,
  `event_type` VARCHAR(100) NOT NULL,
  `reservation_date` DATE NOT NULL,
  `pax` INT NOT NULL,
  `note` TEXT,
  `status` ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

try {
    $pdo->exec($query);
    echo "SUCCESS: Tabel reservations berhasil dibuat/diverifikasi!\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
