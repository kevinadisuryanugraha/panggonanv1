<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// ============================================
// SECURITY: Prevent browser caching of authenticated pages
// ============================================
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');

// ============================================
// ADMIN DB AUTH & SESSION MANAGEMENT
// ============================================

$login_error = '';
$toast_message = '';
$toast_type = 'success';

// Handle Logout — FULL session cleanup
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Unset all session variables
    $_SESSION = [];

    // Delete the session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }

    // Destroy the session
    session_destroy();

    header('Location: index.php');
    exit;
}

// Handle Login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            $_SESSION['admin_logged'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['login_time'] = time();
            $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header('Location: index.php');
            exit;
        } else {
            $login_error = 'Username atau kata sandi salah! Silakan coba lagi.';
        }
    } else {
        $login_error = 'Harap isi username dan kata sandi.';
    }
}

// ============================================
// SESSION TIMEOUT: Auto-logout after 2 hours
// ============================================
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    $timeout = 2 * 60 * 60; // 2 hours
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $timeout) {
        $_SESSION = [];
        session_destroy();
        header('Location: index.php?timeout=1');
        exit;
    }
    // Refresh login time on activity
    $_SESSION['login_time'] = time();
}

// ============================================
// CSRF HELPER
// ============================================
function validateCsrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ============================================
// PROTECTED ACTIONS (require login)
// ============================================
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {

    // CSRF check for all state-changing POST requests (except login)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['admin_login'])) {
        $submittedToken = $_POST['csrf_token'] ?? '';
        if (!validateCsrf($submittedToken)) {
            $_SESSION['toast'] = 'Token keamanan tidak valid. Silakan refresh halaman dan coba lagi.';
            $_SESSION['toast_type'] = 'error';
            header('Location: index.php?tab=' . ($_GET['tab'] ?? 'dashboard'));
            exit;
        }
    }

    // --- CONFIRM RESERVATION ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_reservation_confirm'])) {
        $id = (int) $_POST['reservation_id'];
        $stmt = $pdo->prepare("UPDATE reservations SET status = 'confirmed' WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['toast'] = 'Reservasi berhasil dikonfirmasi! 💚';
        $_SESSION['toast_type'] = 'success';
        header('Location: index.php?tab=reservations');
        exit;
    }

    // --- CANCEL RESERVATION ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_reservation_cancel'])) {
        $id = (int) $_POST['reservation_id'];
        $stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['toast'] = 'Reservasi telah dibatalkan.';
        $_SESSION['toast_type'] = 'error';
        header('Location: index.php?tab=reservations');
        exit;
    }

    // --- DELETE RESERVATION ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_reservation_delete'])) {
        $id = (int) $_POST['reservation_id'];
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['toast'] = 'Reservasi berhasil dihapus permanen.';
        $_SESSION['toast_type'] = 'error';
        header('Location: index.php?tab=reservations');
        exit;
    }

    // --- EDIT RESERVATION ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_reservation_edit'])) {
        $id = (int) $_POST['reservation_id'];
        $name = trim($_POST['edit_res_name'] ?? '');
        $phone = trim($_POST['edit_res_phone'] ?? '');
        $branch = trim($_POST['edit_res_branch'] ?? '');
        $event_type = trim($_POST['edit_res_event'] ?? '');
        $date = trim($_POST['edit_res_date'] ?? '');
        $time = trim($_POST['edit_res_time'] ?? '');
        $pax = (int) ($_POST['edit_res_pax'] ?? 0);
        $note = strip_tags(trim($_POST['edit_res_note'] ?? ''));

        if ($pax < 1) $pax = 1; // Basic safety

        $stmt = $pdo->prepare("UPDATE reservations SET name=?, phone=?, branch=?, event_type=?, reservation_date=?, reservation_time=?, pax=?, note=? WHERE id=?");
        $stmt->execute([$name, $phone, $branch, $event_type, $date, $time, $pax, $note, $id]);

        $_SESSION['toast'] = 'Data reservasi berhasil diperbarui.';
        $_SESSION['toast_type'] = 'success';
        header('Location: index.php?tab=reservations');
        exit;
    }

    // --- APPROVE ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_approve'])) {
        $id = (int) $_POST['journal_id'];
        $stmt = $pdo->prepare("UPDATE journals SET status = 'approved' WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['toast'] = 'Cerita berhasil disetujui & diterbitkan! ✨';
        $_SESSION['toast_type'] = 'success';
        header('Location: index.php?tab=approvals');
        exit;
    }

    // --- DELETE ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_delete'])) {
        $id = (int) $_POST['journal_id'];
        // Get media URL to delete file if it's an upload
        $stmt = $pdo->prepare("SELECT media_url FROM journals WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && strpos($row['media_url'], 'assets/uploads/') !== false) {
            $filepath = __DIR__ . '/../' . $row['media_url'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
        $stmt = $pdo->prepare("DELETE FROM journals WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['toast'] = 'Jurnal berhasil dihapus dari sistem.';
        $_SESSION['toast_type'] = 'error';
        header('Location: index.php?tab=' . ($_GET['tab'] ?? 'dashboard'));
        exit;
    }

    // --- EDIT (Save) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_edit'])) {
        $id = (int) $_POST['journal_id'];
        $author = trim($_POST['edit_author']);
        $date_label = trim($_POST['edit_date']);
        $allowed_tags = '<p><br><strong><b><i><em><u><ul><ol><li>';
        $text = strip_tags(trim($_POST['edit_text']), $allowed_tags);
        $text = preg_replace('/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i', '<$1$2>', $text);
        $plain_text = strip_tags($text);
        $quote = '"' . mb_substr($plain_text, 0, 80) . (mb_strlen($plain_text) > 80 ? '...' : '') . '"';
        $media_type = $_POST['edit_media_type'];

        // Check if a new file was uploaded
        $media_url = $_POST['edit_existing_media_url']; // keep existing by default
        if (isset($_FILES['edit_media_file']) && $_FILES['edit_media_file']['error'] === UPLOAD_ERR_OK) {
            $media_url = handleFileUpload($_FILES['edit_media_file'], $media_type);
            if ($media_url === false) {
                $_SESSION['toast'] = 'Gagal mengunggah file. Format tidak didukung atau ukuran terlalu besar.';
                $_SESSION['toast_type'] = 'error';
                header('Location: index.php?tab=active');
                exit;
            }
        }

        $stmt = $pdo->prepare("UPDATE journals SET author=?, quote=?, text=?, media_type=?, media_url=?, date_label=? WHERE id=?");
        $stmt->execute([$author, $quote, $text, $media_type, $media_url, $date_label, $id]);
        $_SESSION['toast'] = 'Perubahan berhasil disimpan! Jurnal telah diperbarui.';
        $_SESSION['toast_type'] = 'success';
        header('Location: index.php?tab=active');
        exit;
    }

    // --- CREATE ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_create'])) {
        $author = mb_substr(trim($_POST['create_author']), 0, 255);
        $date_label = mb_substr(trim($_POST['create_date']), 0, 255);
        $allowed_tags = '<p><br><strong><b><i><em><u><ul><ol><li>';
        $text = strip_tags(trim($_POST['create_text']), $allowed_tags);
        $text = preg_replace('/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i', '<$1$2>', $text);
        $text = mb_substr($text, 0, 65000);
        $plain_text = strip_tags($text);
        $quote = '"' . mb_substr($plain_text, 0, 80) . (mb_strlen($plain_text) > 80 ? '...' : '') . '"';
        $media_type = $_POST['create_media_type'];

        $media_url = '';
        if (isset($_FILES['create_media_file']) && $_FILES['create_media_file']['error'] === UPLOAD_ERR_OK) {
            $media_url = handleFileUpload($_FILES['create_media_file'], $media_type);
            if ($media_url === false) {
                $_SESSION['toast'] = 'Gagal mengunggah file media. Periksa format dan ukurannya.';
                $_SESSION['toast_type'] = 'error';
                header('Location: index.php?tab=create');
                exit;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO journals (author, quote, text, media_type, media_url, date_label, status) VALUES (?, ?, ?, ?, ?, ?, 'approved')");
        $stmt->execute([$author, $quote, $text, $media_type, $media_url, $date_label]);
        $_SESSION['toast'] = 'Jurnal kustom baru berhasil diterbitkan langsung ke publik! ✨';
        $_SESSION['toast_type'] = 'success';
        header('Location: index.php?tab=dashboard');
        exit;
    }

    // --- EXPORT JSON ---
    if (isset($_GET['action']) && $_GET['action'] === 'export') {
        $stmt = $pdo->query("SELECT * FROM journals ORDER BY id DESC");
        $data = $stmt->fetchAll();
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="panggonan_journals_backup_' . time() . '.json"');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // --- IMPORT JSON ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_import'])) {
        if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] === UPLOAD_ERR_OK) {
            $content = file_get_contents($_FILES['import_file']['tmp_name']);
            $imported = json_decode($content, true);
            if (is_array($imported) && count($imported) > 0) {
                // Clear existing data
                $pdo->exec("TRUNCATE TABLE journals");
                $stmt = $pdo->prepare("INSERT INTO journals (author, quote, text, media_type, media_url, date_label, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                foreach ($imported as $item) {
                    $stmt->execute([
                        $item['author'] ?? '',
                        $item['quote'] ?? '',
                        $item['text'] ?? '',
                        $item['media_type'] ?? 'image',
                        $item['media_url'] ?? '',
                        $item['date_label'] ?? '',
                        $item['status'] ?? 'approved',
                    ]);
                }
                $_SESSION['toast'] = 'Database berhasil diimpor & dipulihkan sepenuhnya!';
                $_SESSION['toast_type'] = 'success';
            } else {
                $_SESSION['toast'] = 'Format file JSON tidak valid.';
                $_SESSION['toast_type'] = 'error';
            }
        }
        header('Location: index.php');
        exit;
    }

    // ============================================
    // MENU CATEGORY CRUD ACTIONS
    // ============================================
    
    // --- ADD CATEGORY ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_category_add'])) {
        $name = trim($_POST['name'] ?? '');
        $column = trim($_POST['column_position'] ?? 'left');
        $sort = (int)($_POST['sort_order'] ?? 0);
        
        if (!empty($name)) {
            $stmt = $pdo->prepare("INSERT INTO `menu_categories` (name, column_position, sort_order) VALUES (?, ?, ?)");
            $stmt->execute([$name, $column, $sort]);
            $_SESSION['toast'] = 'Kategori baru berhasil ditambahkan! 📁';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['toast'] = 'Nama kategori tidak boleh kosong.';
            $_SESSION['toast_type'] = 'error';
        }
        header('Location: index.php?tab=categories');
        exit;
    }

    // --- EDIT CATEGORY ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_category_edit'])) {
        $id = (int)$_POST['category_id'];
        $name = trim($_POST['name'] ?? '');
        $column = trim($_POST['column_position'] ?? 'left');
        $sort = (int)($_POST['sort_order'] ?? 0);
        
        if ($id > 0 && !empty($name)) {
            $stmt = $pdo->prepare("UPDATE `menu_categories` SET name = ?, column_position = ?, sort_order = ? WHERE id = ?");
            $stmt->execute([$name, $column, $sort, $id]);
            $_SESSION['toast'] = 'Perubahan kategori berhasil disimpan!';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['toast'] = 'Gagal menyimpan. Nama kategori tidak boleh kosong.';
            $_SESSION['toast_type'] = 'error';
        }
        header('Location: index.php?tab=categories');
        exit;
    }

    // --- DELETE CATEGORY ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_category_delete'])) {
        $id = (int)$_POST['category_id'];
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM `menu_categories` WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['toast'] = 'Kategori beserta seluruh menunya berhasil dihapus.';
            $_SESSION['toast_type'] = 'error';
        }
        header('Location: index.php?tab=categories');
        exit;
    }

    // ============================================
    // MENU ITEM CRUD ACTIONS
    // ============================================
    
    // --- ADD MENU ITEM ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_menu_item_add'])) {
        $cat_id = (int)($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $price = (int)($_POST['price'] ?? 0);
        $avail = isset($_POST['is_available']) ? 1 : 0;
        $sort = (int)($_POST['sort_order'] ?? 0);
        
        if ($cat_id > 0 && !empty($name)) {
            $stmt = $pdo->prepare("INSERT INTO `menu_items` (category_id, name, description, price, is_available, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cat_id, $name, empty($desc) ? null : $desc, $price, $avail, $sort]);
            $_SESSION['toast'] = 'Item menu baru berhasil ditambahkan! 🍳';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['toast'] = 'Nama menu dan kategori wajib diisi.';
            $_SESSION['toast_type'] = 'error';
        }
        header('Location: index.php?tab=menu');
        exit;
    }

    // --- EDIT MENU ITEM ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_menu_item_edit'])) {
        $id = (int)$_POST['item_id'];
        $cat_id = (int)($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $price = (int)($_POST['price'] ?? 0);
        $avail = isset($_POST['is_available']) ? 1 : 0;
        $sort = (int)($_POST['sort_order'] ?? 0);
        
        if ($id > 0 && $cat_id > 0 && !empty($name)) {
            $stmt = $pdo->prepare("UPDATE `menu_items` SET category_id = ?, name = ?, description = ?, price = ?, is_available = ?, sort_order = ? WHERE id = ?");
            $stmt->execute([$cat_id, $name, empty($desc) ? null : $desc, $price, $avail, $sort, $id]);
            $_SESSION['toast'] = 'Perubahan item menu berhasil disimpan!';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['toast'] = 'Gagal menyimpan. Harap isi data dengan lengkap.';
            $_SESSION['toast_type'] = 'error';
        }
        header('Location: index.php?tab=menu');
        exit;
    }

    // --- DELETE MENU ITEM ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_menu_item_delete'])) {
        $id = (int)$_POST['item_id'];
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM `menu_items` WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['toast'] = 'Item menu berhasil dihapus dari sistem.';
            $_SESSION['toast_type'] = 'error';
        }
        header('Location: index.php?tab=menu');
        exit;
    }

    // ============================================
    // GALLERY CATEGORY CRUD ACTIONS
    // ============================================
    
    // --- ADD GALLERY CATEGORY ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_gallery_category_add'])) {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $sort = (int)($_POST['sort_order'] ?? 0);
        
        if (!empty($name) && !empty($slug)) {
            // Slug sanitization: lowercase, replace spaces with hyphen
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', str_replace(' ', '-', $slug)));
            $stmt = $pdo->prepare("INSERT INTO `gallery_categories` (name, slug, sort_order) VALUES (?, ?, ?)");
            $stmt->execute([$name, $slug, $sort]);
            $_SESSION['toast'] = 'Kategori galeri baru berhasil ditambahkan! 📁';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['toast'] = 'Nama dan slug kategori wajib diisi.';
            $_SESSION['toast_type'] = 'error';
        }
        header('Location: index.php?tab=gallery');
        exit;
    }

    // --- EDIT GALLERY CATEGORY ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_gallery_category_edit'])) {
        $id = (int)$_POST['category_id'];
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $sort = (int)($_POST['sort_order'] ?? 0);
        
        if ($id > 0 && !empty($name) && !empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', str_replace(' ', '-', $slug)));
            $stmt = $pdo->prepare("UPDATE `gallery_categories` SET name = ?, slug = ?, sort_order = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $sort, $id]);
            $_SESSION['toast'] = 'Perubahan kategori galeri berhasil disimpan!';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['toast'] = 'Gagal menyimpan. Harap isi data dengan lengkap.';
            $_SESSION['toast_type'] = 'error';
        }
        header('Location: index.php?tab=gallery');
        exit;
    }

    // --- DELETE GALLERY CATEGORY ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_gallery_category_delete'])) {
        $id = (int)$_POST['category_id'];
        if ($id > 0) {
            // Get all images under this category to delete physical files
            $stmt = $pdo->prepare("SELECT image_url FROM `gallery` WHERE category_id = ?");
            $stmt->execute([$id]);
            $images = $stmt->fetchAll();
            foreach ($images as $img) {
                if (strpos($img['image_url'], 'assets/uploads/') !== false) {
                    $filepath = __DIR__ . '/../' . $img['image_url'];
                    if (file_exists($filepath)) {
                        unlink($filepath);
                    }
                }
            }
            
            // Delete category (cascade will delete from gallery table in DB)
            $stmt = $pdo->prepare("DELETE FROM `gallery_categories` WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['toast'] = 'Kategori galeri beserta seluruh fotonya berhasil dihapus.';
            $_SESSION['toast_type'] = 'error';
        }
        header('Location: index.php?tab=gallery');
        exit;
    }

    // ============================================
    // GALLERY ITEM CRUD ACTIONS
    // ============================================

    // --- ADD GALLERY ITEM ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_gallery_add'])) {
        $title = trim($_POST['title'] ?? '');
        $cat_id = (int)($_POST['category_id'] ?? 0);
        
        if (!empty($title) && $cat_id > 0) {
            if (isset($_FILES['gallery_file']) && $_FILES['gallery_file']['error'] === UPLOAD_ERR_OK) {
                $image_url = handleGalleryUpload($_FILES['gallery_file']);
                if ($image_url !== false) {
                    $stmt = $pdo->prepare("INSERT INTO `gallery` (title, category_id, image_url) VALUES (?, ?, ?)");
                    $stmt->execute([$title, $cat_id, $image_url]);
                    $_SESSION['toast'] = 'Foto baru berhasil diunggah! 📸';
                    $_SESSION['toast_type'] = 'success';
                } else {
                    $_SESSION['toast'] = 'Gagal mengunggah gambar. Format tidak didukung atau melebihi batas ukuran.';
                    $_SESSION['toast_type'] = 'error';
                }
            } else {
                $_SESSION['toast'] = 'Harap pilih file gambar untuk diunggah.';
                $_SESSION['toast_type'] = 'error';
            }
        } else {
            $_SESSION['toast'] = 'Judul dan kategori wajib diisi.';
            $_SESSION['toast_type'] = 'error';
        }
        header('Location: index.php?tab=gallery');
        exit;
    }

    // --- EDIT GALLERY ITEM ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_gallery_edit'])) {
        $id = (int)$_POST['item_id'];
        $title = trim($_POST['title'] ?? '');
        $cat_id = (int)($_POST['category_id'] ?? 0);
        
        if ($id > 0 && !empty($title) && $cat_id > 0) {
            $stmt = $pdo->prepare("UPDATE `gallery` SET title = ?, category_id = ? WHERE id = ?");
            $stmt->execute([$title, $cat_id, $id]);
            $_SESSION['toast'] = 'Perubahan foto berhasil disimpan!';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['toast'] = 'Gagal menyimpan. Judul dan kategori tidak boleh kosong.';
            $_SESSION['toast_type'] = 'error';
        }
        header('Location: index.php?tab=gallery');
        exit;
    }

    // --- DELETE GALLERY ITEM ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_gallery_delete'])) {
        $id = (int)$_POST['item_id'];
        if ($id > 0) {
            $stmt = $pdo->prepare("SELECT image_url FROM `gallery` WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row) {
                if (strpos($row['image_url'], 'assets/uploads/') !== false) {
                    $filepath = __DIR__ . '/../' . $row['image_url'];
                    if (file_exists($filepath)) {
                        unlink($filepath);
                    }
                }
                $stmt = $pdo->prepare("DELETE FROM `gallery` WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['toast'] = 'Foto berhasil dihapus dari galeri.';
                $_SESSION['toast_type'] = 'error';
            }
        }
        header('Location: index.php?tab=gallery');
        exit;
    }
}

// ============================================
// FILE UPLOAD HANDLER
// ============================================
function handleFileUpload($file, $media_type) {
    $allowed_image = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png'];
    $allowed_video = ['video/mp4', 'video/webm'];
    $max_size = 50 * 1024 * 1024; // 50MB

    $allowed_mimes = [
        'image/webp' => 'webp',
        'image/jpeg' => 'jpg',
        'image/jpg' => 'jpg',
        'image/png' => 'png',
        'video/mp4' => 'mp4',
        'video/webm' => 'webm'
    ];
    
    $mime = mime_content_type($file['tmp_name']);
    
    if ($media_type === 'video') {
        if (!in_array($mime, $allowed_video)) return false;
    } else {
        if (!in_array($mime, $allowed_image)) return false;
    }

    if ($file['size'] > $max_size) return false;

    $ext = isset($allowed_mimes[$mime]) ? $allowed_mimes[$mime] : pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'jurnal_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
    $dest_dir = __DIR__ . '/../assets/uploads/jurnal/';

    if (!is_dir($dest_dir)) {
        mkdir($dest_dir, 0755, true);
    }

    $dest_path = $dest_dir . $filename;
    if (move_uploaded_file($file['tmp_name'], $dest_path)) {
        return 'assets/uploads/jurnal/' . $filename;
    }
    return false;
}

function handleGalleryUpload($file) {
    $allowed_image = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png'];
    $max_size = 10 * 1024 * 1024; // 10MB limit

    $allowed_mimes = [
        'image/webp' => 'webp',
        'image/jpeg' => 'jpg',
        'image/jpg' => 'jpg',
        'image/png' => 'png'
    ];
    
    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, $allowed_image)) return false;
    if ($file['size'] > $max_size) return false;

    $ext = isset($allowed_mimes[$mime]) ? $allowed_mimes[$mime] : pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'gallery_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
    $dest_dir = __DIR__ . '/../assets/uploads/gallery/';

    if (!is_dir($dest_dir)) {
        mkdir($dest_dir, 0755, true);
    }

    $dest_path = $dest_dir . $filename;
    if (move_uploaded_file($file['tmp_name'], $dest_path)) {
        return 'assets/uploads/gallery/' . $filename;
    }
    return false;
}

// ============================================
// FETCH DATA FOR DASHBOARD VIEWS
// ============================================
$active_count = 0;
$pending_count = 0;
$pending_list = [];
$active_list = [];
$pending_res_count = 0;
$res_list = [];

if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    try {
        $active_count = $pdo->query("SELECT COUNT(*) FROM journals WHERE status = 'approved'")->fetchColumn();
        $pending_count = $pdo->query("SELECT COUNT(*) FROM journals WHERE status = 'pending'")->fetchColumn();
        $pending_list = $pdo->query("SELECT * FROM journals WHERE status = 'pending' ORDER BY created_at DESC")->fetchAll();
        $active_list = $pdo->query("SELECT * FROM journals WHERE status = 'approved' ORDER BY id DESC")->fetchAll();
        $pending_res_count = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'pending'")->fetchColumn();
        $res_list = $pdo->query("SELECT * FROM reservations ORDER BY reservation_date DESC, id DESC")->fetchAll();
        
        // Fetch Menu Categories & Relational Menu Items
        $categories_list = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM `menu_items` m WHERE m.category_id = c.id) as item_count FROM `menu_categories` c ORDER BY c.column_position ASC, c.sort_order ASC")->fetchAll();
        $menu_items_list = $pdo->query("SELECT m.*, c.name as category_name, c.column_position FROM `menu_items` m JOIN `menu_categories` c ON m.category_id = c.id ORDER BY c.column_position ASC, c.sort_order ASC, m.sort_order ASC")->fetchAll();
        
        // Fetch Gallery Items & Categories
        $gallery_categories_list = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM `gallery` g WHERE g.category_id = c.id) as item_count FROM `gallery_categories` c ORDER BY c.sort_order ASC, c.id ASC")->fetchAll();
        $gallery_list = $pdo->query("SELECT g.*, c.name as category_name, c.slug as category_slug FROM `gallery` g JOIN `gallery_categories` c ON g.category_id = c.id ORDER BY g.id DESC")->fetchAll();
    } catch (Exception $e) {
        error_log("Panggonan Admin Dashboard DB Error: " . $e->getMessage());
        // All variables remain at their default empty values
    }
} else {
    $categories_list = [];
    $menu_items_list = [];
    $gallery_categories_list = [];
    $gallery_list = [];
}

// Toast from session
if (isset($_SESSION['toast'])) {
    $toast_message = $_SESSION['toast'];
    $toast_type = $_SESSION['toast_type'] ?? 'success';
    unset($_SESSION['toast'], $_SESSION['toast_type']);
}

$current_tab = $_GET['tab'] ?? 'dashboard';
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <title>Panggonan Jurnal Management — Dasbor Admin</title>
  <meta content="width=device-width, initial-scale=1" name="viewport" />
  <meta name="robots" content="noindex, nofollow" />
  <link href="https://fonts.googleapis.com" rel="preconnect" />
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous" />
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <?php if ($current_tab === 'traffic'): ?>
    <style>
      <?php include __DIR__ . '/../assets/css/dashboard.css'; ?>
    </style>
  <?php endif; ?>

  <style>
    /* DATATABLES DARK THEME OVERRIDES */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_processing,
    .dataTables_wrapper .dataTables_paginate {
      color: var(--text-muted) !important;
      font-family: var(--font-inter);
      font-size: 0.9rem;
      margin-bottom: 16px;
      margin-top: 16px;
    }
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
      background-color: var(--bg-input);
      border: 1px solid var(--border-color);
      color: var(--text-white);
      border-radius: 6px;
      padding: 6px 12px;
      outline: none;
      margin-left: 8px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
      color: var(--text-white) !important;
      border: 1px solid transparent !important;
      border-radius: 6px !important;
      padding: 6px 12px !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
      background: var(--bg-input) !important;
      border-color: var(--border-color) !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
      background: var(--primary-gold) !important;
      color: var(--bg-main) !important;
      border-color: var(--primary-gold) !important;
      font-weight: 600;
    }
    table.dataTable.no-footer {
      border-bottom: 1px solid var(--border-color) !important;
    }
    table.dataTable thead th, table.dataTable thead td {
      border-bottom: 1px solid var(--border-color) !important;
    }
    .table-responsive { padding: 0 32px 24px 32px; }
    /* VARIABLES & RESET */
    :root {
      --bg-main: #0c0e17;
      --bg-sidebar: #121622;
      --bg-card: #181c28;
      --bg-input: #1f2434;
      --primary-gold: #d4af37;
      --primary-gold-hover: #bda031;
      --text-white: #ffffff;
      --text-muted: #8e95a5;
      --accent-blue: #5465ff;
      --accent-green: #00e676;
      --accent-red: #ff3d71;
      --border-color: rgba(255, 255, 255, 0.08);
      --font-sora: 'Sora', sans-serif;
      --font-inter: 'Inter', sans-serif;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background-color: var(--bg-main);
      color: var(--text-white);
      font-family: var(--font-inter);
      overflow-x: hidden;
      -webkit-font-smoothing: antialiased;
    }

    /* LOGIN SCREEN */
    .login-container {
      position: fixed; inset: 0;
      background: radial-gradient(circle at center, #1b2030 0%, #0c0e17 100%);
      display: flex; align-items: center; justify-content: center;
      z-index: 9999;
    }
    .login-card {
      background: rgba(24, 28, 40, 0.7); backdrop-filter: blur(16px);
      border: 1px solid var(--border-color); border-radius: 20px;
      padding: 48px; width: 100%; max-width: 420px; text-align: center;
      box-shadow: 0 24px 80px rgba(0, 0, 0, 0.5);
    }
    .login-logo {
      width: 80px; height: 80px; border-radius: 50%;
      background: var(--bg-sidebar); border: 2px solid var(--primary-gold);
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 24px auto;
    }
    .login-logo h2 { color: var(--primary-gold); font-family: var(--font-sora); font-size: 2rem; }
    .login-card h3 { font-family: var(--font-sora); font-size: 1.5rem; margin-bottom: 8px; font-weight: 700; }
    .login-card p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 32px; }
    .form-group { text-align: left; margin-bottom: 24px; }
    .form-label { display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; }
    .form-input {
      width: 100%; background: var(--bg-input); border: 1px solid var(--border-color);
      border-radius: 12px; padding: 14px 16px; color: var(--text-white);
      font-family: inherit; font-size: 0.95rem; transition: all 0.3s;
    }
    .form-input:focus { outline: none; border-color: var(--primary-gold); box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15); }
    .btn-gold {
      width: 100%; background: var(--primary-gold); color: #0c0e17; border: none;
      border-radius: 12px; padding: 16px; font-family: var(--font-sora);
      font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: all 0.3s;
    }
    .btn-gold:hover { background: var(--primary-gold-hover); box-shadow: 0 8px 24px rgba(212, 175, 55, 0.25); }
    .login-error { color: var(--accent-red); font-size: 0.85rem; margin-top: 16px; }

    /* MAIN APP LAYOUT */
    .app-layout { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }

    /* SIDEBAR */
    .sidebar {
      background-color: var(--bg-sidebar); border-right: 1px solid var(--border-color);
      padding: 32px 24px; display: flex; flex-direction: column; height: 100vh;
      position: sticky; top: 0;
      overflow-y: auto;
      scrollbar-width: none; /* Firefox */
      -ms-overflow-style: none; /* IE/Edge */
    }
    .sidebar::-webkit-scrollbar {
      display: none; /* Chrome/Safari/Opera */
    }
    .sidebar-header { display: flex; align-items: center; gap: 12px; margin-bottom: 48px; }
    .sidebar-logo {
      width: 42px; height: 42px; border-radius: 50%; background: var(--bg-main);
      border: 1.5px solid var(--primary-gold); display: flex; align-items: center;
      justify-content: center; flex-shrink: 0;
    }
    .sidebar-logo span { color: var(--primary-gold); font-family: var(--font-sora); font-weight: 800; font-size: 1.1rem; }
    .sidebar-title-wrap h1 { font-family: var(--font-sora); font-size: 1rem; font-weight: 700; line-height: 1.2; }
    .sidebar-title-wrap span { font-size: 0.75rem; color: var(--text-muted); }

    .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 8px; flex-grow: 1; }
    .menu-item {
      display: flex; align-items: center; gap: 14px; padding: 14px 16px;
      border-radius: 12px; color: var(--text-muted); text-decoration: none;
      font-weight: 500; font-size: 0.92rem; cursor: pointer;
      position: relative;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .menu-item:hover {
      color: var(--text-white);
      background: rgba(255, 255, 255, 0.05);
    }
    .menu-item:hover svg {
      transform: scale(1.06);
    }
    .menu-item.active {
      background: linear-gradient(90deg, rgba(212, 175, 55, 0.12) 0%, rgba(212, 175, 55, 0.02) 100%);
      color: var(--primary-gold);
      font-weight: 600;
    }
    .menu-item.active:hover {
      background: linear-gradient(90deg, rgba(212, 175, 55, 0.16) 0%, rgba(212, 175, 55, 0.04) 100%);
      color: var(--primary-gold);
    }
    .menu-item.active::before {
      content: '';
      position: absolute;
      left: 0;
      top: 12px;
      bottom: 12px;
      width: 4px;
      background-color: var(--primary-gold);
      border-radius: 0 4px 4px 0;
    }
    .menu-item svg {
      width: 20px;
      height: 20px;
      flex-shrink: 0;
      transition: transform 0.2s ease;
    }
    .menu-item svg:not([fill="none"]) {
      fill: currentColor;
    }
    .menu-badge { margin-left: auto; background: var(--accent-red); color: #fff; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 20px; }
    .sidebar-footer { border-top: 1px solid var(--border-color); padding-top: 24px; display: flex; flex-direction: column; gap: 8px; }
    .sidebar-footer .menu-item { padding: 10px 16px; }

    /* MAIN CONTENT */
    .main-content { padding: 40px; overflow-y: auto; height: 100vh; }

    /* TOP BAR */
    .top-bar {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 40px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;
    }
    .top-bar-left h2 { font-family: var(--font-sora); font-size: 1.8rem; font-weight: 700; display: flex; align-items: center; gap: 16px; }
    .sync-status { font-size: 0.8rem; color: var(--accent-green); display: flex; align-items: center; gap: 8px; font-family: var(--font-inter); font-weight: 500; }
    .sync-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--accent-green); box-shadow: 0 0 10px var(--accent-green); animation: pulse 1.8s infinite; }
    @keyframes pulse {
      0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 230, 118, 0.7); }
      70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(0, 230, 118, 0); }
      100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 230, 118, 0); }
    }
    .top-bar-right { display: flex; align-items: center; gap: 16px; }
    .admin-info { text-align: right; }
    .admin-name { font-weight: 600; font-size: 0.9rem; }
    .admin-role { font-size: 0.75rem; color: var(--text-muted); }
    .admin-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--accent-blue); display: flex; align-items: center; justify-content: center; font-family: var(--font-sora); font-weight: 700; color: #fff; }

    /* METRIC CARDS */
    .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 40px; }
    .metric-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; display: flex; align-items: center; gap: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); }
    .metric-icon-wrap { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .metric-icon-wrap.gold { background: rgba(212, 175, 55, 0.1); color: var(--primary-gold); }
    .metric-icon-wrap.blue { background: rgba(84, 101, 255, 0.1); color: var(--accent-blue); }
    .metric-icon-wrap.green { background: rgba(0, 230, 118, 0.1); color: var(--accent-green); }
    .metric-icon-wrap svg { width: 28px; height: 28px; fill: currentColor; }
    .metric-info h3 { font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 6px; font-weight: 600; }
    .metric-info .value { font-size: 1.8rem; font-family: var(--font-sora); font-weight: 700; }

    /* SECTIONS */
    .tab-section { display: none; }
    .tab-section.active { display: block; }

    /* DATA TABLE */
    .table-container { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); margin-bottom: 24px; }
    .table-header { padding: 24px 32px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; }
    .table-header h3 { font-family: var(--font-sora); font-size: 1.2rem; font-weight: 700; }
    .data-table { width: 100%; border-collapse: collapse; text-align: left; }
    .data-table th, .data-table td { padding: 18px 32px; border-bottom: 1px solid var(--border-color); }
    .data-table th { font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; background: rgba(255, 255, 255, 0.01); }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover { background: rgba(255, 255, 255, 0.015); }
    .col-author { font-weight: 600; width: 220px; }
    .col-quote { font-style: italic; color: #eee; }
    .col-media { width: 140px; }
    .col-actions { width: 180px; text-align: right; }
    
    .data-table td.col-actions {
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      align-items: center;
      justify-content: flex-end;
      gap: 8px;
      padding-top: 24px;
    }

    .badge { padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: inline-block; }
    .badge-image { background: rgba(84, 101, 255, 0.15); color: #8fa0ff; }
    .badge-video { background: rgba(212, 175, 55, 0.15); color: var(--primary-gold); }

    .btn-action {
      background: none; border: 1px solid var(--border-color); border-radius: 8px;
      padding: 8px 12px; font-size: 0.85rem; font-weight: 600; cursor: pointer;
      color: var(--text-white); transition: all 0.2s; display: inline-flex;
      align-items: center; gap: 6px;
    }
    .btn-action svg { width: 14px; height: 14px; fill: currentColor; }
    .btn-action.approve { border-color: rgba(0, 230, 118, 0.4); color: var(--accent-green); }
    .btn-action.approve:hover { background: rgba(0, 230, 118, 0.1); }
    .btn-action.edit { border-color: rgba(212, 175, 55, 0.4); color: var(--primary-gold); }
    .btn-action.edit:hover { background: rgba(212, 175, 55, 0.1); }
    .btn-action.delete { border-color: rgba(255, 61, 113, 0.4); color: var(--accent-red); }
    .btn-action.delete:hover { background: rgba(255, 61, 113, 0.1); }

    /* CARD QUEUE (WHATSAPP BUBBLE STYLE) */
    .queue-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }
    .queue-card { background: #202c33; border: none; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 4px 12px rgba(0,0,0,0.2); font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
    .queue-media-preview { width: 100%; min-height: 180px; max-height: 400px; background: #05060a; overflow: hidden; position: relative; display: flex; align-items: center; justify-content: center; padding: 4px 4px 0 4px; }
    .queue-media-preview img, .queue-media-preview video { width: 100%; height: auto; max-height: 400px; object-fit: cover; border-radius: 8px; }
    .queue-media-preview .no-media { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 0.85rem; font-style: italic; }
    .queue-content { padding: 8px 12px 12px 12px; flex-grow: 1; display: flex; flex-direction: column; gap: 4px; color: #e9edef; }
    .queue-header { display: none; /* Hide header completely to match WA aesthetic */ }
    .queue-quote { font-size: 1rem; line-height: 1.4; white-space: pre-wrap; font-weight: 500; }
    .queue-text { font-size: 1rem; line-height: 1.4; }
    .wa-footer { text-align: right; font-size: 0.65rem; color: #8696a0; margin-top: 4px; }
    
    .queue-actions { border-top: 1px solid #2a3942; padding: 12px; display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 8px; background: #202c33; }
    .queue-actions > button, .queue-actions > form { flex: 1 1 30%; display: flex; }
    .queue-actions .btn-action { flex-grow: 1; justify-content: center; margin-left: 0; background: #2a3942; border: none; }

    .empty-state { padding: 80px 40px; text-align: center; color: var(--text-muted); }
    .empty-state svg { width: 64px; height: 64px; margin-bottom: 16px; opacity: 0.3; fill: currentColor; }
    .empty-state h4 { font-family: var(--font-sora); font-size: 1.2rem; color: #fff; margin-bottom: 8px; }

    /* FORMS */
    .form-container { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 40px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); max-width: 800px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
    @media (max-width: 767px) { .form-grid { grid-template-columns: 1fr; } }
    .text-area { resize: vertical; min-height: 120px; }
    .radio-group { display: flex; gap: 16px; margin-top: 8px; }
    .radio-label { flex: 1; display: flex; cursor: pointer; font-size: 0.9rem; }
    .radio-label input { display: none; }
    .radio-label .radio-text {
      width: 100%; text-align: center; background: var(--bg-input); border: 1px solid var(--border-color);
      border-radius: 12px; padding: 14px 12px; font-weight: 600; color: var(--text-muted);
      transition: all 0.3s;
    }
    .radio-label input:checked + .radio-text {
      background: rgba(212, 175, 55, 0.1);
      border-color: var(--primary-gold);
      color: var(--primary-gold);
    }
    .radio-label:hover .radio-text { border-color: rgba(255,255,255,0.2); }
    
    .file-input-styled { margin-top: 8px; }
    .file-input-styled input[type="file"] {
      width: 100%; background: var(--bg-input); border: 1px dashed var(--border-color);
      border-radius: 12px; padding: 12px; color: var(--text-white); font-family: inherit;
      font-size: 0.85rem; cursor: pointer; transition: all 0.3s;
    }
    .file-input-styled input[type="file"]:hover { border-color: var(--primary-gold); background: rgba(212, 175, 55, 0.05); }
    .file-input-styled input[type="file"]::file-selector-button {
      background: var(--bg-card); color: var(--text-white); border: 1px solid var(--border-color);
      border-radius: 8px; padding: 8px 16px; cursor: pointer; font-family: var(--font-sora);
      font-weight: 600; transition: all 0.2s; margin-right: 16px;
    }
    .file-input-styled input[type="file"]::file-selector-button:hover { background: var(--primary-gold); color: #0c0e17; border-color: var(--primary-gold); }

    /* BACKUP */
    .utility-box { margin-top: 32px; border-top: 1px solid var(--border-color); padding-top: 32px; }
    .utility-desc { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 16px; line-height: 1.5; }
    .utility-actions { display: flex; gap: 16px; flex-wrap: wrap; }
    .btn-secondary {
      background: transparent; border: 1px solid var(--border-color); color: var(--text-white);
      border-radius: 10px; padding: 12px 20px; font-size: 0.88rem; font-weight: 600;
      cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-block;
    }
    .btn-secondary:hover { background: rgba(255, 255, 255, 0.05); border-color: var(--text-white); }

    /* MODALS */
    .modal { position: fixed; inset: 0; background: rgba(5, 6, 10, 0.8); backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center; z-index: 10000; }
    .modal.active { display: flex; }
    .modal-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 20px; width: 100%; max-width: 600px; box-shadow: 0 24px 80px rgba(0, 0, 0, 0.5); overflow: hidden; }
    .modal-header { padding: 24px 32px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; }
    .modal-header h3 { font-family: var(--font-sora); font-size: 1.3rem; font-weight: 700; }
    .btn-close { background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; transition: color 0.2s; }
    .btn-close:hover { color: var(--text-white); }
    .modal-body { padding: 32px; max-height: 60vh; overflow-y: auto; }
    .modal-footer { border-top: 1px solid var(--border-color); padding: 20px 32px; display: flex; justify-content: flex-end; gap: 16px; background: rgba(255, 255, 255, 0.01); }

    /* LIVE PREVIEW LAYOUT */
    .create-layout { display: grid; grid-template-columns: 1fr 380px; gap: 32px; align-items: start; }
    @media (max-width: 1200px) { .create-layout { grid-template-columns: 1fr; } }
    .preview-container { position: sticky; top: 120px; }
    .preview-container h4 { font-family: var(--font-sora); font-size: 1rem; color: var(--text-muted); margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 8px; }
    .preview-container .queue-card { pointer-events: none; opacity: 0.9; }
    .pulse-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--accent-green); box-shadow: 0 0 10px var(--accent-green); animation: pulse 1.8s infinite; }

    /* TOAST */
    .toast-container { position: fixed; bottom: 40px; right: 40px; display: flex; flex-direction: column; gap: 12px; z-index: 10001; }
    .toast {
      background: #181c28; border-left: 4px solid var(--accent-blue); border-radius: 8px;
      padding: 16px 24px; color: var(--text-white); font-size: 0.9rem; font-weight: 500;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35); display: flex; align-items: center; gap: 12px;
      animation: toastSlideIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    }
    .toast.success { border-left-color: var(--accent-green); }
    .toast.error { border-left-color: var(--accent-red); }
    @keyframes toastSlideIn { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    /* RESPONSIVE */
    .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    @media (max-width: 991px) {
      .app-layout { display: block; padding-bottom: 85px; }
      .sidebar {
        position: fixed; top: auto; bottom: 0; left: 0; right: 0; height: auto; width: 100%;
        flex-direction: row; padding: 6px 12px; background: rgba(18, 22, 34, 0.97);
        backdrop-filter: blur(15px); border-right: none; border-top: 1px solid var(--border-color);
        z-index: 9000; justify-content: flex-start;
        overflow-x: auto;
        display: flex;
        align-items: center;
        gap: 6px;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE/Edge */
      }
      .sidebar::-webkit-scrollbar {
        display: none; /* Chrome/Safari/Opera */
      }
      .sidebar-header { display: none; }
      .sidebar-menu {
        flex-direction: row;
        justify-content: flex-start;
        width: auto;
        gap: 6px;
        flex-wrap: nowrap;
        display: flex;
      }
      .sidebar-footer {
        display: flex;
        flex-direction: row;
        border-top: none;
        padding-top: 0;
        margin: 0;
        gap: 6px;
        flex-wrap: nowrap;
      }
      .menu-item {
        flex-direction: column; gap: 4px; padding: 8px 12px; font-size: 0.7rem; text-align: center;
        flex: 0 0 auto;
        min-width: 85px;
        white-space: nowrap;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .menu-item svg { width: 20px; height: 20px; }
      .menu-item.active::before { display: none; }
      .menu-item.active { border-left: none; border-bottom: 3px solid var(--primary-gold); background: transparent; color: var(--primary-gold); }
      .menu-badge { position: absolute; top: 0; right: 10px; font-size: 0.65rem; padding: 2px 6px; }
      .main-content { padding: 20px 16px; height: auto; overflow-y: visible; }
      .top-bar { flex-direction: column; align-items: flex-start; gap: 16px; margin-bottom: 24px; }
      .top-bar-left h2 { font-size: 1.3rem; flex-wrap: wrap; }
      .queue-grid { grid-template-columns: 1fr; }
      .login-card { padding: 32px 24px; margin: 16px; width: calc(100% - 32px); }
      .modal-card { max-height: 90vh; margin: 16px; }
      
      /* Additional fixes for mobile */
      .form-container { padding: 24px; }
      .table-responsive { padding: 0 16px 16px 16px; }
      .table-header { padding: 20px 16px; }
      .data-table { min-width: 800px; }
      
      /* DataTables stacking on mobile */
      .dataTables_wrapper .dataTables_length,
      .dataTables_wrapper .dataTables_filter {
        float: none; text-align: left; margin: 8px 0;
      }
      .dataTables_wrapper .dataTables_paginate {
        float: none; text-align: center; margin-top: 16px;
      }
    }

    /* MEDIA THUMB */
    .media-thumb { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border-color); }
  </style>
  <!-- TinyMCE WYSIWYG Editor -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
  <script>
    tinymce.init({
      selector: '.text-area',
      plugins: 'lists link code',
      toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | code',
      menubar: false,
      skin: 'oxide-dark',
      content_css: 'dark',
      height: 250,
      setup: function (editor) {
        editor.on('change', function () {
          editor.save();
        });
      }
    });
  </script>
</head>

<body>

  <!-- TOAST NOTIFICATION -->
  <?php if ($toast_message): ?>
  <div class="toast-container">
    <div class="toast <?= htmlspecialchars($toast_type) ?>">
      <?= htmlspecialchars($toast_message) ?>
    </div>
  </div>
  <script>setTimeout(() => { document.querySelector('.toast-container')?.remove(); }, 4000);</script>
  <?php endif; ?>

  <?php if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true): ?>
  <!-- LOGIN SCREEN -->
  <div class="login-container">
    <div class="login-card">
      <div class="login-logo"><h2>P</h2></div>
      <h3>Manajemen Panggonan</h3>
      <p>Masukkan username dan kata sandi tim manajemen Panggonan untuk mengakses dasbor admin.</p>
      <form method="POST">
        <input type="hidden" name="admin_login" value="1" />
        <div class="form-group">
          <label class="form-label" for="username">Username</label>
          <input type="text" id="username" name="username" required class="form-input" placeholder="admin" autofocus />
        </div>
        <div class="form-group">
          <label class="form-label" for="password">Kata Sandi</label>
          <input type="password" id="password" name="password" required class="form-input" placeholder="••••••••" />
        </div>
        <button type="submit" class="btn-gold">Buka Dasbor</button>
      </form>
      <?php if ($login_error): ?>
        <div class="login-error"><?= htmlspecialchars($login_error) ?></div>
      <?php endif; ?>
    </div>
  </div>

  <?php else: ?>
  <!-- MAIN APPLICATION LAYOUT -->
  <div class="app-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-logo"><span>P</span></div>
        <div class="sidebar-title-wrap">
          <h1>Panggonan</h1>
          <span>Admin Dashboard</span>
        </div>
      </div>

      <ul class="sidebar-menu">
        <li>
          <a class="menu-item <?= $current_tab === 'dashboard' ? 'active' : '' ?>" href="?tab=dashboard">
            <svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            Dashboard Jurnal
          </a>
        </li>
        <li>
          <a class="menu-item <?= $current_tab === 'approvals' ? 'active' : '' ?>" href="?tab=approvals">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            Antrean Persetujuan
            <?php if ($pending_count > 0): ?>
              <span class="menu-badge"><?= $pending_count ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li>
          <a class="menu-item <?= $current_tab === 'active' ? 'active' : '' ?>" href="?tab=active">
            <svg viewBox="0 0 24 24"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H8V4h12v12z"/></svg>
            Daftar Jurnal Aktif
          </a>
        </li>
        <li>
          <a class="menu-item <?= $current_tab === 'reservations' ? 'active' : '' ?>" href="?tab=reservations">
            <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
            Kelola Reservasi
            <?php if ($pending_res_count > 0): ?>
              <span class="menu-badge" style="background-color: var(--primary-gold); color: #0c0e17;"><?= $pending_res_count ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li>
          <a class="menu-item <?= $current_tab === 'create' ? 'active' : '' ?>" href="?tab=create">
            <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
            Tambah Jurnal Baru
          </a>
        </li>
        <li>
          <a class="menu-item <?= $current_tab === 'categories' ? 'active' : '' ?>" href="?tab=categories">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
            Kategori Menu
          </a>
        </li>
        <li>
          <a class="menu-item <?= $current_tab === 'menu' ? 'active' : '' ?>" href="?tab=menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            Daftar Menu Resto
          </a>
        </li>
        <li>
          <a class="menu-item <?= $current_tab === 'gallery' ? 'active' : '' ?>" href="?tab=gallery">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
            Kelola Galeri Foto
          </a>
        </li>
        <li>
          <a class="menu-item <?= $current_tab === 'traffic' ? 'active' : '' ?>" href="?tab=traffic">
            <svg viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
            Traffic Analytics
          </a>
        </li>
      </ul>

      <div class="sidebar-footer">
        <a href="../" class="menu-item" target="_blank">
          <svg viewBox="0 0 24 24"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg>
          Kembali ke Website
        </a>
        <a class="menu-item" href="?action=logout" style="color: var(--accent-red);">
          <svg viewBox="0 0 24 24"><path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
          Keluar (Logout)
        </a>
      </div>
    </aside>

    <!-- MAIN CONTENT VIEW -->
    <main class="main-content">
      
      <?php if ($current_tab === 'traffic'): ?>
        <?php include __DIR__ . '/traffic_content.php'; ?>
      <?php else: ?>

      <!-- TOP STATUS BAR -->
      <header class="top-bar">
        <div class="top-bar-left">
          <h2>
            <?php
            $titles = [
              'dashboard' => 'Dashboard Jurnal', 
              'approvals' => 'Antrean Persetujuan', 
              'active' => 'Daftar Jurnal Aktif', 
              'create' => 'Tambah Jurnal Baru', 
              'reservations' => 'Kelola Reservasi',
              'categories' => 'Kelola Kategori Menu',
              'menu' => 'Kelola Item Menu Resto',
              'gallery' => 'Kelola Galeri Foto'
            ];
            echo $titles[$current_tab] ?? 'Dashboard Jurnal';
            ?>
            <span class="sync-status"><span class="sync-dot"></span> Live Sync</span>
          </h2>
        </div>
        <div class="top-bar-right">
          <div class="admin-info">
            <div class="admin-name">Administrator</div>
            <div class="admin-role">Tim Manajemen</div>
          </div>
          <div class="admin-avatar">A</div>
        </div>
      </header>

      <!-- TAB 1: DASHBOARD SUMMARY -->
      <section class="tab-section <?= $current_tab === 'dashboard' ? 'active' : '' ?>">
        <div class="metrics-grid">
          <div class="metric-card">
            <div class="metric-icon-wrap blue">
              <svg viewBox="0 0 24 24"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H8V4h12v12z"/></svg>
            </div>
            <div class="metric-info">
              <h3>Jurnal Aktif</h3>
              <div class="value"><?= $active_count ?></div>
            </div>
          </div>
          <div class="metric-card">
            <div class="metric-icon-wrap gold">
              <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            </div>
            <div class="metric-info">
              <h3>Antrean Persetujuan</h3>
              <div class="value"><?= $pending_count ?></div>
            </div>
          </div>
          <div class="metric-card">
            <div class="metric-icon-wrap gold" style="background: rgba(212, 175, 55, 0.15); color: var(--primary-gold);">
              <svg viewBox="0 0 24 24" style="fill: currentColor;"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/></svg>
            </div>
            <div class="metric-info">
              <h3>Reservasi Pending</h3>
              <div class="value"><?= $pending_res_count ?></div>
            </div>
          </div>
          <div class="metric-card">
            <div class="metric-icon-wrap green">
              <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
            <div class="metric-info">
              <h3>Total Jurnal (Semua)</h3>
              <div class="value"><?= $active_count + $pending_count ?></div>
            </div>
          </div>
        </div>

        <!-- QUICK RESERVATION TABLE -->
        <div class="table-container">
          <div class="table-header">
            <h3>Reservasi Masuk Terbaru (Butuh Tindakan)</h3>
          </div>
          <div class="table-responsive">
            <table class="data-table">
            <thead>
              <tr>
                <th style="width: 150px;">Nama</th>
                <th style="width: 130px;">No. WA</th>
                <th style="width: 130px;">Cabang</th>
                <th style="width: 130px;">Acara</th>
                <th style="width: 120px;">Tanggal</th>
                <th style="width: 80px;">Pax</th>
                <th>Catatan</th>
                <th style="width: 180px; text-align: right;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $pending_reservations = array_filter($res_list, function($r) { return $r['status'] === 'pending'; });
              foreach (array_slice($pending_reservations, 0, 5) as $res): ?>
                  <tr>
                    <td><strong><?= htmlspecialchars($res['name']) ?></strong></td>
                    <td style="font-size: 0.9rem;">
                      <?php if (!empty($res['phone'])): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $res['phone']) ?>" target="_blank" style="color: var(--accent-green); text-decoration: none; display: flex; align-items: center; gap: 4px;">
                          <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.888-.788-1.487-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a5.8 5.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 21a9 9 0 110-18 9 9 0 010 18zm0-21a12 12 0 100 24 12 12 0 000-24z"/></svg>
                          <?= htmlspecialchars($res['phone']) ?>
                        </a>
                      <?php else: ?>
                        -
                      <?php endif; ?>
                    </td>
                    <td><span class="badge" style="background: rgba(212, 175, 55, 0.1); color: var(--primary-gold);"><?= htmlspecialchars($res['branch']) ?></span></td>
                    <td><?= htmlspecialchars($res['event_type']) ?></td>
                  <td>
                    <?= date('d M Y', strtotime($res['reservation_date'])) ?>
                    <?php if (!empty($res['reservation_time'])): ?>
                      <br><span style="font-size: 0.8rem; color: var(--text-muted);"><?= date('H:i', strtotime($res['reservation_time'])) ?> WIB</span>
                    <?php endif; ?>
                  </td>
                    <td><strong style="color: var(--primary-gold);"><?= (int)$res['pax'] ?></strong></td>
                    <td style="font-size: 0.9rem; color: #ddd; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($res['note']) ?></td>
                    <td class="col-actions" style="display: flex; gap: 8px; justify-content: flex-end; align-items: center; border-bottom: none; padding-top: 14px;">
                      <form method="POST" style="display:inline;">
                        <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>" />
                        <button type="submit" name="action_reservation_confirm" class="btn-action approve">
                          Setujui
                        </button>
                      </form>
                      <button type="button" class="btn-action edit" onclick='openResEditModal(<?= json_encode($res, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>Sunting</button>
                      <form method="POST" style="display:inline;">
                        <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>" />
                        <button type="submit" name="action_reservation_cancel" class="btn-action delete">
                          Batal
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
          </div>
        </div>

        <!-- QUICK APPROVAL TABLE -->
        <div class="table-container">
          <div class="table-header">
            <h3>Cerita Pelanggan Terbaru (Butuh Persetujuan)</h3>
          </div>
          <div class="table-responsive">
            <table class="data-table">
            <thead>
              <tr>
                <th class="col-author">Nama / Info</th>
                <th class="col-quote">Quotes / Cerita</th>
                <th class="col-media">Media</th>
                <th class="col-actions">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach (array_slice($pending_list, 0, 5) as $post): ?>
                <tr>
                  <td class="col-author">
                    <div class="queue-author"><?= htmlspecialchars($post['author']) ?></div>
                    <div class="queue-ig"><?= htmlspecialchars($post['date_label']) ?></div>
                  </td>
                  <td class="col-quote"><?= htmlspecialchars($post['quote'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td class="col-media">
                    <span class="badge <?= $post['media_type'] === 'video' ? 'badge-video' : 'badge-image' ?>"><?= $post['media_type'] ?></span>
                  </td>
                  <td class="col-actions">
                    <form method="POST" style="display:inline;">
                      <input type="hidden" name="journal_id" value="<?= $post['id'] ?>" />
                      <button type="submit" name="action_approve" class="btn-action approve">
                        <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Setujui
                      </button>
                    </form>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus cerita ini?');">
                      <input type="hidden" name="journal_id" value="<?= $post['id'] ?>" />
                      <button type="submit" name="action_delete" class="btn-action delete">
                        <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                      </button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
          </div>
        </div>

        <!-- BACKUP & SYNC -->
        <div class="form-container utility-box">
          <h3 style="font-family: var(--font-sora); font-size: 1.2rem; margin-bottom: 8px;">Backup & Sinkronisasi Database</h3>
          <p class="utility-desc">
            Cadangkan seluruh data jurnal ke dalam file JSON, atau unggah data lama untuk dipulihkan ke database server.
          </p>
          <div class="utility-actions">
            <a href="?action=export" class="btn-secondary">Ekspor Database (JSON)</a>
            <form method="POST" enctype="multipart/form-data" style="display:inline;">
              <input type="hidden" name="action_import" value="1" />
              <label class="btn-secondary" style="cursor:pointer;">
                Impor Database (JSON)
                <input type="file" name="import_file" accept=".json" style="display:none;" onchange="this.form.submit();" />
              </label>
            </form>
          </div>
        </div>
      </section>
      </section>

      <!-- TAB 2: APPROVALS QUEUE -->
      <section class="tab-section <?= $current_tab === 'approvals' ? 'active' : '' ?>">
        <?php if (empty($pending_list)): ?>
          <div class="empty-state">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            <h4>Antrean Bersih & Rapi</h4>
            <p>Tidak ada cerita atau kutipan dari customer yang mengantre saat ini.</p>
          </div>
        <?php else: ?>
          <div class="queue-grid">
            <?php foreach ($pending_list as $post): ?>
            <div class="queue-card">
              <div class="queue-media-preview">
                <?php if (!empty($post['media_url']) && strlen($post['media_url']) > 5): ?>
                  <?php if ($post['media_type'] === 'video'): ?>
                    <video src="../<?= htmlspecialchars($post['media_url']) ?>" preload="metadata" controls muted playsinline></video>
                  <?php else: ?>
                    <img src="../<?= htmlspecialchars($post['media_url']) ?>" alt="Media Preview" loading="lazy" decoding="async" />
                  <?php endif; ?>
                <?php else: ?>
                  <div class="no-media">Lampiran foto disusulkan via WhatsApp</div>
                <?php endif; ?>
              </div>
                  
              <div class="queue-content">
                    <div class="queue-text"><?= $post['text'] ?></div>
                    <div class="wa-footer"><?= htmlspecialchars($post['date_label']) ?> • <?= htmlspecialchars($post['author']) ?></div>
                  </div>
              <div class="queue-actions">
                <form method="POST">
                  <input type="hidden" name="journal_id" value="<?= $post['id'] ?>" />
                  <button type="submit" name="action_approve" class="btn-action approve">
                    <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Setujui Tayang
                  </button>
                </form>
                <button type="button" class="btn-action edit" onclick='openEditModal(<?= $post['id'] ?>, <?= htmlspecialchars(json_encode([
                  "author" => $post["author"],
                  "date_label" => $post["date_label"],
                  "quote" => $post["quote"],
                  "text" => $post["text"],
                  "media_type" => $post["media_type"],
                  "media_url" => $post["media_url"]
                ]), ENT_QUOTES, "UTF-8") ?>)'>
                  <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg> Edit
                </button>
                <form method="POST" onsubmit="return confirm('Hapus cerita ini?');">
                  <input type="hidden" name="journal_id" value="<?= $post['id'] ?>" />
                  <button type="submit" name="action_delete" class="btn-action delete">
                    <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg> Hapus
                  </button>
                </form>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

      <!-- TAB 3: ACTIVE POSTS -->
      <section class="tab-section <?= $current_tab === 'active' ? 'active' : '' ?>">
        <div class="table-container">
          <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <h3>Daftar Jurnal yang Sedang Tayang Publik</h3>
            <div style="display: flex; gap: 12px;">
              <a href="export_excel.php?type=journals" class="btn-action edit" style="text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;" title="Unduh format Excel"><i class="fas fa-file-excel"></i> Ekspor Excel</a>
              <a href="print_report.php?type=journals" target="_blank" class="btn-action approve" style="text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;" title="Cetak laporan PDF"><i class="fas fa-file-pdf"></i> Cetak Laporan (PDF)</a>
            </div>
          </div>
          <div class="table-responsive">
            <table class="data-table">
            <thead>
              <tr>
                <th class="col-author">Penulis</th>
                <th class="col-quote">Kutipan</th>
                <th class="col-media">Jenis Media</th>
                <th class="col-actions">Kontrol</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($active_list as $post): ?>
                <tr>
                  <td class="col-author">
                    <div class="queue-author"><?= htmlspecialchars($post['author']) ?></div>
                    <div class="queue-ig"><?= htmlspecialchars($post['date_label']) ?></div>
                  </td>
                  <td class="col-quote"><?= htmlspecialchars($post['quote'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td class="col-media">
                    <span class="badge <?= $post['media_type'] === 'video' ? 'badge-video' : 'badge-image' ?>"><?= $post['media_type'] ?></span>
                  </td>
                  <td class="col-actions">
                    <button class="btn-action edit" onclick="openEditModal(<?= $post['id'] ?>, <?= htmlspecialchars(json_encode($post, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES) ?>)">
                      <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg> Sunting
                    </button>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurnal ini?');">
                      <input type="hidden" name="journal_id" value="<?= $post['id'] ?>" />
                      <button type="submit" name="action_delete" class="btn-action delete">
                        <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg> Hapus
                      </button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>

            </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- TAB 5: KELOLA RESERVASI -->
      <section class="tab-section <?= $current_tab === 'reservations' ? 'active' : '' ?>">
        <div class="table-container">
          <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <h3>Daftar Semua Reservasi Pelanggan</h3>
            <div style="display: flex; gap: 12px;">
              <a href="export_excel.php?type=reservations" class="btn-action edit" style="text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;" title="Unduh format Excel"><i class="fas fa-file-excel"></i> Ekspor Excel</a>
              <a href="print_report.php?type=reservations" target="_blank" class="btn-action approve" style="text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;" title="Cetak laporan PDF"><i class="fas fa-file-pdf"></i> Cetak Laporan (PDF)</a>
            </div>
          </div>
          <div class="table-responsive">
            <table class="data-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nama Pelanggan</th>
                <th>No. WA</th>
                <th>Cabang</th>
                <th>Jenis Acara</th>
                <th>Tanggal Reservasi</th>
                <th>Pax</th>
                <th>Catatan</th>
                <th>Status</th>
                <th>Tanggal Pengajuan</th>
                <th style="width: 240px; text-align: right;">Kontrol Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($res_list as $res): ?>
                <tr>
                  <td>#<?= $res['id'] ?></td>
                  <td><strong><?= htmlspecialchars($res['name']) ?></strong></td>
                  <td style="font-size: 0.9rem;">
                      <?php if (!empty($res['phone'])): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $res['phone']) ?>" target="_blank" style="color: var(--accent-green); text-decoration: none; display: flex; align-items: center; gap: 4px;">
                          <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.888-.788-1.487-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a5.8 5.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 21a9 9 0 110-18 9 9 0 010 18zm0-21a12 12 0 100 24 12 12 0 000-24z"/></svg>
                          <?= htmlspecialchars($res['phone']) ?>
                        </a>
                      <?php else: ?>
                        -
                      <?php endif; ?>
                  </td>
                  <td><span class="badge" style="background: rgba(255, 255, 255, 0.05); color: #fff; border: 1px solid var(--border-color);"><?= htmlspecialchars($res['branch']) ?></span></td>
                  <td><?= htmlspecialchars($res['event_type']) ?></td>
                  <td>
                    <strong><?= date('d M Y', strtotime($res['reservation_date'])) ?></strong>
                    <?php if (!empty($res['reservation_time'])): ?>
                      <br><span style="font-size: 0.8rem; color: var(--text-muted);"><?= date('H:i', strtotime($res['reservation_time'])) ?> WIB</span>
                    <?php endif; ?>
                  </td>
                  <td><strong style="color: var(--primary-gold);"><?= (int)$res['pax'] ?></strong></td>
                  <td style="font-size: 0.9rem; max-width: 220px; overflow-wrap: break-word; white-space: normal;"><?= htmlspecialchars($res['note']) ?></td>
                  <td>
                    <?php if ($res['status'] === 'confirmed'): ?>
                      <span class="badge" style="background: rgba(0, 230, 118, 0.15); color: var(--accent-green);">Dikonfirmasi</span>
                    <?php elseif ($res['status'] === 'cancelled'): ?>
                      <span class="badge" style="background: rgba(255, 61, 113, 0.15); color: var(--accent-red);">Dibatalkan</span>
                    <?php else: ?>
                      <span class="badge" style="background: rgba(212, 175, 55, 0.15); color: var(--primary-gold);">Menunggu</span>
                    <?php endif; ?>
                  </td>
                  <td style="font-size: 0.8rem; color: var(--text-muted);"><?= date('d/m/Y H:i', strtotime($res['created_at'])) ?></td>
                  <td class="col-actions" style="display: flex; gap: 8px; justify-content: flex-end; align-items: center; border-bottom: none; padding-top: 14px;">
                    <?php if ($res['status'] === 'pending'): ?>
                      <form method="POST" style="display:inline;">
                        <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>" />
                        <button type="submit" name="action_reservation_confirm" class="btn-action approve" title="Setujui Reservasi">
                          Setujui
                        </button>
                      </form>
                      <form method="POST" style="display:inline;">
                        <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>" />
                        <button type="submit" name="action_reservation_cancel" class="btn-action delete" title="Batalkan Reservasi">
                          Batal
                        </button>
                      </form>
                    <?php elseif ($res['status'] === 'confirmed'): ?>
                      <form method="POST" style="display:inline;">
                        <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>" />
                        <button type="submit" name="action_reservation_cancel" class="btn-action delete" title="Batalkan Reservasi">
                          Batal
                        </button>
                      </form>
                    <?php elseif ($res['status'] === 'cancelled'): ?>
                      <form method="POST" style="display:inline;">
                        <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>" />
                        <button type="submit" name="action_reservation_confirm" class="btn-action approve" title="Setujui Reservasi">
                          Setujui
                        </button>
                      </form>
                    <?php endif; ?>
                    <button type="button" class="btn-action edit" style="padding: 8px 10px;" title="Sunting Reservasi" onclick='openResEditModal(<?= json_encode($res, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                      <svg viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: currentColor;"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                    </button>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus data reservasi ini secara permanen?');">
                      <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>" />
                      <button type="submit" name="action_reservation_delete" class="btn-action delete" style="padding: 8px 10px;" title="Hapus Permanen">
                        <svg viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: currentColor;"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- TAB 4: CREATE POST -->
      <section class="tab-section <?= $current_tab === 'create' ? 'active' : '' ?>">
        <div class="create-layout">
          <div class="form-container" style="max-width: 100%;">
            <form method="POST" enctype="multipart/form-data">
              <input type="hidden" name="action_create" value="1" />
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label" for="create-author">Nama Penulis / Inisial</label>
                  <input type="text" id="create-author" name="create_author" required class="form-input" placeholder="Bp. Masyuri (Founder)" />
                </div>
                <div class="form-group">
                  <label class="form-label" for="create-date">Label Tanggal / Informasi Kategori</label>
                  <input type="text" id="create-date" name="create_date" required class="form-input" placeholder="Founder Story / 20 Mei 2026" />
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="create-text">Paragraf Deskripsi Cerita Lengkap</label>
                <textarea id="create-text" name="create_text" class="form-input text-area" placeholder="Tulis rincian cerita puitis di sini..."></textarea>
              </div>

              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label">Tipe Media</label>
                  <div class="radio-group">
                    <label class="radio-label">
                      <input type="radio" name="create_media_type" value="image" checked />
                      <span class="radio-text">🖼️ Gambar</span>
                    </label>
                    <label class="radio-label">
                      <input type="radio" name="create_media_type" value="video" />
                      <span class="radio-text">🎥 Video</span>
                    </label>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">Unggah File Media</label>
                  <div class="file-input-styled">
                    <input type="file" name="create_media_file" accept="image/webp,image/jpeg,image/png,video/mp4,video/webm" />
                  </div>
                </div>
              </div>

              <button type="submit" class="btn-gold" style="margin-top: 16px;">Terbitkan Jurnal Baru</button>
            </form>
          </div>
          
          <div class="preview-container">
            <h4><span class="pulse-dot"></span> Live Preview</h4>
            <div class="queue-card" id="live-preview-card">
              <div class="queue-media-preview">
                <div class="no-media" style="color: #8696a0;">Visual Media</div>
              </div>
              <div class="queue-content">

                <div class="queue-text" id="prev-text" style="opacity: 0.7;">Paragraf deskripsi akan tampil di sini menyesuaikan format.</div>
                <div class="wa-footer" id="prev-meta">Label Tanggal • Nama Penulis</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- TAB 6: KELOLA KATEGORI MENU -->
      <section class="tab-section <?= $current_tab === 'categories' ? 'active' : '' ?>">
        <div class="table-container">
          <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <h3>Daftar Kategori Menu Restoran</h3>
            <button type="button" class="btn-action approve" style="padding: 10px 16px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;" onclick="openAddCategoryModal()">
              <i class="fas fa-plus"></i> Tambah Kategori Baru
            </button>
          </div>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nama Kategori</th>
                  <th>Posisi Kolom (Visual Publik)</th>
                  <th>Urutan Sorting</th>
                  <th>Jumlah Menu Terkait</th>
                  <th style="width: 200px; text-align: right;">Kontrol Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($categories_list as $cat): ?>
                  <tr>
                    <td>#<?= $cat['id'] ?></td>
                    <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                    <td>
                      <?php if ($cat['column_position'] === 'left'): ?>
                        <span class="badge" style="background: rgba(0, 188, 212, 0.15); color: #00bcd4;">Kolom Kiri (Left)</span>
                      <?php else: ?>
                        <span class="badge" style="background: rgba(156, 39, 176, 0.15); color: #e040fb;">Kolom Kanan (Right)</span>
                      <?php endif; ?>
                    </td>
                    <td><span style="font-weight: 600; color: var(--primary-gold);"><?= (int)$cat['sort_order'] ?></span></td>
                    <td><span class="badge" style="background: rgba(255, 255, 255, 0.05); color: #fff;"><?= (int)$cat['item_count'] ?> Menu</span></td>
                    <td class="col-actions" style="display: flex; gap: 8px; justify-content: flex-end; align-items: center; border-bottom: none; padding-top: 14px;">
                      <button type="button" class="btn-action edit" title="Sunting Kategori" onclick='openCategoryEditModal(<?= json_encode($cat, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                        Sunting
                      </button>
                      <form method="POST" style="display:inline;" onsubmit="return confirm('APAKAH ANDA YAKIN? Menghapus kategori ini juga akan MENGHAPUS PERMANEN seluruh item menu di dalamnya secara otomatis.');">
                        <input type="hidden" name="category_id" value="<?= $cat['id'] ?>" />
                        <button type="submit" name="action_category_delete" class="btn-action delete" title="Hapus Kategori">
                          Hapus
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- TAB 7: KELOLA ITEM MENU RESTO -->
      <section class="tab-section <?= $current_tab === 'menu' ? 'active' : '' ?>">
        <div class="table-container">
          <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <h3>Daftar Item Menu Restoran</h3>
            <button type="button" class="btn-action approve" style="padding: 10px 16px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;" onclick="openAddMenuModal()">
              <i class="fas fa-plus"></i> Tambah Menu Baru
            </button>
          </div>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nama Menu</th>
                  <th>Kategori</th>
                  <th>Deskripsi Keterangan</th>
                  <th>Harga (Rupiah)</th>
                  <th>Status Ketersediaan</th>
                  <th>Urutan Sorting</th>
                  <th style="width: 200px; text-align: right;">Kontrol Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($menu_items_list as $item): ?>
                  <tr>
                    <td>#<?= $item['id'] ?></td>
                    <td><strong><?= htmlspecialchars($item['name']) ?></strong></td>
                    <td>
                      <span class="badge" style="background: rgba(212, 175, 55, 0.15); color: var(--primary-gold);">
                        <?= htmlspecialchars($item['category_name']) ?>
                      </span>
                      <br>
                      <span style="font-size: 0.75rem; color: var(--text-muted);">
                        (<?= $item['column_position'] === 'left' ? 'Kiri' : 'Kanan' ?>)
                      </span>
                    </td>
                    <td style="font-size: 0.9rem; color: #ddd; max-width: 250px; overflow-wrap: break-word; white-space: normal;">
                      <?= !empty($item['description']) ? htmlspecialchars($item['description']) : '<span style="color: var(--text-muted); font-style: italic;">Tidak ada deskripsi</span>' ?>
                    </td>
                    <td><strong style="color: var(--accent-green);">Rp <?= number_format($item['price'], 0, ',', '.') ?></strong></td>
                    <td>
                      <?php if ($item['is_available'] == 1): ?>
                        <span class="badge" style="background: rgba(0, 230, 118, 0.15); color: var(--accent-green);"><i class="fas fa-check-circle"></i> Tersedia</span>
                      <?php else: ?>
                        <span class="badge" style="background: rgba(255, 61, 113, 0.15); color: var(--accent-red);"><i class="fas fa-times-circle"></i> Habis / Kosong</span>
                      <?php endif; ?>
                    </td>
                    <td><span style="font-weight: 600;"><?= (int)$item['sort_order'] ?></span></td>
                    <td class="col-actions" style="display: flex; gap: 8px; justify-content: flex-end; align-items: center; border-bottom: none; padding-top: 14px;">
                      <button type="button" class="btn-action edit" title="Sunting Item Menu" onclick='openMenuEditModal(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                        Sunting
                      </button>
                      <form method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item menu ini secara permanen?');">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>" />
                        <button type="submit" name="action_menu_item_delete" class="btn-action delete" title="Hapus Item Menu">
                          Hapus
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- TAB 8: KELOLA GALERI FOTO & KATEGORI -->
      <section class="tab-section <?= $current_tab === 'gallery' ? 'active' : '' ?>">
        <!-- SECTION 1: KELOLA KATEGORI GALERI -->
        <div class="table-container" style="margin-bottom: 40px;">
          <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <h3>Daftar Kategori Galeri Foto</h3>
            <button type="button" class="btn-action approve" style="padding: 10px 16px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;" onclick="openAddGalleryCategoryModal()">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
              Tambah Kategori Baru
            </button>
          </div>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th style="width: 80px;">ID</th>
                  <th>Nama Kategori</th>
                  <th>Slug (Kunci Filter JS)</th>
                  <th>Urutan Sorting</th>
                  <th>Jumlah Foto Terkait</th>
                  <th style="width: 180px; text-align: right;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($gallery_categories_list as $cat): ?>
                  <tr>
                    <td>#<?= $cat['id'] ?></td>
                    <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                    <td><span class="badge" style="background: rgba(255, 255, 255, 0.05); color: #fff; font-family: monospace;"><?= htmlspecialchars($cat['slug']) ?></span></td>
                    <td><span style="font-weight: 600; color: var(--primary-gold);"><?= (int)$cat['sort_order'] ?></span></td>
                    <td><span class="badge" style="background: rgba(212, 175, 55, 0.1); color: var(--primary-gold);"><?= (int)$cat['item_count'] ?> Foto</span></td>
                    <td class="col-actions" style="display: flex; gap: 8px; justify-content: flex-end; align-items: center; border-bottom: none; padding-top: 14px;">
                      <button type="button" class="btn-action edit" title="Sunting Kategori" onclick='openGalleryCategoryEditModal(<?= json_encode($cat, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                        Sunting
                      </button>
                      <form method="POST" style="display:inline;" onsubmit="return confirm('Menghapus kategori ini juga akan MENGHAPUS PERMANEN seluruh foto di dalamnya secara otomatis dari server cPanel Anda. Lanjutkan?');">
                        <input type="hidden" name="category_id" value="<?= $cat['id'] ?>" />
                        <button type="submit" name="action_gallery_category_delete" class="btn-action delete" title="Hapus Kategori">
                          Hapus
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- SECTION 2: DAFTAR GALERI FOTO -->
        <div class="table-container">
          <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <h3>Koleksi Foto Galeri</h3>
            <button type="button" class="btn-action approve" style="padding: 10px 16px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;" onclick="openAddGalleryModal()">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
              Unggah Foto Baru
            </button>
          </div>
          
          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; padding: 20px 0;">
            <?php if (count($gallery_list) > 0): ?>
              <?php foreach ($gallery_list as $img): ?>
                <div class="metric-card" style="display: block; padding: 16px; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; background: #121420; transition: transform 0.3s ease;">
                  <div style="width: 100%; height: 150px; overflow: hidden; border-radius: 8px; margin-bottom: 12px; position: relative; background: #0c0e17;">
                    <img src="../<?= htmlspecialchars($img['image_url']) ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?= htmlspecialchars($img['title']) ?>" />
                    <span class="badge" style="position: absolute; top: 8px; left: 8px; background: rgba(212, 175, 55, 0.9); color: #0c0e17; font-weight: 700; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; padding: 4px 8px; border-radius: 4px;">
                      <?= htmlspecialchars($img['category_name']) ?>
                    </span>
                  </div>
                  <h4 style="color: #fff; font-size: 0.95rem; margin: 0 0 8px 0; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($img['title']) ?>">
                    <?= htmlspecialchars($img['title']) ?>
                  </h4>
                  <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center; margin-top: 12px;">
                    <button type="button" class="btn-action edit" style="padding: 6px 12px; font-size: 0.8rem;" onclick='openGalleryEditModal(<?= json_encode($img, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                      Sunting
                    </button>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus foto ini dari galeri secara permanen?');">
                      <input type="hidden" name="item_id" value="<?= $img['id'] ?>" />
                      <button type="submit" name="action_gallery_delete" class="btn-action delete" style="padding: 6px 12px; font-size: 0.8rem;">
                        Hapus
                      </button>
                    </form>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div style="grid-column: 1/-1; text-align: center; color: var(--text-muted); padding: 48px 0;">
                Belum ada koleksi foto di galeri.
              </div>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <?php endif; ?>

    </main>
  </div>

  <!-- EDIT JOURNAL MODAL -->
  <div class="modal" id="edit-modal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Edit Jurnal Panggonan</h3>
        <button class="btn-close" onclick="closeEditModal()">&times;</button>
      </div>
      <form method="POST" enctype="multipart/form-data" id="edit-form">
        <input type="hidden" name="action_edit" value="1" />
        <input type="hidden" name="journal_id" id="edit-id" />
        <input type="hidden" name="edit_existing_media_url" id="edit-existing-media-url" />

        <div class="modal-body">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="edit-author">Penulis</label>
              <input type="text" id="edit-author" name="edit_author" required class="form-input" />
            </div>
            <div class="form-group">
              <label class="form-label" for="edit-date">Label Info / Tanggal</label>
              <input type="text" id="edit-date" name="edit_date" required class="form-input" />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="edit-text">Rincian Cerita</label>
            <textarea id="edit-text" name="edit_text" class="form-input text-area"></textarea>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Tipe Media</label>
              <div class="radio-group">
                <label class="radio-label">
                  <input type="radio" name="edit_media_type" id="edit-type-image" value="image" />
                  <span class="radio-text">🖼️ Gambar</span>
                </label>
                <label class="radio-label">
                  <input type="radio" name="edit_media_type" id="edit-type-video" value="video" />
                  <span class="radio-text">🎥 Video</span>
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Ganti File Media (Opsional)</label>
              <div class="file-input-styled">
                <input type="file" name="edit_media_file" accept="image/webp,image/jpeg,image/png,video/mp4,video/webm" />
              </div>
              <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">Biarkan kosong jika tidak ingin mengganti media.</div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeEditModal()">Batal</button>
          <button type="submit" class="btn-gold">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- EDIT RESERVATION MODAL -->
  <div class="modal" id="edit-res-modal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Sunting Data Reservasi</h3>
        <button class="btn-close" onclick="closeResEditModal()">&times;</button>
      </div>
      <form method="POST" id="edit-res-form">
        <input type="hidden" name="action_reservation_edit" value="1" />
        <input type="hidden" name="reservation_id" id="edit-res-id" />

        <div class="modal-body">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="edit-res-name">Nama Pelanggan</label>
              <input type="text" id="edit-res-name" name="edit_res_name" required class="form-input" />
            </div>
            <div class="form-group">
              <label class="form-label" for="edit-res-phone">Nomor WhatsApp</label>
              <input type="tel" id="edit-res-phone" name="edit_res_phone" required class="form-input" />
            </div>
          </div>
          
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="edit-res-branch">Cabang</label>
              <select id="edit-res-branch" name="edit_res_branch" required class="form-input">
                <option value="GDC Depok">GDC Depok</option>
                <option value="Ciracas">Ciracas</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="edit-res-event">Jenis Acara</label>
              <input type="text" id="edit-res-event" name="edit_res_event" required class="form-input" />
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="edit-res-date">Tanggal & Jam Reservasi</label>
              <div style="display: flex; gap: 12px;">
                <input type="date" id="edit-res-date" name="edit_res_date" required class="form-input" style="flex: 1;" title="Tanggal" />
                <input type="time" id="edit-res-time" name="edit_res_time" required class="form-input" style="flex: 1;" title="Jam" />
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="edit-res-pax">Jumlah Tamu (Pax)</label>
              <input type="number" id="edit-res-pax" name="edit_res_pax" min="1" required class="form-input" />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="edit-res-note">Catatan Tambahan</label>
            <textarea id="edit-res-note" name="edit_res_note" class="form-input" style="min-height: 80px; resize: vertical;"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeResEditModal()">Batal</button>
          <button type="submit" class="btn-gold">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ADD CATEGORY MODAL -->
  <div class="modal" id="add-category-modal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Tambah Kategori Menu Baru</h3>
        <button class="btn-close" onclick="closeAddCategoryModal()">&times;</button>
      </div>
      <form method="POST">
        <input type="hidden" name="action_category_add" value="1" />
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label" for="add-cat-name">Nama Kategori</label>
            <input type="text" id="add-cat-name" name="name" required class="form-input" placeholder="Contoh: Makanan Utama, Seafood, Es Tradisional" />
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="add-cat-column">Posisi Kolom Publik</label>
              <select id="add-cat-column" name="column_position" required class="form-input">
                <option value="left">Kolom Kiri (Left Column)</option>
                <option value="right">Kolom Kanan (Right Column)</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="add-cat-sort">Urutan Penampilan (Sort Order)</label>
              <input type="number" id="add-cat-sort" name="sort_order" min="0" value="0" required class="form-input" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeAddCategoryModal()">Batal</button>
          <button type="submit" class="btn-gold">Tambah Kategori</button>
        </div>
      </form>
    </div>
  </div>

  <!-- EDIT CATEGORY MODAL -->
  <div class="modal" id="edit-category-modal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Sunting Kategori Menu</h3>
        <button class="btn-close" onclick="closeCategoryEditModal()">&times;</button>
      </div>
      <form method="POST">
        <input type="hidden" name="action_category_edit" value="1" />
        <input type="hidden" name="category_id" id="edit-cat-id" />
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label" for="edit-cat-name">Nama Kategori</label>
            <input type="text" id="edit-cat-name" name="name" required class="form-input" />
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="edit-cat-column">Posisi Kolom Publik</label>
              <select id="edit-cat-column" name="column_position" required class="form-input">
                <option value="left">Kolom Kiri (Left Column)</option>
                <option value="right">Kolom Kanan (Right Column)</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="edit-cat-sort">Urutan Penampilan (Sort Order)</label>
              <input type="number" id="edit-cat-sort" name="sort_order" min="0" required class="form-input" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeCategoryEditModal()">Batal</button>
          <button type="submit" class="btn-gold">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ADD MENU ITEM MODAL -->
  <div class="modal" id="add-menu-modal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Tambah Item Menu Baru</h3>
        <button class="btn-close" onclick="closeAddMenuModal()">&times;</button>
      </div>
      <form method="POST">
        <input type="hidden" name="action_menu_item_add" value="1" />
        <div class="modal-body">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="add-menu-name">Nama Menu</label>
              <input type="text" id="add-menu-name" name="name" required class="form-input" placeholder="Contoh: Gurame Bakar Madu" />
            </div>
            <div class="form-group">
              <label class="form-label" for="add-menu-cat">Kategori Menu</label>
              <select id="add-menu-cat" name="category_id" required class="form-input">
                <option value="" disabled selected>-- Pilih Kategori --</option>
                <?php foreach ($categories_list as $cat): ?>
                  <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?> (<?= $cat['column_position'] === 'left' ? 'Kiri' : 'Kanan' ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="add-menu-desc">Deskripsi / Keterangan Tambahan</label>
            <input type="text" id="add-menu-desc" name="description" class="form-input" placeholder="Contoh: (Lalapan + Trancam + Sambal) atau Porsi 2-3 orang" />
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="add-menu-price">Harga (Rupiah)</label>
              <input type="number" id="add-menu-price" name="price" min="0" required class="form-input" placeholder="Contoh: 35000" />
            </div>
            <div class="form-group">
              <label class="form-label" for="add-menu-sort">Urutan (Sort Order)</label>
              <input type="number" id="add-menu-sort" name="sort_order" min="0" value="0" required class="form-input" />
            </div>
          </div>
          <div class="form-group" style="margin-top: 12px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
              <input type="checkbox" name="is_available" value="1" checked style="width: 18px; height: 18px;" />
              <strong style="color: var(--text-white);">Status: Menu ini Tersedia (Aktif)</strong>
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeAddMenuModal()">Batal</button>
          <button type="submit" class="btn-gold">Tambah Menu</button>
        </div>
      </form>
    </div>
  </div>

  <!-- EDIT MENU ITEM MODAL -->
  <div class="modal" id="edit-menu-modal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Sunting Item Menu</h3>
        <button class="btn-close" onclick="closeMenuEditModal()">&times;</button>
      </div>
      <form method="POST">
        <input type="hidden" name="action_menu_item_edit" value="1" />
        <input type="hidden" name="item_id" id="edit-menu-id" />
        <div class="modal-body">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="edit-menu-name">Nama Menu</label>
              <input type="text" id="edit-menu-name" name="name" required class="form-input" />
            </div>
            <div class="form-group">
              <label class="form-label" for="edit-menu-cat">Kategori Menu</label>
              <select id="edit-menu-cat" name="category_id" required class="form-input">
                <?php foreach ($categories_list as $cat): ?>
                  <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?> (<?= $cat['column_position'] === 'left' ? 'Kiri' : 'Kanan' ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="edit-menu-desc">Deskripsi / Keterangan Tambahan</label>
            <input type="text" id="edit-menu-desc" name="description" class="form-input" />
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="edit-menu-price">Harga (Rupiah)</label>
              <input type="number" id="edit-menu-price" name="price" min="0" required class="form-input" />
            </div>
            <div class="form-group">
              <label class="form-label" for="edit-menu-sort">Urutan (Sort Order)</label>
              <input type="number" id="edit-menu-sort" name="sort_order" min="0" required class="form-input" />
            </div>
          </div>
          <div class="form-group" style="margin-top: 12px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
              <input type="checkbox" name="is_available" id="edit-menu-avail" value="1" style="width: 18px; height: 18px;" />
              <strong style="color: var(--text-white);">Status: Menu ini Tersedia (Aktif)</strong>
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeMenuEditModal()">Batal</button>
          <button type="submit" class="btn-gold">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ADD GALLERY ITEM MODAL -->
  <div class="modal" id="add-gallery-modal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Unggah Foto Galeri Baru</h3>
        <button class="btn-close" onclick="closeAddGalleryModal()">&times;</button>
      </div>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action_gallery_add" value="1" />
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label" for="add-gallery-title">Judul Foto</label>
            <input type="text" id="add-gallery-title" name="title" required class="form-input" placeholder="Contoh: Senja Indah di Joglo Panggonan" />
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="add-gallery-cat">Kategori Foto</label>
              <select id="add-gallery-cat" name="category_id" required class="form-input">
                <option value="" disabled selected>-- Pilih Kategori --</option>
                <?php foreach ($gallery_categories_list as $cat): ?>
                  <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="add-gallery-file">Pilih Berkas Gambar</label>
              <div class="file-input-styled">
                <input type="file" id="add-gallery-file" name="gallery_file" accept="image/webp,image/jpeg,image/png,image/jpg" required />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeAddGalleryModal()">Batal</button>
          <button type="submit" class="btn-gold">Unggah Foto</button>
        </div>
      </form>
    </div>
  </div>

  <!-- EDIT GALLERY ITEM MODAL -->
  <div class="modal" id="edit-gallery-modal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Sunting Info Foto Galeri</h3>
        <button class="btn-close" onclick="closeGalleryEditModal()">&times;</button>
      </div>
      <form method="POST">
        <input type="hidden" name="action_gallery_edit" value="1" />
        <input type="hidden" name="item_id" id="edit-gallery-id" />
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label" for="edit-gallery-title-field">Judul Foto</label>
            <input type="text" id="edit-gallery-title-field" name="title" required class="form-input" />
          </div>
          <div class="form-group">
            <label class="form-label" for="edit-gallery-cat-field">Kategori Foto</label>
            <select id="edit-gallery-cat-field" name="category_id" required class="form-input">
              <?php foreach ($gallery_categories_list as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeGalleryEditModal()">Batal</button>
          <button type="submit" class="btn-gold">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ADD GALLERY CATEGORY MODAL -->
  <div class="modal" id="add-gallery-category-modal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Tambah Kategori Galeri Baru</h3>
        <button class="btn-close" onclick="closeAddGalleryCategoryModal()">&times;</button>
      </div>
      <form method="POST">
        <input type="hidden" name="action_gallery_category_add" value="1" />
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label" for="add-galcat-name">Nama Kategori</label>
            <input type="text" id="add-galcat-name" name="name" required class="form-input" placeholder="Contoh: Suasana Malam, Camilan Sore" />
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="add-galcat-slug">Slug Kunci Filter (JS)</label>
              <input type="text" id="add-galcat-slug" name="slug" required class="form-input" placeholder="Contoh: suasana-malam, camilan" />
            </div>
            <div class="form-group">
              <label class="form-label" for="add-galcat-sort">Urutan Penampilan (Sort Order)</label>
              <input type="number" id="add-galcat-sort" name="sort_order" min="0" value="0" required class="form-input" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeAddGalleryCategoryModal()">Batal</button>
          <button type="submit" class="btn-gold">Tambah Kategori</button>
        </div>
      </form>
    </div>
  </div>

  <!-- EDIT GALLERY CATEGORY MODAL -->
  <div class="modal" id="edit-gallery-category-modal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Sunting Kategori Galeri</h3>
        <button class="btn-close" onclick="closeGalleryCategoryEditModal()">&times;</button>
      </div>
      <form method="POST">
        <input type="hidden" name="action_gallery_category_edit" value="1" />
        <input type="hidden" name="category_id" id="edit-galcat-id" />
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label" for="edit-galcat-name">Nama Kategori</label>
            <input type="text" id="edit-galcat-name" name="name" required class="form-input" />
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="edit-galcat-slug">Slug Kunci Filter (JS)</label>
              <input type="text" id="edit-galcat-slug" name="slug" required class="form-input" />
            </div>
            <div class="form-group">
              <label class="form-label" for="edit-galcat-sort">Urutan Penampilan (Sort Order)</label>
              <input type="number" id="edit-galcat-sort" name="sort_order" min="0" required class="form-input" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeGalleryCategoryEditModal()">Batal</button>
          <button type="submit" class="btn-gold">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Live Preview Sync Logic
      const authorIn = document.getElementById('create-author');
      const dateIn = document.getElementById('create-date');
      
      const pMeta = document.getElementById('prev-meta');
      const pText = document.getElementById('prev-text');
      
      const mediaInput = document.querySelector('input[name="create_media_file"]');
      const mediaTypeRadios = document.querySelectorAll('input[name="create_media_type"]');
      const pMediaPreview = document.querySelector('#live-preview-card .queue-media-preview');
      
      function updatePreview() {
        const author = authorIn.value || 'Nama Penulis';
        const date = dateIn.value || 'Label Tanggal';
        if(pMeta) pMeta.innerText = `${date} • ${author}`;
        
        // Try to get tinymce content if it exists
        if (typeof tinymce !== 'undefined' && tinymce.get('create-text')) {
           const content = tinymce.get('create-text').getContent();
           if(pText && content) pText.innerHTML = content;
        }
      }

      function updateMediaPreview() {
        if (!mediaInput || !pMediaPreview) return;
        const file = mediaInput.files[0];
        const mediaType = document.querySelector('input[name="create_media_type"]:checked').value;
        
        if (file) {
          const fileURL = URL.createObjectURL(file);
          if (mediaType === 'video') {
             pMediaPreview.innerHTML = `<video src="${fileURL}#t=0.001" controls muted loop playsinline style="width:100%; max-height:400px; object-fit:cover; border-radius:8px 8px 0 0;"></video>`;
          } else {
             pMediaPreview.innerHTML = `<img src="${fileURL}" style="width:100%; max-height:400px; object-fit:cover; border-radius:8px 8px 0 0;" loading="lazy" decoding="async" alt="Panggonan Resto Image" />`;
          }
        } else {
          pMediaPreview.innerHTML = `<div class="no-media" style="color: #8696a0;">Visual Media</div>`;
        }
      }
      
      if(authorIn) authorIn.addEventListener('input', updatePreview);
      if(dateIn) dateIn.addEventListener('input', updatePreview);

      if(mediaInput) mediaInput.addEventListener('change', updateMediaPreview);
      mediaTypeRadios.forEach(r => r.addEventListener('change', updateMediaPreview));

      // Bind TinyMCE changes to live preview
      setTimeout(() => {
        if (typeof tinymce !== 'undefined' && tinymce.get('create-text')) {
          tinymce.get('create-text').on('keyup change', updatePreview);
        }
      }, 1500); // give time for tinymce to init
    });

    function openEditModal(id, data) {
      document.getElementById('edit-id').value = id;
      document.getElementById('edit-author').value = data.author || '';
      document.getElementById('edit-date').value = data.date_label || '';
      const textVal = data.text || '';
      document.getElementById('edit-text').value = textVal;
      if (window.tinymce && tinymce.get('edit-text')) {
        tinymce.get('edit-text').setContent(textVal);
      }
      document.getElementById('edit-existing-media-url').value = data.media_url || '';

      if (data.media_type === 'video') {
        document.getElementById('edit-type-video').checked = true;
      } else {
        document.getElementById('edit-type-image').checked = true;
      }

      document.getElementById('edit-modal').classList.add('active');
    }

    function closeEditModal() {
      document.getElementById('edit-modal').classList.remove('active');
    }

    // Close modal on ESC
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeEditModal();
        closeResEditModal();
      }
    });

    function openResEditModal(data) {
      document.getElementById('edit-res-id').value = data.id;
      document.getElementById('edit-res-name').value = data.name || '';
      document.getElementById('edit-res-phone').value = data.phone || '';
      document.getElementById('edit-res-branch').value = data.branch || '';
      document.getElementById('edit-res-event').value = data.event_type || '';
      document.getElementById('edit-res-date').value = data.reservation_date || '';
      document.getElementById('edit-res-time').value = data.reservation_time || '';
      document.getElementById('edit-res-pax').value = data.pax || 1;
      document.getElementById('edit-res-note').value = data.note || '';

      document.getElementById('edit-res-modal').classList.add('active');
    }

    function closeResEditModal() {
      document.getElementById('edit-res-modal').classList.remove('active');
    }

    // --- CATEGORY MODAL CONTROLLERS ---
    function openAddCategoryModal() {
      document.getElementById('add-category-modal').classList.add('active');
    }
    function closeAddCategoryModal() {
      document.getElementById('add-category-modal').classList.remove('active');
    }
    function openCategoryEditModal(data) {
      document.getElementById('edit-cat-id').value = data.id;
      document.getElementById('edit-cat-name').value = data.name || '';
      document.getElementById('edit-cat-column').value = data.column_position || 'left';
      document.getElementById('edit-cat-sort').value = data.sort_order || 0;
      document.getElementById('edit-category-modal').classList.add('active');
    }
    function closeCategoryEditModal() {
      document.getElementById('edit-category-modal').classList.remove('active');
    }

    // --- MENU MODAL CONTROLLERS ---
    function openAddMenuModal() {
      document.getElementById('add-menu-modal').classList.add('active');
    }
    function closeAddMenuModal() {
      document.getElementById('add-menu-modal').classList.remove('active');
    }
    function openMenuEditModal(data) {
      document.getElementById('edit-menu-id').value = data.id;
      document.getElementById('edit-menu-name').value = data.name || '';
      document.getElementById('edit-menu-cat').value = data.category_id || '';
      document.getElementById('edit-menu-desc').value = data.description || '';
      document.getElementById('edit-menu-price').value = data.price || 0;
      document.getElementById('edit-menu-sort').value = data.sort_order || 0;
      document.getElementById('edit-menu-avail').checked = (data.is_available == 1);
      document.getElementById('edit-menu-modal').classList.add('active');
    }
    function closeMenuEditModal() {
      document.getElementById('edit-menu-modal').classList.remove('active');
    }

    function openAddGalleryModal() {
      document.getElementById('add-gallery-modal').classList.add('active');
    }
    function closeAddGalleryModal() {
      document.getElementById('add-gallery-modal').classList.remove('active');
    }
    function openGalleryEditModal(img) {
      document.getElementById('edit-gallery-id').value = img.id;
      document.getElementById('edit-gallery-title-field').value = img.title;
      document.getElementById('edit-gallery-cat-field').value = img.category_id;
      document.getElementById('edit-gallery-modal').classList.add('active');
    }
    function closeGalleryEditModal() {
      document.getElementById('edit-gallery-modal').classList.remove('active');
    }

    function openAddGalleryCategoryModal() {
      document.getElementById('add-gallery-category-modal').classList.add('active');
    }
    function closeAddGalleryCategoryModal() {
      document.getElementById('add-gallery-category-modal').classList.remove('active');
    }
    function openGalleryCategoryEditModal(cat) {
      document.getElementById('edit-galcat-id').value = cat.id;
      document.getElementById('edit-galcat-name').value = cat.name || '';
      document.getElementById('edit-galcat-slug').value = cat.slug || '';
      document.getElementById('edit-galcat-sort').value = cat.sort_order || 0;
      document.getElementById('edit-gallery-category-modal').classList.add('active');
    }
    function closeGalleryCategoryEditModal() {
      document.getElementById('edit-gallery-category-modal').classList.remove('active');
    }

    // Expand ESC key handling
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeAddCategoryModal();
        closeCategoryEditModal();
        closeAddMenuModal();
        closeMenuEditModal();
        closeAddGalleryModal();
        closeGalleryEditModal();
        closeAddGalleryCategoryModal();
        closeGalleryCategoryEditModal();
      }
    });
  </script>
  <?php endif; ?>
  <!-- END MODALS -->

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.data-table').DataTable({
        "language": {
          "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
        },
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        "ordering": false // Order already managed by PHP queries
      });
    });
  </script>

  <!-- TinyMCE Initialization Script -->
  <script>
    // Focus management for tinymce within custom modals
    document.addEventListener('focusin', function(e) {
      if (e.target.closest('.tox-tinymce-aux, .moxman-window, .tam-assetmanager-root') !== null) {
        e.stopImmediatePropagation();
      }
    });
  </script>

  <?php if ($current_tab === 'traffic'): ?>
    <!-- Traffic Dashboard Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      <?php include __DIR__ . '/../assets/js/dashboard.js'; ?>
    </script>
  <?php endif; ?>
</body>
</html>
