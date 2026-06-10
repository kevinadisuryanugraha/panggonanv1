<?php
require_once __DIR__ . '/../config/db.php';
$stmt = $pdo->query('SELECT id, author, quote, text, media_url, date_label, status FROM journals WHERE status="approved" ORDER BY id DESC LIMIT 12');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "ID: " . $row['id'] . " | Author: " . $row['author'] . " | Date: " . $row['date_label'] . " | Media: " . $row['media_url'] . "\n";
    echo "Quote: " . $row['quote'] . "\n";
    echo "Text: " . substr(strip_tags($row['text']), 0, 50) . "...\n";
    echo "--------------------------------------------------\n";
}
