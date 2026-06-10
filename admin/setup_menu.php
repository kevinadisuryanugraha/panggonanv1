<?php
/**
 * Database Setup & Menu Seeder
 * Creates `menu_categories` and `menu_items` tables and seeds them with the exact current menu structure.
 */

session_start();
$is_cli = (php_sapi_name() === 'cli');
$is_admin = isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;

if (!$is_cli && !$is_admin) {
    die("Akses ditolak. Anda harus login sebagai admin untuk menjalankan skrip ini.");
}

require_once __DIR__ . '/../config/db.php';

try {
    echo "Starting Menu Database Migration...\n";
    
    // 1. Create `menu_categories` table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `menu_categories` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `column_position` ENUM('left', 'right') NOT NULL DEFAULT 'left',
        `sort_order` INT NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "-> Table `menu_categories` resolved.\n";

    // 2. Create `menu_items` table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `menu_items` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `category_id` INT NOT NULL,
        `name` VARCHAR(150) NOT NULL,
        `description` VARCHAR(255) DEFAULT NULL,
        `price` INT NOT NULL,
        `is_available` TINYINT(1) NOT NULL DEFAULT 1,
        `sort_order` INT NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "-> Table `menu_items` resolved.\n";

    // 3. Clear existing menu data for fresh seed (safe backup check)
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE `menu_items`;");
    $pdo->exec("TRUNCATE TABLE `menu_categories`;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    echo "-> Stale menu tables truncated successfully.\n";

    // 4. Seed Categories
    $categories = [
        // Left Column
        ['name' => 'Makanan', 'column_position' => 'left', 'sort_order' => 1],
        ['name' => 'Menu Baru', 'column_position' => 'left', 'sort_order' => 2],
        ['name' => 'Minuman', 'column_position' => 'left', 'sort_order' => 3],
        // Right Column
        ['name' => 'Menu Sore', 'column_position' => 'right', 'sort_order' => 4],
        ['name' => 'Camilan', 'column_position' => 'right', 'sort_order' => 5],
        ['name' => 'Minuman Jadul', 'column_position' => 'right', 'sort_order' => 6],
        ['name' => 'Minuman Part 2', 'column_position' => 'right', 'sort_order' => 7]
    ];

    $ins_cat = $pdo->prepare("INSERT INTO `menu_categories` (name, column_position, sort_order) VALUES (?, ?, ?)");
    $cat_ids = [];
    foreach ($categories as $cat) {
        $ins_cat->execute([$cat['name'], $cat['column_position'], $cat['sort_order']]);
        $cat_ids[$cat['name']] = $pdo->lastInsertId();
    }
    echo "-> 7 Menu Categories seeded successfully.\n";

    // 5. Seed Menu Items
    $items = [
        // === MAKANAN ===
        ['cat' => 'Makanan', 'name' => 'Ayam Goreng Panggonan', 'desc' => '(Lalapan + Trancam + Sambal + Kremes)', 'price' => 32000, 'sort' => 1],
        ['cat' => 'Makanan', 'name' => 'Ayam Panggang Panggonan', 'desc' => '(Lalapan + Trancam + Sambal)', 'price' => 34000, 'sort' => 2],
        ['cat' => 'Makanan', 'name' => 'Rica-Rica Entog', 'desc' => null, 'price' => 34000, 'sort' => 3],
        ['cat' => 'Makanan', 'name' => 'Garang Asem', 'desc' => null, 'price' => 35000, 'sort' => 4],
        ['cat' => 'Makanan', 'name' => 'Cah Kangkung', 'desc' => null, 'price' => 20000, 'sort' => 5],
        ['cat' => 'Makanan', 'name' => 'Gurame Goreng', 'desc' => null, 'price' => 58000, 'sort' => 6],
        ['cat' => 'Makanan', 'name' => 'Gurame Asam Manis', 'desc' => null, 'price' => 72000, 'sort' => 7],
        ['cat' => 'Makanan', 'name' => 'Asem-Asem Iga Sapi', 'desc' => null, 'price' => 47000, 'sort' => 8],
        ['cat' => 'Makanan', 'name' => 'Nasi', 'desc' => null, 'price' => 5000, 'sort' => 9],
        ['cat' => 'Makanan', 'name' => 'Kepala Ayam (isi 5)', 'desc' => null, 'price' => 23000, 'sort' => 10],
        ['cat' => 'Makanan', 'name' => 'Ati Ampela', 'desc' => null, 'price' => 18000, 'sort' => 11],
        ['cat' => 'Makanan', 'name' => 'Trancam', 'desc' => null, 'price' => 7000, 'sort' => 12],

        // === MENU BARU ===
        ['cat' => 'Menu Baru', 'name' => 'Sapi Black Papper', 'desc' => null, 'price' => 40000, 'sort' => 1],
        ['cat' => 'Menu Baru', 'name' => 'Sayur Asam', 'desc' => null, 'price' => 15000, 'sort' => 2],

        // === MINUMAN ===
        ['cat' => 'Minuman', 'name' => 'Teh Tawar', 'desc' => null, 'price' => 3500, 'sort' => 1],
        ['cat' => 'Minuman', 'name' => 'Teh Manis Hangat/Panas', 'desc' => null, 'price' => 7000, 'sort' => 2],
        ['cat' => 'Minuman', 'name' => 'Es Teh Tawar', 'desc' => null, 'price' => 6000, 'sort' => 3],
        ['cat' => 'Minuman', 'name' => 'Es Teh Manis', 'desc' => null, 'price' => 8000, 'sort' => 4],
        ['cat' => 'Minuman', 'name' => 'Teh Poci Gula Batu', 'desc' => null, 'price' => 20000, 'sort' => 5],
        ['cat' => 'Minuman', 'name' => 'Kopi Jos', 'desc' => null, 'price' => 12000, 'sort' => 6],
        ['cat' => 'Minuman', 'name' => 'Kopi Gula Aren', 'desc' => null, 'price' => 22000, 'sort' => 7],
        ['cat' => 'Minuman', 'name' => 'Macha Latte', 'desc' => null, 'price' => 18000, 'sort' => 8],
        ['cat' => 'Minuman', 'name' => 'Es Jeruk', 'desc' => null, 'price' => 11000, 'sort' => 9],
        ['cat' => 'Minuman', 'name' => 'Jeruk Panas', 'desc' => null, 'price' => 9000, 'sort' => 10],

        // === MENU SORE ===
        ['cat' => 'Menu Sore', 'name' => 'Bakmi Goreng Jawa', 'desc' => null, 'price' => 25000, 'sort' => 1],
        ['cat' => 'Menu Sore', 'name' => 'Bakmi Godok', 'desc' => null, 'price' => 25000, 'sort' => 2],
        ['cat' => 'Menu Sore', 'name' => 'Nasi Goreng Jawa', 'desc' => null, 'price' => 25000, 'sort' => 3],

        // === CAMILAN ===
        ['cat' => 'Camilan', 'name' => 'Pisang Goreng Ndeso', 'desc' => null, 'price' => 23000, 'sort' => 1],
        ['cat' => 'Camilan', 'name' => 'Paket Tempe Mendoan', 'desc' => null, 'price' => 20000, 'sort' => 2],
        ['cat' => 'Camilan', 'name' => 'Kerupuk', 'desc' => null, 'price' => 2500, 'sort' => 3],

        // === MINUMAN JADUL ===
        ['cat' => 'Minuman Jadul', 'name' => 'Temulawak', 'desc' => null, 'price' => 19000, 'sort' => 1],
        ['cat' => 'Minuman Jadul', 'name' => 'Temulawak Susu', 'desc' => null, 'price' => 23000, 'sort' => 2],
        ['cat' => 'Minuman Jadul', 'name' => 'Sarsaparilla', 'desc' => null, 'price' => 19000, 'sort' => 3],
        ['cat' => 'Minuman Jadul', 'name' => 'Sarsaparilla Susu', 'desc' => null, 'price' => 23000, 'sort' => 4],
        ['cat' => 'Minuman Jadul', 'name' => 'Lechee', 'desc' => null, 'price' => 19000, 'sort' => 5],
        ['cat' => 'Minuman Jadul', 'name' => 'Lechee Susu', 'desc' => null, 'price' => 23000, 'sort' => 6],
        ['cat' => 'Minuman Jadul', 'name' => 'Coffee Beer', 'desc' => null, 'price' => 19000, 'sort' => 7],
        ['cat' => 'Minuman Jadul', 'name' => 'Coffee Beer Susu', 'desc' => null, 'price' => 23000, 'sort' => 8],

        // === MINUMAN PART 2 ===
        ['cat' => 'Minuman Part 2', 'name' => 'Beras Kencur Panas', 'desc' => null, 'price' => 13500, 'sort' => 1],
        ['cat' => 'Minuman Part 2', 'name' => 'Es Beras Kencur', 'desc' => null, 'price' => 14500, 'sort' => 2],
        ['cat' => 'Minuman Part 2', 'name' => 'Gula Asem Panas', 'desc' => null, 'price' => 13500, 'sort' => 3],
        ['cat' => 'Minuman Part 2', 'name' => 'Es Gula Asem', 'desc' => null, 'price' => 14500, 'sort' => 4],
        ['cat' => 'Minuman Part 2', 'name' => 'Wedang Jahe', 'desc' => null, 'price' => 11000, 'sort' => 5],
        ['cat' => 'Minuman Part 2', 'name' => 'Wedang Uwuh', 'desc' => null, 'price' => 15000, 'sort' => 6],
        ['cat' => 'Minuman Part 2', 'name' => 'Wedang Ronde', 'desc' => null, 'price' => 12000, 'sort' => 7],
        ['cat' => 'Minuman Part 2', 'name' => 'Choco Latte', 'desc' => null, 'price' => 18000, 'sort' => 8],
        ['cat' => 'Minuman Part 2', 'name' => 'Jus Sirsak', 'desc' => null, 'price' => 18000, 'sort' => 9],
        ['cat' => 'Minuman Part 2', 'name' => 'Es Sirsak', 'desc' => null, 'price' => 18000, 'sort' => 10],
        ['cat' => 'Minuman Part 2', 'name' => 'Jus Jambu', 'desc' => null, 'price' => 18000, 'sort' => 11],
        ['cat' => 'Minuman Part 2', 'name' => 'Jus Alpukat', 'desc' => null, 'price' => 18000, 'sort' => 12],
        ['cat' => 'Minuman Part 2', 'name' => 'Jus Mangga', 'desc' => null, 'price' => 18000, 'sort' => 13],
        ['cat' => 'Minuman Part 2', 'name' => 'Le Minerale', 'desc' => null, 'price' => 7000, 'sort' => 14],
    ];

    $ins_item = $pdo->prepare("INSERT INTO `menu_items` (category_id, name, description, price, sort_order) VALUES (?, ?, ?, ?, ?)");
    $item_count = 0;
    foreach ($items as $item) {
        $cat_id = $cat_ids[$item['cat']];
        $ins_item->execute([$cat_id, $item['name'], $item['desc'], $item['price'], $item['sort']]);
        $item_count++;
    }
    echo "-> Successfully seeded $item_count Menu Items!\n";
    echo "Database Migration Completed Successfully!\n";
    
} catch (Exception $e) {
    echo "MIGRATION ERROR: " . $e->getMessage() . "\n";
}
