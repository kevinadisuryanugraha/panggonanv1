<?php
/**
 * Setup and Seeder Script for Panggonan Traffic Analytics System
 * Run once to create database tables and populate 30 days of realistic history.
 */

// Security: limit to localhost or logged in admin
session_start();
$is_cli = (php_sapi_name() === 'cli');
$is_admin = isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;

if (!$is_cli && !$is_admin) {
    die("Akses ditolak. Anda harus login sebagai admin untuk menjalankan skrip ini.");
}

require_once __DIR__ . '/../config/db.php';

try {
    echo "🏗️  Memulai Setup Database Traffic Analytics...\n";
    if (!$is_cli) echo "<pre>";

    // 1. CREATE TABLES
    echo "Creating 'visitor_logs' table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `visitor_logs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `session_id` VARCHAR(100) NOT NULL,
            `ip_address` VARCHAR(45) NOT NULL,
            `user_agent` VARCHAR(255) NOT NULL,
            `device_type` ENUM('desktop', 'mobile', 'tablet') DEFAULT 'desktop',
            `device_name` VARCHAR(100) DEFAULT 'Unknown',
            `browser` VARCHAR(100) DEFAULT 'Unknown',
            `location` VARCHAR(100) DEFAULT 'Unknown',
            `visited_page` VARCHAR(100) NOT NULL,
            `traffic_source` VARCHAR(100) DEFAULT 'Direct',
            `duration` INT DEFAULT 0,
            `status` ENUM('active', 'ended', 'bounced') DEFAULT 'active',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX (`session_id`),
            INDEX (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "Creating 'traffic_conversions' table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `traffic_conversions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `session_id` VARCHAR(100) NOT NULL,
            `conversion_type` VARCHAR(100) NOT NULL,
            `page_url` VARCHAR(100) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (`session_id`),
            INDEX (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "✅ Tabel database berhasil dibuat.\n\n";

    // 2. CHECK IF DATA EXISTS
    $count = $pdo->query("SELECT COUNT(*) FROM `visitor_logs`")->fetchColumn();
    if ($count > 100) {
        echo "⚠️  Database sudah berisi {$count} entri data log. Seeding dilewati agar tidak menumpuk.\n";
        echo "Setup Selesai!\n";
        if (!$is_cli) echo "</pre>";
        exit;
    }

    // 3. SEEDING 30-DAY HISTORICAL DATA
    echo "🌱 Memulai Seeding Data Riwayat 30 Hari Terakhir...\n";

    $pages = ['Beranda', 'Menu', 'Tentang Kami', 'Layanan', 'Galeri', 'Tanya Jawab', 'Jurnal', 'Hubungi Kami'];
    
    $devices = [
        ['type' => 'mobile', 'name' => 'iPhone 15', 'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Mobile/15E148 Safari/604.1'],
        ['type' => 'mobile', 'name' => 'Samsung Galaxy S24', 'ua' => 'Mozilla/5.0 (Linux; Android 14; SM-S928B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36'],
        ['type' => 'mobile', 'name' => 'Xiaomi 14', 'ua' => 'Mozilla/5.0 (Linux; Android 14; 23127PN0CG) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36'],
        ['type' => 'mobile', 'name' => 'OPPO Find X7', 'ua' => 'Mozilla/5.0 (Linux; Android 14; PHZ110) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36'],
        ['type' => 'desktop', 'name' => 'Windows PC', 'ua' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36'],
        ['type' => 'desktop', 'name' => 'MacBook Pro', 'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36'],
        ['type' => 'tablet', 'name' => 'iPad Pro', 'ua' => 'Mozilla/5.0 (iPad; CPU OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Mobile/15E148 Safari/604.1'],
    ];

    $browsers = ['Chrome 124', 'Safari 17.4', 'Firefox 125', 'Edge 124', 'Chrome Mobile 124'];

    $locations = [
        'Jakarta Timur, ID', 'Depok, ID', 'Ciracas, ID', 'Bekasi, ID', 'Bogor, ID',
        'Jakarta Selatan, ID', 'Jakarta Barat, ID', 'Bandung, ID', 'Cibubur, ID', 'Tangerang, ID'
    ];

    $ip_prefixes = ['114.124', '180.252', '36.72', '182.1', '110.136', '120.188', '103.3'];
    $sources = ['Pencarian Google', 'Instagram Link', 'Direct'];
    $statuses = ['ended', 'ended', 'ended', 'ended', 'ended', 'bounced']; // mostly ended, some bounce

    // Seed around 1,500 visitor sessions
    $total_sessions = 1800;
    
    // Batch inserts for speed
    $log_stmt = $pdo->prepare("
        INSERT INTO `visitor_logs` 
        (session_id, ip_address, user_agent, device_type, device_name, browser, location, visited_page, traffic_source, duration, status, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $conv_stmt = $pdo->prepare("
        INSERT INTO `traffic_conversions` 
        (session_id, conversion_type, page_url, created_at) 
        VALUES (?, ?, ?, ?)
    ");

    $inserted_logs = 0;
    $inserted_convs = 0;

    echo "Generating database entries...\n";

    for ($i = 0; $i < $total_sessions; $i++) {
        $sessionId = md5(uniqid('session_' . $i, true));
        $ip = $ip_prefixes[array_rand($ip_prefixes)] . '.' . rand(1, 254) . '.' . rand(1, 254);
        
        $device = $devices[array_rand($devices)];
        $browser = $browsers[array_rand($browsers)];
        $location = $locations[array_rand($locations)];
        $source = $sources[array_rand($sources)];
        
        // Random date in the last 30 days
        $daysBack = rand(0, 30);
        $hour = rand(8, 23); // realistic open hours
        $minute = rand(0, 59);
        $second = rand(0, 59);
        
        // Distribute timestamp over last 30 days
        $createdTime = strtotime("-{$daysBack} days");
        $createdTime = strtotime(date('Y-m-d', $createdTime) . " {$hour}:{$minute}:{$second}");
        $createdDateStr = date('Y-m-d H:i:s', $createdTime);
        
        // Determine number of page views in this session (1 to 5)
        $session_pages_count = rand(1, 5);
        $session_pages = array_values($pages);
        shuffle($session_pages);
        
        $status = $statuses[array_rand($statuses)];
        $is_bounce = ($status === 'bounced');
        
        $session_duration = 0;
        
        for ($p = 0; $p < $session_pages_count; $p++) {
            if ($is_bounce && $p > 0) break; // Bounced users only view 1 page
            
            $page = $session_pages[$p];
            $page_duration = $is_bounce ? rand(2, 8) : rand(15, 300);
            $session_duration += $page_duration;
            
            $page_status = ($p === $session_pages_count - 1) ? ($is_bounce ? 'bounced' : 'ended') : 'ended';
            
            // Backdate page view slightly if multiple pages in session
            $pageCreatedTime = $createdTime + $session_duration;
            $pageCreatedStr = date('Y-m-d H:i:s', $pageCreatedTime);
            
            $log_stmt->execute([
                $sessionId,
                $ip,
                $device['ua'],
                $device['type'],
                $device['name'],
                $browser,
                $location,
                $page,
                $source,
                $page_duration,
                $page_status,
                $pageCreatedStr,
                $pageCreatedStr
            ]);
            
            $inserted_logs++;
            
            // Check for conversions! 9.5% conversion rate
            if (!$is_bounce && rand(1, 100) <= 9) {
                // Determine conversion type: 60% whatsapp_click, 40% maps_click
                $conv_type = (rand(1, 10) <= 6) ? 'whatsapp_click' : 'maps_click';
                $conv_page = ($conv_type === 'maps_click') ? 'Hubungi Kami' : $page;
                
                $conv_stmt->execute([
                    $sessionId,
                    $conv_type,
                    $conv_page,
                    $pageCreatedStr
                ]);
                $inserted_convs++;
            }
        }
        
        // Flush every 200 sessions to prevent timeouts
        if ($i % 200 === 0) {
            echo "   Inserted: {$i} sessions...\n";
            if (!$is_cli) flush();
        }
    }

    echo "\n🌱 Seeding selesai!\n";
    echo "📊 Total Logs dimasukkan       : " . number_format($inserted_logs) . " halaman\n";
    echo "📊 Total Conversions dimasukkan : " . number_format($inserted_convs) . " klik/form\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🎉 SISTEM TRAFFIC ANALYTICS BERHASIL DIAKTIFKAN!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    if (!$is_cli) echo "</pre>";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    if (!$is_cli) echo "</pre>";
}
