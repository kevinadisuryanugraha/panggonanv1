<?php
/**
 * Panggonan Resto — Handler Submit Reservasi Terpadu (AJAX)
 * Menerima request POST dari contact-us/index.php, memvalidasi input,
 * menyimpan data reservasi ke MySQL dengan status 'pending',
 * dan mengembalikan respon JSON sukses.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$branch = trim($_POST['branch'] ?? '');
$event_type = trim($_POST['event_type'] ?? '');
$reservation_date = trim($_POST['reservation_date'] ?? '');
$reservation_time = trim($_POST['reservation_time'] ?? '');
$pax_raw = $_POST['pax'] ?? '';
$note = trim($_POST['note'] ?? '');

// 1. VALIDATION: Check required fields
if (empty($name) || empty($phone) || empty($branch) || empty($event_type) || empty($reservation_date) || empty($reservation_time) || empty($pax_raw)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Semua kolom bertanda wajib harus diisi dengan benar.']);
    exit;
}

// 2. SECURITY & HYGIENE: Input Sanitation & Length Limiting
$name = mb_substr(strip_tags($name), 0, 100);
$phone = mb_substr(strip_tags($phone), 0, 20);
$event_type = mb_substr(strip_tags($event_type), 0, 100);
$note = mb_substr(strip_tags($note), 0, 65000);

// Validate branch value strictly
if ($branch !== 'GDC Depok' && $branch !== 'Ciracas') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Cabang yang dipilih tidak valid.']);
    exit;
}

// Validate date format (YYYY-MM-DD)
$date_parts = explode('-', $reservation_date);
if (count($date_parts) !== 3 || !checkdate((int)$date_parts[1], (int)$date_parts[2], (int)$date_parts[0])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Format tanggal pelaksanaan tidak valid.']);
    exit;
}

// Validate time format (HH:MM or HH:MM:SS)
if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $reservation_time)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Format jam pelaksanaan tidak valid.']);
    exit;
}

// Validate pax is positive integer >= 1
$pax = (int)$pax_raw;
if ($pax < 1) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Jumlah tamu/pax minimal adalah 1 orang.']);
    exit;
}

try {
    // 3. DATABASE: Secure Prepared Statement Insertion
    $stmt = $pdo->prepare("INSERT INTO reservations (name, phone, branch, event_type, reservation_date, reservation_time, pax, note, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$name, $phone, $branch, $event_type, $reservation_date, $reservation_time, $pax, $note]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Reservasi berhasil disimpan ke database.'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menyimpan data ke database. Silakan coba lagi.'
    ]);
}
exit;
