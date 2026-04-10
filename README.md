# Panggonan Resto - Premium Website 🍽️

Sebuah proyek website statis berkelas premium (Editorial Style) yang dirancang khusus untuk merepresentasikan identitas, sejarah, dan layanan dari **Panggonan Resto** (Cabang GDC Depok & Ciracas).

Proyek ini telah dikembangkan dengan fokus pada seni *storytelling* visual (seperti kutipan filosofi "Clueyourun"), performa yang sangat cepat, dan fungsionalitas pemesanan (reservasi) langsung yang terintegrasi dengan WhatsApp multi-cabang.

---

## 🌟 Fitur Utama

- **Desain Premium & Asimetris (Opsi C)**: Menggunakan pola desain terang (*light-theme*) dengan kombinasi warna *Cream, Gold*, dan *Black* untuk menonjolkan kesan eksklusif dan elegan.
- **Sistem Grid Responsif (Bento Grid)**: Tata letak *masonry* modern pada galeri "Atmosfer Panggonan" dan "Nilai Tambah Layanan" yang secara otomatis menyesuaikan (*responsive*) secara sempurna di resolusi Desktop, Layar Tablet (iPad), hingga *Mobile*.
- **Integrasi Reservasi WhatsApp Dinamis**: Formulir pemesanan tiket/meja/event yang menggunakan *Vanilla JavaScript* untuk memproses input (Nama Lengkap, Pilihan Cabang, Tanggal, dll) dan menyusunnya menjadi pesan WhatsApp siap kirim yang rapi secara otomatis.
- **Kemandirian Infrastruktur (100% *Self-Hosted*)**: Seluruh aset kode (HTML, CSS, JS), ikon SVG, dan ilustrasi gambar telah diisolasi secara lokal. Tidak ada lagi ketergantungan pada *server* luar atau CDN pihak ketiga (seperti *Webflow*).
- **SEO & Meta Siap Rilis**: Telah dioptimasi dengan struktur tag *Heading*, *Meta Description*, dan grafis *Open Graph* (OG) yang relevan untuk mesin pencari Google.

## 🛠️ Tumpukan Teknologi (Tech Stack)

Website ini adalah **Static Site** (Situs Statis) murni yang tidak membutuhkan *Database* atau bahasa prosesor sisi server (*Backend* seperti PHP/Node.js). Sangat ringan dan aman.

- **Struktur**: HTML5 Semantik
- **Gaya (Styling)**: CSS3 Lanjutan (Animasi transisi, *CSS Variables*, *Flexbox*, dan *Grid Layout*)
- **Fungsionalitas**: *Vanilla JavaScript* (ES6) dan *jQuery v3.5.1* (opsional untuk animasi bawaan)
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
└── README.md             # Dokumentasi Proyek Ini
```

## 🚀 Panduan Peluncuran (*Deployment*)

Karena struktur proyek ini adalah **Situs Statis (Static HTML)**, proses menaikkan website ini ke Internet agar dapat diakses publik sangatlah mudah dan tidak memerlukan *cPanel* konvensional.

Kami sangat merekomendasikan menggunakan layanan seperti **Netlify**, **Vercel**, atau **GitHub Pages** (gratis dengan SSL/HTTPS terpasang otomatis).

### Cara *Hosting* via Netlify Drop:
1. Buka peramban (*browser*) dan kunjungi [Netlify Drop](https://drop.netlify.com/).
2. Buka aplikasi *File Explorer* (Windows/Mac) Anda.
3. Arahkan direktori Windows ke folder utama proyek ini: 
   `c:\laragon\www\porto-apps\50+client\panggonan_version\conc-panggonanv1 - Copy`
4. Lakukan **Drag & Drop** (Tarik & Pisahkan) keseluruhan isi folder utama tersebut tepat di atas antarmuka layar Netlify Drop tadi.
5. Tunggu proses kompilasi beberapa detik, lalu website siap meluncur (*Live*).
6. Masuk ke manu *Domain Management* di pengaturan akun Netlify untuk menyambungkan dengan domain asli (seperti `panggonan.id` atau `panggonanresto.com`).

---

## 🎨 Panduan Modifikasi & Pemeliharaan (Maintenance)

- **Mengubah Desain Warna Utama**: Tema primer (terutama *Gold* dan *Cream*) dapat dengan mudah diubah nilainya melalui inisialisasi *:root* pada file utama `assets/css/style.css` garis awal (baris `1 - 50`).
- **Nomor Telepon Reservasi**: Jika ada perubahan pada nomor layanan WhatsApp, Anda hanya perlu mengutak-atik fungsi JavaScript pada file `contact-us/index.html` (di baris `<script>` paling dasar dokumen halaman).
- **Penambahan Aset Baru**: Pastikan untuk selalu menggunakan format `.webp` atau mengkompresi `.jpeg / .png` terlebih dahulu sebelum meletakkannya di *folder* `assets/images/` untuk menjaga metrik kecepatan *loading* halaman (PageSpeed) tetap di angka > 90.

---

> *Dikembangkan dan direvitalisasi khusus oleh Panggonan Developer Team & Antigravity Assistant. Seluruh hak cipta desain konten dimiliki oleh Panggonan, 2024.*
