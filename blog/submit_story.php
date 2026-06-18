<?php
/**
 * Panggonan Resto — Handler Submit Cerita Pelanggan
 * Menerima form POST dari blog/index.php, menyimpan ke MySQL dengan status 'pending',
 * lalu redirect ke WhatsApp + halaman sukses.
 */

session_start();
require_once __DIR__ . '/../config/db.php';

// Detect if post size exceeds PHP post_max_size limit (which results in empty $_POST and $_FILES)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
    $_SESSION['story_upload_error'] = 'Foto gagal diunggah karena ukurannya melebihi batas kapasitas server PHP (coba kompres foto di bawah 2MB).';
    // We cannot preserve form text input because $_POST was completely wiped out by PHP due to post_max_size limit
    header('Location: index.php?error=1#panggonan-story-form');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$nama = trim($_POST['nama'] ?? '');
$ig = trim($_POST['ig'] ?? '');
$pesan_raw = trim($_POST['pesan'] ?? '');

if (empty($nama) || empty($pesan_raw)) {
    header('Location: index.php');
    exit;
}

// Length validation to protect database
$nama = mb_substr($nama, 0, 255);
$ig = mb_substr($ig, 0, 255);
$pesan_raw = mb_substr($pesan_raw, 0, 65000);

// 1. SECURITY: XSS SANITATION FOR WYSIWYG INPUT
// Allow only safe basic formatting tags
$allowed_tags = '<p><br><strong><b><i><em><u><ul><ol><li>';
$pesan_stripped = strip_tags($pesan_raw, $allowed_tags);

// Strip ALL attributes from the allowed tags to prevent onclick="", style="", etc.
$text = preg_replace('/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i', '<$1$2>', $pesan_stripped);

$plain_text = strip_tags($text);
$quote = '"' . mb_substr($plain_text, 0, 80) . (mb_strlen($plain_text) > 80 ? '...' : '') . '"';

$date_label = (!empty($ig) && $ig !== 'Tidak ada') ? '@' . ltrim($ig, '@') : 'Kiriman Pelanggan';

// 2. SECURITY: SECURE FILE UPLOAD HANDLING
$media_url = '';
$media_type = 'image';
$upload_error = '';

if (isset($_FILES['foto_file']) && $_FILES['foto_file']['name'] !== '') {
    $file = $_FILES['foto_file'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $upload_error = 'Foto gagal diunggah karena ukurannya melebihi batas kapasitas server PHP (coba kompres foto di bawah 2MB).';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $upload_error = 'Foto melebihi batas ukuran form HTML.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $upload_error = 'Pengunggahan foto terputus di tengah jalan. Coba kirim ulang.';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $upload_error = 'Folder temporary PHP tidak ditemukan di server.';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $upload_error = 'Server gagal menulis file ke disk. Periksa izin folder uploads cPanel.';
                break;
            default:
                $upload_error = 'Gagal mengunggah foto karena kesalahan server (Error Code: ' . $file['error'] . ').';
        }
    } else {
        // Strict MIME type checking using FileInfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mimes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'video/mp4' => 'mp4'
        ];

        // 2MB strict limit requested by user for the PHP upload size alignment
        $max_size = 2 * 1024 * 1024; 

        if (!array_key_exists($mime, $allowed_mimes)) {
            $upload_error = 'Format file tidak diizinkan. Hanya menerima JPG, PNG, WEBP, atau MP4.';
        } elseif ($file['size'] > $max_size) {
            $upload_error = 'Ukuran file melebihi batas maksimal 2MB. Silakan kompres foto Anda terlebih dahulu.';
        } else {
            $ext = $allowed_mimes[$mime];
            // Generate random safe filename
            $filename = uniqid('story_', true) . '.' . $ext;
            $upload_dir = __DIR__ . '/../assets/uploads/jurnal/';
            
            // Ensure directory exists
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    $upload_error = 'Gagal membuat folder tujuan pengunggahan. Periksa izin akses folder assets/uploads/.';
                }
            }
            
            if (empty($upload_error)) {
                $dest_path = $upload_dir . $filename;
                if (move_uploaded_file($file['tmp_name'], $dest_path)) {
                    $media_url = 'assets/uploads/jurnal/' . $filename;
                    $media_type = strpos($mime, 'video') === 0 ? 'video' : 'image';
                } else {
                    $upload_error = 'Gagal memindahkan file ke folder tujuan. Periksa permission folder jurnal/.';
                }
            }
        }
    }
}

// If upload/validation has failed, abort DB entry and preserve input data
if (!empty($upload_error)) {
    $_SESSION['story_upload_error'] = $upload_error;
    $_SESSION['story_form_data'] = [
        'nama' => $nama,
        'ig' => $ig,
        'pesan' => $pesan_raw
    ];
    header('Location: index.php?error=1#panggonan-story-form');
    exit;
}

// Save to database as pending
$stmt = $pdo->prepare("INSERT INTO journals (author, quote, text, media_type, media_url, date_label, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
$stmt->execute([$nama, $quote, $text, $media_type, $media_url, $date_label]);

// Clean up form retention variables only after successful insert
unset($_SESSION['story_form_data']);
unset($_SESSION['story_upload_error']);

// Store success data in session for display
$_SESSION['story_submitted'] = true;

header('Location: index.php?submitted=1#panggonan-story-form');
exit;
