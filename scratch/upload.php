<?php
/**
 * Panggonan Resto — Automated FTP Uploader using PHP cURL
 * Uploads panggonan_release.zip and installer.php directly to DomaiNesia public_html/ folder.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$ftp_host = 'flanders.id.rapidplex.com';
$ftp_user = 'panggona';
$ftp_pass = '75CP-l2USagg+3';

$files_to_upload = [
    __DIR__ . '/../panggonan_release.zip' => 'public_html/panggonan_release.zip',
    __DIR__ . '/installer.php'            => 'public_html/installer.php'
];

echo "🚀 Memulai Proses Upload Otomatis ke DomaiNesia...\n\n";

foreach ($files_to_upload as $local => $remote) {
    if (!file_exists($local)) {
        die("❌ ERROR: Berkas lokal tidak ditemukan: {$local}\n");
    }

    echo "⚡ Mengunggah: " . basename($local) . " -> ftp://{$ftp_host}/{$remote} ...\n";

    $ch = curl_init();
    $fp = fopen($local, 'r');

    curl_setopt($ch, CURLOPT_URL, "ftp://{$ftp_host}/{$remote}");
    curl_setopt($ch, CURLOPT_USERPWD, "{$ftp_user}:{$ftp_pass}");
    curl_setopt($ch, CURLOPT_UPLOAD, 1);
    curl_setopt($ch, CURLOPT_INFILE, $fp);
    curl_setopt($ch, CURLOPT_INFILESIZE, filesize($local));
    curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 minutes timeout

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "❌ GAGAL Mengunggah: " . curl_error($ch) . "\n";
        curl_close($ch);
        fclose($fp);
        exit;
    } else {
        echo "✅ BERHASIL Mengunggah!\n\n";
    }

    curl_close($ch);
    fclose($fp);
}

echo "🎉 UPLOAD SELESAI DENGAN SUKSES!\n";
echo "🔗 Silakan buka browser Anda dan akses: http://panggonanresto.com/installer.php\n";
