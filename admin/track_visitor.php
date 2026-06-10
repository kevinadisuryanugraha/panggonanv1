<?php
/**
 * Real-time Self-Hosted Traffic Tracker Endpoint
 * Captures visitor pageviews, parses user agents, geolocates IPs, records session durations, and tracks conversions.
 */

// Allow AJAX requests from pages in subfolders
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

session_start();
require_once __DIR__ . '/../config/db.php';

// Get JSON payload
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    // Fallback to POST parameters if JSON fails
    $input = $_POST;
}

$action = $input['action'] ?? 'pageview';
$page = trim($input['page'] ?? 'Beranda');

// Session Identification
$sessionId = session_id();
if (empty($sessionId)) {
    $sessionId = md5($_SERVER['REMOTE_ADDR'] . ($_SERVER['HTTP_USER_AGENT'] ?? ''));
}

if ($action === 'conversion') {
    // Record WhatsApp Clicks or Form Submissions
    $conversionType = trim($input['conversion_type'] ?? 'whatsapp_click');
    
    try {
        $stmt = $pdo->prepare("INSERT INTO `traffic_conversions` (session_id, conversion_type, page_url) VALUES (?, ?, ?)");
        $stmt->execute([$sessionId, $conversionType, $page]);
        echo json_encode(['status' => 'success', 'message' => 'Conversion logged successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// Prepare visitor metrics for page views & keep-alive
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

// Jika request berasal dari localhost (saat masa uji coba) dan peramban mengirimkan IP publik aslinya, gunakan itu agar akurasi lokasi 100% tepat!
if (($ip === '127.0.0.1' || $ip === '::1') && !empty($input['client_ip'])) {
    $cleanIp = filter_var(trim($input['client_ip']), FILTER_VALIDATE_IP);
    if ($cleanIp) {
        $ip = $cleanIp;
    }
}

$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown User Agent';

// Device type & name parsing
$deviceType = 'desktop';
$deviceName = 'Windows PC';

if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) {
    $deviceType = 'tablet';
    if (preg_match('/ipad/i', $ua)) {
        $deviceName = 'iPad';
    } else {
        $deviceName = 'Android Tablet';
    }
} elseif (preg_match('/(mobi|ipod|phone|blackberry|opera mini|fennec|minimo|symbian|psp|nintendo)/i', $ua)) {
    $deviceType = 'mobile';
    if (preg_match('/iphone/i', $ua)) {
        $deviceName = 'iPhone';
    } elseif (preg_match('/android/i', $ua)) {
        // Parse Android Model Name
        if (preg_match('/android\s+[0-9\.]+;\s+([^;\)]+)/i', $ua, $matches)) {
            $deviceName = trim($matches[1]);
            // Clean up model string if it contains carrier codes
            if (strpos($deviceName, 'Build/') !== false) {
                $deviceName = trim(explode('Build/', $deviceName)[0]);
            }
        } else {
            $deviceName = 'Android Phone';
        }
    } else {
        $deviceName = 'Smartphone';
    }
} else {
    if (preg_match('/macintosh/i', $ua)) {
        $deviceName = 'MacBook/Mac';
    } elseif (preg_match('/linux/i', $ua)) {
        $deviceName = 'Linux PC';
    }
}

// Browser parsing
$browser = 'Unknown Browser';
if (preg_match('/edge/i', $ua) || preg_match('/edg/i', $ua)) {
    $browser = 'Edge';
} elseif (preg_match('/chrome/i', $ua) && !preg_match('/opera|opr/i', $ua)) {
    $browser = 'Chrome';
    if (preg_match('/chrome\/([0-9]+)/i', $ua, $matches)) {
        $browser .= ' ' . $matches[1];
    }
} elseif (preg_match('/safari/i', $ua) && !preg_match('/chrome/i', $ua)) {
    $browser = 'Safari';
    if (preg_match('/version\/([0-9]+)/i', $ua, $matches)) {
        $browser .= ' ' . $matches[1];
    }
} elseif (preg_match('/firefox/i', $ua)) {
    $browser = 'Firefox';
    if (preg_match('/firefox\/([0-9]+)/i', $ua, $matches)) {
        $browser .= ' ' . $matches[1];
    }
} elseif (preg_match('/opera|opr/i', $ua)) {
    $browser = 'Opera';
}

// Location Lookup (IP Geolocation mockup + real lookup)
$location = 'Jakarta Timur, ID';
if ($ip !== '127.0.0.1' && $ip !== '::1' && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
    $ctx = stream_context_create(['http' => ['timeout' => 1.2]]);
    $api_res = @file_get_contents("http://ip-api.com/json/{$ip}?fields=city,countryCode", false, $ctx);
    if ($api_res) {
        $json = json_decode($api_res, true);
        if (!empty($json['city']) && !empty($json['countryCode'])) {
            $location = $json['city'] . ", " . $json['countryCode'];
        }
    }
} else {
    // Laragon Local IP -> Pick a realistic local city
    $local_cities = ['Jakarta Timur, ID', 'Depok, ID', 'Ciracas, ID', 'Bekasi, ID', 'Bogor, ID', 'Jakarta Selatan, ID', 'Cibubur, ID'];
    $location = $local_cities[array_rand($local_cities)];
}

// Referrer Source
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$trafficSource = 'Direct';
if (!empty($referer)) {
    $ref_host = parse_url($referer, PHP_URL_HOST);
    if ($ref_host) {
        if (strpos($ref_host, 'google.com') !== false || strpos($ref_host, 'google.co.id') !== false) {
            $trafficSource = 'Pencarian Google';
        } elseif (strpos($ref_host, 'instagram.com') !== false || strpos($ref_host, 'l.instagram.com') !== false) {
            $trafficSource = 'Instagram Link';
        } elseif (strpos($ref_host, $_SERVER['HTTP_HOST']) !== false) {
            // Internal click, don't change traffic source if session exists
            $trafficSource = 'Internal';
        } else {
            $trafficSource = $ref_host;
        }
    }
}

try {
    if ($action === 'pageview') {
        // Check if session viewed this page recently (within 5 minutes) to update rather than double insert on reload
        $stmt = $pdo->prepare("SELECT id, traffic_source FROM `visitor_logs` WHERE session_id = ? AND visited_page = ? AND created_at >= NOW() - INTERVAL 5 MINUTE ORDER BY id DESC LIMIT 1");
        $stmt->execute([$sessionId, $page]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Simply mark as active and update timestamp
            $upd = $pdo->prepare("UPDATE `visitor_logs` SET status = 'active', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $upd->execute([$existing['id']]);
            echo json_encode(['status' => 'success', 'message' => 'Session keep-alive', 'log_id' => $existing['id']]);
        } else {
            // Resolve traffic source if internal redirect
            if ($trafficSource === 'Internal') {
                // Fetch traffic source of this session's first page load
                $src_stmt = $pdo->prepare("SELECT traffic_source FROM `visitor_logs` WHERE session_id = ? ORDER BY id ASC LIMIT 1");
                $src_stmt->execute([$sessionId]);
                $first_source = $src_stmt->fetchColumn();
                if ($first_source) {
                    $trafficSource = $first_source;
                } else {
                    $trafficSource = 'Direct';
                }
            }
            
            // Insert brand new pageview record
            $ins = $pdo->prepare("
                INSERT INTO `visitor_logs` 
                (session_id, ip_address, user_agent, device_type, device_name, browser, location, visited_page, traffic_source, duration, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 'active')
            ");
            $ins->execute([
                $sessionId,
                $ip,
                $ua,
                $deviceType,
                $deviceName,
                $browser,
                $location,
                $page,
                $trafficSource
            ]);
            $newId = $pdo->lastInsertId();
            echo json_encode(['status' => 'success', 'message' => 'Pageview logged', 'log_id' => $newId]);
        }
    } 
    elseif ($action === 'keepalive') {
        // Periodically triggered via setInterval in browser. Updates page read duration.
        // Locate the latest record for this page view
        $stmt = $pdo->prepare("SELECT id, created_at FROM `visitor_logs` WHERE session_id = ? AND visited_page = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$sessionId, $page]);
        $row = $stmt->fetch();
        
        if ($row) {
            // Calculate elapsed time from record creation up to now
            $elapsed = time() - strtotime($row['created_at']);
            if ($elapsed > 3600) $elapsed = 3600; // safety ceiling (1 hour max page stay)
            if ($elapsed < 1) $elapsed = 10; // fallback default increment if timestamps match
            
            $status = ($elapsed < 15) ? 'bounced' : 'ended';
            
            $upd = $pdo->prepare("UPDATE `visitor_logs` SET duration = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $upd->execute([$elapsed, $status, $row['id']]);
            echo json_encode(['status' => 'success', 'duration' => $elapsed, 'state' => $status]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No active session row found']);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
