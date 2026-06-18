<?php
$host = "flanders.id.rapidplex.com";
$username = "panggona";
$password = "75CP-l2USagg+3";
$remote_base = "/public_html";

$files = [
    'index.html' => 'index.html',
    'about-us/index.html' => 'about-us/index.html',
    'menu/index.php' => 'menu/index.php',
    'services/index.html' => 'services/index.html',
    'gallery/index.php' => 'gallery/index.php',
    'faq/index.html' => 'faq/index.html',
    'contact-us/index.php' => 'contact-us/index.php',
    'blog/post.php' => 'blog/post.php',
    'blog/index.php' => 'blog/index.php',
    'assets/css/custom.css' => 'assets/css/custom.css',
];

echo "=============================================\n";
echo " Panggonan Resto — Mengunduh File dari Live cPanel\n";
echo "=============================================\n\n";

foreach ($files as $local_path => $remote_path) {
    $full_local = __DIR__ . '/../' . $local_path;
    $dir = dirname($full_local);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    echo "Mengunduh $remote_path dari Live Server...\n";
    
    // Remote FTP URL
    $ftp_url = "ftp://{$host}{$remote_base}/{$remote_path}";
    
    // Build curl.exe command safely to download
    $cmd = 'curl.exe -u ' . escapeshellarg($username) . ':' . escapeshellarg($password) . ' -o ' . escapeshellarg($full_local) . ' ' . escapeshellarg($ftp_url);
    
    $output = [];
    $retval = -1;
    exec($cmd, $output, $retval);
    
    if ($retval === 0) {
        echo "  ✅ Sukses diunduh!\n\n";
    } else {
        echo "  ❌ Gagal mengunduh (Exit Code: $retval)\n\n";
    }
}

echo "=============================================\n";
echo " Unduh Selesai!\n";
echo "=============================================\n";
