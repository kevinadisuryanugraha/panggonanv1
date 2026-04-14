# Panggonan Resto - Premium Website 🍽️

Sebuah proyek website statis berkelas premium (Editorial Style) yang dirancang khusus untuk merepresentasikan identitas, sejarah, dan layanan dari **Panggonan Resto** (Cabang GDC Depok & Ciracas).

Proyek ini telah dikembangkan dengan fokus pada seni _storytelling_ visual (seperti kutipan filosofi "Kluyuran"), performa yang sangat cepat, dan fungsionalitas pemesanan (reservasi) langsung yang terintegrasi dengan WhatsApp multi-cabang.

---

## 🌟 Fitur Utama

- **Desain Premium & Asimetris (Opsi C)**: Menggunakan pola desain terang (_light-theme_) dengan kombinasi warna _Cream, Gold_, dan _Black_ untuk menonjolkan kesan eksklusif dan elegan.
- **Sistem Grid Responsif (Bento Grid)**: Tata letak _masonry_ modern pada galeri "Atmosfer Panggonan" dan "Nilai Tambah Layanan" yang secara otomatis menyesuaikan (_responsive_) secara sempurna di resolusi Desktop, Layar Tablet (iPad), hingga _Mobile_.
- **Integrasi Reservasi WhatsApp Dinamis**: Formulir pemesanan tiket/meja/event yang menggunakan _Vanilla JavaScript_ untuk memproses input (Nama Lengkap, Pilihan Cabang, Tanggal, dll) dan menyusunnya menjadi pesan WhatsApp siap kirim yang rapi secara otomatis.
- **Kemandirian Infrastruktur (100% _Self-Hosted_)**: Seluruh aset kode (HTML, CSS, JS), ikon SVG, dan ilustrasi gambar telah diisolasi secara lokal. Tidak ada lagi ketergantungan pada _server_ luar atau CDN pihak ketiga (seperti _Webflow_).
- **SEO & Meta Siap Rilis**: Telah dioptimasi dengan struktur tag _Heading_, _Meta Description_, dan grafis _Open Graph_ (OG) yang relevan untuk mesin pencari Google.

## 🛠️ Tumpukan Teknologi (Tech Stack)

Website ini adalah **Static Site** (Situs Statis) murni yang tidak membutuhkan _Database_ atau bahasa prosesor sisi server (_Backend_ seperti PHP/Node.js). Sangat ringan dan aman.

- **Struktur**: HTML5 Semantik
- **Gaya (Styling)**: CSS3 Lanjutan (Animasi transisi, _CSS Variables_, _Flexbox_, dan _Grid Layout_)
- **Fungsionalitas**: _Vanilla JavaScript_ (ES6) dan _jQuery v3.5.1_ (opsional untuk animasi bawaan)
- **Desain Font**: Sora (Google Fonts)

## 📁 Struktur Direktori Proyek

```text
📁 conc-panggonanv1 - Copy/
├── 📁 about-us/          # Halaman Tentang Kami (Filosofi & Sejarah Upcycle)
├── 📁 assets/
│   ├── 📁 css/           # Kumpulan Stylesheets (style.css, custom.css, home.css)
│   ├── 📁 images/        # Seluruh aset gambar (resolusi tinggi & ikon SVG)
│   └── 📁 js/            # Skrip Logika Website (Navigasi & Form WhatsApp)
├── 📁 blog/              # (Draft) Direktori Artikel Jurnal
├── 📁 contact-us/        # Halaman Kontak & Form Reservasi WhatsApp (Multi-Cabang)
├── 📁 faq/               # Halaman Pusat Bantuan & Pertanyaan Interaktif
├── 📁 menu/              # Halaman Katalog Sajian & Resep
├── 📁 services/          # Halaman Kategori Layanan Spesial Panggonan
├── index.html            # Halaman Utama (Beranda / Homepage)
├── laporan_progress_owner.md # Laporan Final Status Pengerjaan (Ref. Eksekusi 100%)
└── README.md             # Dokumentasi Proyek Ini
```

## 🚀 Versi 1.1 (Update April 2026)
Keseluruhan website telah disempurnakan berdasarkan arahan final:
- **Standarisasi Universal Kontak**: Kontak WA Ciracas (`0878-2888-8538`) & GDC (`0878-4535-9184`), integrasi Instagram/TikTok resmi di seluruh laman.
- **Identitas "KLU YU RAN"**: Menginjeksi nilai filosofi dan puisi otentik ("Merekah senyum seindah pagi...") pada laman Tentang Kami.
- **Peningkatan Responsivitas Antarmuka**: Redesain Grid _Footer_ menjadi format 4-kolom super responsif, pembersihan _white-space_, penetapan Sub-Navigasi Galeri terintegrasi, dan pembaruan Hak Cipta &copy; 2026.

## 🚀 Panduan Peluncuran (_Deployment_)

Karena struktur proyek ini adalah **Situs Statis (Static HTML)**, proses menaikkan website ini ke Internet agar dapat diakses publik sangatlah mudah dan tidak memerlukan _cPanel_ konvensional.

Kami sangat merekomendasikan menggunakan layanan seperti **Netlify**, **Vercel**, atau **GitHub Pages** (gratis dengan SSL/HTTPS terpasang otomatis).

### Cara _Hosting_ via Netlify Drop:

1. Buka peramban (_browser_) dan kunjungi [Netlify Drop](https://drop.netlify.com/).
2. Buka aplikasi _File Explorer_ (Windows/Mac) Anda.
3. Arahkan direktori Windows ke folder utama proyek ini:
   `c:\laragon\www\porto-apps\50+client\panggonan_version\conc-panggonanv1 - Copy`
4. Lakukan **Drag & Drop** (Tarik & Pisahkan) keseluruhan isi folder utama tersebut tepat di atas antarmuka layar Netlify Drop tadi.
5. Tunggu proses kompilasi beberapa detik, lalu website siap meluncur (_Live_).
6. Masuk ke manu _Domain Management_ di pengaturan akun Netlify untuk menyambungkan dengan domain asli (seperti `panggonan.id` atau `panggonanresto.com`).

---

## 🎨 Panduan Modifikasi & Pemeliharaan (Maintenance)

- **Mengubah Desain Warna Utama**: Tema primer (terutama _Gold_ dan _Cream_) dapat dengan mudah diubah nilainya melalui inisialisasi _:root_ pada file utama `assets/css/style.css` garis awal (baris `1 - 50`).
- **Nomor Telepon Reservasi**: Jika ada perubahan pada nomor layanan WhatsApp, Anda hanya perlu mengutak-atik fungsi JavaScript pada file `contact-us/index.html` (di baris `<script>` paling dasar dokumen halaman).
- **Penambahan Aset Baru**: Pastikan untuk selalu menggunakan format `.webp` atau mengkompresi `.jpeg / .png` terlebih dahulu sebelum meletakkannya di _folder_ `assets/images/` untuk menjaga metrik kecepatan _loading_ halaman (PageSpeed) tetap di angka > 90.

---

> _Dikembangkan dan direvitalisasi khusus oleh Panggonan Developer Team & Antigravity Assistant. Seluruh hak cipta desain konten dimiliki oleh Panggonan, 2026._
