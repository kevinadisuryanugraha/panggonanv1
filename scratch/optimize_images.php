<?php
/**
 * Panggonan Resto — Skrip Otomatisasi Kompresi WebP
 * Mengompresi file gambar WebP raksasa di direktori assets/images/
 * guna meningkatkan skor performa Lighthouse secara signifikan.
 */

// Menentukan batas memori tinggi untuk manipulasi gambar
ini_set('memory_limit', '512M');

$images = [
    __DIR__ . '/../assets/images/panggonan_aset_ke_2/hero.webp',
    __DIR__ . '/../assets/images/panggonan_aset_ke_2/ayam_goreng.webp',
    __DIR__ . '/../assets/images/panggonan_aset_ke_2/pisang_goreng.webp',
    __DIR__ . '/../assets/images/panggonan_aset_ke_2/rendang.webp',
];

echo "=============================================\n";
echo " Panggonan Resto — Mulai Kompresi Gambar WebP\n";
echo "=============================================\n\n";

foreach ($images as $path) {
    if (!file_exists($path)) {
        echo "⚠️  File tidak ditemukan: " . basename($path) . "\n";
        continue;
    }

    $original_size = filesize($path);
    $filename = basename($path);

    echo "Mengompresi $filename...\n";
    echo "  - Ukuran Asal: " . number_format($original_size / 1024, 2) . " KB\n";

    // 1. Memuat gambar WebP menggunakan GD secara aman via string
    $img = @imagecreatefromstring(file_get_contents($path));
    if (!$img) {
        echo "  ❌ Gagal memuat file WebP: $filename\n\n";
        continue;
    }

    // Mendapatkan resolusi asli
    $width = imagesx($img);
    $height = imagesy($img);
    echo "  - Resolusi: {$width}x{$height} piksel\n";

    // Jika resolusi hero sangat raksasa (misal > 3000px), kita bisa me-resize-nya secara proporsional demi efisiensi render
    // Tetapi karena WebP asal sudah dalam format WebP, kita kompresi langsung dengan parameter kualitas 75
    // 2. Tulis ulang dengan kualitas optimal 75
    $temp_path = $path . '.tmp';
    if (imagewebp($img, $temp_path, 75)) {
        // Hancurkan resource gambar di memori
        imagedestroy($img);

        $compressed_size = filesize($temp_path);
        
        // Pastikan ukuran baru lebih kecil, lalu gantikan file lama
        if ($compressed_size < $original_size) {
            unlink($path);
            rename($temp_path, $path);
            
            $saving = $original_size - $compressed_size;
            $percent = ($saving / $original_size) * 100;
            
            echo "  ✅ Sukses Kompresi!\n";
            echo "  - Ukuran Baru: " . number_format($compressed_size / 1024, 2) . " KB\n";
            echo "  - Penghematan: " . number_format($saving / 1024, 2) . " KB (" . number_format($percent, 1) . "%)\n\n";
        } else {
            // Jika ukuran hasil kompresi ternyata lebih besar (jarang terjadi pada kualitas 75), batalkan
            unlink($temp_path);
            echo "  ℹ️ File asal sudah sangat optimal. Tidak ada perubahan dilakukan.\n\n";
        }
    } else {
        imagedestroy($img);
        echo "  ❌ Gagal melakukan kompresi WebP: $filename\n\n";
    }
}

echo "=============================================\n";
echo " Proses Kompresi Selesai!\n";
echo "=============================================\n";
