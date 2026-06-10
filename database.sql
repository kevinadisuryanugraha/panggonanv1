-- =====================================================
-- Panggonan Resto — Skema Database Jurnal
-- Jalankan file ini di phpMyAdmin atau MySQL CLI
-- =====================================================

CREATE DATABASE IF NOT EXISTS `panggonan_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `panggonan_db`;

CREATE TABLE IF NOT EXISTS `journals` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `author` VARCHAR(100) NOT NULL,
  `quote` VARCHAR(500) NOT NULL,
  `text` TEXT NOT NULL,
  `media_type` ENUM('image', 'video') DEFAULT 'image',
  `media_url` VARCHAR(255) NOT NULL DEFAULT '',
  `date_label` VARCHAR(100) NOT NULL DEFAULT '',
  `status` ENUM('pending', 'approved') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Data Awal: 3 Jurnal Founder Bawaan (Langsung Tayang)
-- =====================================================

INSERT INTO `journals` (`author`, `quote`, `text`, `media_type`, `media_url`, `date_label`, `status`) VALUES
(
  'Bp. Masyuri (Founder)',
  '"Satu | KLU sy dpt dari Mas Prof. Topo | YU saat ngopi² sesaat | RAN"',
  'what then s e n i ooor.. @ kopi KLU YU RAN<br /><br />pada setiap K A R Y A disana ada sidik jari kita...<br /><br />yaaa KLU YU RAN yg memberi insight kebermanfaatan.<br />Tetiba ketemuan dan lanjut jadi bincang² yg mencerahkan..',
  'image',
  'assets/images/jurnal/kluyuran1.webp',
  'Founder Story',
  'approved'
),
(
  'Bp. Masyuri (Founder)',
  '"t a k c l u e | y o u | r u n.<br />Satu h a r i satu Silaturahmi.."',
  'Mereka h s e n y u m s e i n d a h pagi<br />b e r c e n g k r a m a s e t u l u s h a t i<br />D a m a i s e n t o s a rasa b a h a g i a<br /><br />Smngt p a g i Indonesia',
  'video',
  'assets/images/jurnal/kluyuran2.mp4',
  'Founder Story',
  'approved'
),
(
  'Bp. Masyuri (Founder)',
  '"@panggonan, g a k c l u e | y o u | r u n"',
  'Pagi h a r i, rinai menyapa bukan l e b a t, hanya rintik syahdu<br />l a m p u jalan masih m e n y a l a pantulannya m e n a r i di genangan<br />S i l u e t pohon, basah dan I n d a h<br /><br />Menjelma l u k i s a n d i k a n v a s pagi<br /><br />Smngt p a g i Indonesia',
  'image',
  'assets/images/panggonan_aset_ke_2/ambiance_luar_malam.webp',
  'Founder Story',
  'approved'
);

-- =====================================================
-- Skema Tabel Reservasi Terpadu
-- =====================================================

CREATE TABLE IF NOT EXISTS `reservations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL DEFAULT '',
  `branch` VARCHAR(50) NOT NULL,
  `event_type` VARCHAR(100) NOT NULL,
  `reservation_date` DATE NOT NULL,
  `reservation_time` TIME DEFAULT NULL,
  `pax` INT NOT NULL,
  `note` TEXT,
  `status` ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

