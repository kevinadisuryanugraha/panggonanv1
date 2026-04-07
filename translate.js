const fs = require('fs');
let html = fs.readFileSync('index.html', 'utf8');

// Title & Meta
html = html.replace(/<title>Conc - Webflow HTML website template<\/title>/g, '<title>Panggonan - Menghidupkan Kembali yang Telah Dilupakan</title>');
html = html.replace(/<meta\s+content="Explore our Construction Webflow template[^"]+"\s+name="description"\s*\/>/g, '<meta content="Panggonan - Restoran khas Jawa yang mengapresiasi dan menghidupkan kembali barang bekas bernilai sejarah tinggi melalui arsitektur memukau." name="description" />');
html = html.replace(/<meta content="Conc - Webflow HTML website template" property="og:title" \/>/g, '<meta content="Panggonan - Menghidupkan Kembali yang Telah Dilupakan" property="og:title" />');
html = html.replace(/<meta\s+content="Explore our Construction Webflow template[^"]+"\s+property="og:description"\s*\/>/g, '<meta content="Panggonan - Restoran khas Jawa yang mengapresiasi dan menghidupkan kembali barang bekas bernilai sejarah tinggi melalui arsitektur memukau." property="og:description" />');
html = html.replace(/<meta\s+content="Conc - Webflow HTML website template"\s+property="twitter:title"\s*\/>/g, '<meta content="Panggonan - Menghidupkan Kembali yang Telah Dilupakan" property="twitter:title" />');
html = html.replace(/<meta\s+content="Explore our Construction Webflow template[^"]+"\s+property="twitter:description"\s*\/>/g, '<meta content="Panggonan - Restoran khas Jawa yang mengapresiasi dan menghidupkan kembali barang bekas bernilai sejarah tinggi melalui arsitektur memukau." property="twitter:description" />');

// Navigation links
html = html.replace(/>Home</g, '>Beranda<');
html = html.replace(/>About Us</g, '>Tentang Kami<');
html = html.replace(/>Project</g, '>Galeri<');
html = html.replace(/>Pages</g, '>Halaman<');
html = html.replace(/>Service</g, '>Layanan<');
html = html.replace(/>Review</g, '>Ulasan<');
html = html.replace(/>FAQ</g, '>Tanya Jawab<');
html = html.replace(/>Blog</g, '>Jurnal<');
html = html.replace(/>Contact Us</g, '>Hubungi Kami<');

// Buttons / Text
html = html.replace(/>Play now</g, '>Putar Video<');
html = html.replace(/>View All</g, '>Lihat Semua<');
html = html.replace(/>View Details</g, '>Selengkapnya<');
html = html.replace(/>Contact Us</g, '>Hubungi Kami<');

// Sliders / Carousel roles
html = html.replace(/>Real Estate Developer</g, '>Pengunjung Setia<');
html = html.replace(/>Homeowner</g, '>Pecinta Seni<');
// Creative Worker already handled earlier? If not:
html = html.replace(/>Creative Worker</g, '>Pekerja Kreatif<');
html = html.replace(/>Urban Ventures</g, '>Penjelajah Rasa<');
html = html.replace(/>Property Manager</g, '>Keluarga<');
html = html.replace(/>Head of Facilities</g, '>Pelanggan Setia<');
html = html.replace(/>Project Lead</g, '>Arsitek Lokal<');

// FAQ section
html = html.replace(/>Frequently Asked Questions</g, '>Pertanyaan yang Sering Diajukan<');
html = html.replace(/>Still Have Questions\?</g, '>Masih Punya Pertanyaan?<');
html = html.replace(/Can’t find the answer you’re looking for\? Please contact with our customer service\./g, 'Tidak menemukan jawaban yang Anda cari? Silakan hubungi layanan pelanggan kami.');
html = html.replace(/>What types of construction services do you offer\?</g, '>Konsep unik apa yang diusung oleh Panggonan?<');
html = html.replace(/We provide a wide range of construction services, including residential and commercial building, renovations, interior design, project management, and more\./g, 'Kami mengusung konsep apresiasi benda lawas. Setiap sudut interior kami menggunakan material upcycle yang dirancang dengan nilai seni tinggi, memadukan tradisi Jawa dan arsitektur modern.');
html = html.replace(/>How do I start a construction project with your company\?</g, '>Apakah saya bisa berkontribusi untuk Clueyourun?<');
html = html.replace(/To begin, simply contact us through our website or call us directly. We'll schedule an initial consultation to discuss your project goals and needs\./g, 'Tentu saja! Kami sangat menyambut antusiasme Anda. Anda dapat berfoto di area kami dan menyertakan kutipan/clue, lalu membagikannya kepada staf kami untuk dipajang.');
html = html.replace(/>What is your typical project timeline\?</g, '>Apakah Panggonan cocok untuk acara keluarga atau rapat?<');
html = html.replace(/The timeline for each project varies based on its scope and complexity. After our initial consultation, we will provide you with a detailed timeline tailored to your project\./g, 'Sangat cocok. Panggonan menawarkan suasana yang tenang dan estetik, sehingga nyaman untuk kumpul keluarga, diskusi santai, maupun sekadar menikmati waktu sendiri.');
html = html.replace(/>How do you determine project costs\?</g, '>Buka jam berapa Panggonan setiap harinya?<');
html = html.replace(/Project costs are determined based on factors such as design complexity, materials, labor, and timelines. We provide detailed estimates after our consultation\./g, 'Kami buka setiap hari mulai pukul 10.00 pagi hingga 22.00 malam. Khusus akhir pekan, kami buka hingga pukul 23.00 malam.');
html = html.replace(/>Do you offer financing options\?</g, '>Apakah ada menu khas Jawa yang direkomendasikan?<');
html = html.replace(/Yes, we offer various financing options to help clients manage their construction costs. Our team can discuss these options during your consultation\./g, 'Kami menyediakan hidangan khas Jawa otentik yang disajikan dengan sentuhan modern, mencerminkan perpaduan masa lalu dan masa kini dalam satu sajian.');

// Footer section
html = html.replace(/>Don’t Missed Suncritpion</g, '>Tetap Terhubung dengan Kami<');
html = html.replace(/>Company</g, '>Perusahaan<');
html = html.replace(/>About</g, '>Tentang<');
html = html.replace(/>Services</g, '>Layanan<');
html = html.replace(/>Contact</g, '>Kontak<');
html = html.replace(/>Utility Pages</g, '>Halaman Utilitas<');
html = html.replace(/>Licenses</g, '>Lisensi<');
html = html.replace(/>Changelog</g, '>Catatan Perubahan<');
html = html.replace(/>Coming soon</g, '>Segera Hadir<');
html = html.replace(/>Protected</g, '>Dilindungi<');
html = html.replace(/>Resource</g, '>Sumber Daya<');
html = html.replace(/>Privacy Policy</g, '>Kebijakan Privasi<');
html = html.replace(/>Cookie Policy</g, '>Kebijakan Cookie<');
html = html.replace(/>Terms &amp; Conditions</g, '>Syarat &amp; Ketentuan<');
html = html.replace(/>Email Now</g, '>Kirim Email<');
html = html.replace(/>Phone</g, '>Telepon<');
html = html.replace(/>Follow Us</g, '>Ikuti Kami<');
html = html.replace(/© 2024 conc/g, '© 2024 Panggonan. Segala hak cipta dilindungi.');

// Subtags that might overlap
html = html.replace(/>Work by/g, '>Dibuat oleh<');
html = html.replace(/>Made with/g, '>Menggunakan<');
html = html.replace(/Project Details/g, 'Detail Galeri');
html = html.replace(/>Industry Trends</g, '>Gaya Hidup<');
html = html.replace(/>Sustainability</g, '>Keberlanjutan<');

// Missed reviews
html = html.replace(/The team at Conc transformed our vision into a beautiful, functional office space. Their meticulous attention to detail and commitment to deadlines was impressive\./g, 'Panggonan mengubah cara pandang saya terhadap benda-benda usang. Detail arsitektur di sini sangat mengesankan dan penuh dengan sentuhan nostalgia yang bermakna.');
html = html.replace(/“Conc exceeded our expectations in every way during our office renovation. They were highly attentive, maintained top quality, and delivered the project on time.”/g, '“Panggonan melampaui ekspektasi saya dalam segala hal. Pelayanannya sangat membumi, kualitas suasananya sempurna, dan makanannya sungguh luar biasa.”');
html = html.replace(/“From planning to execution, the team was dedicated, transparent, and responsive to every question we had. The results speak for themselves — a beautifully crafted space that our.”/g, '“Dari suasana hingga rasa, semuanya dikurasi dengan dedikasi tinggi. Hasilnya adalah sebuah tempat istimewa yang memanjakan hati dan perasaan kita.”');
html = html.replace(/“ Their expertise and commitment ensured that we completed our healthcare facility on schedule and within our budget, all while maintaining top-tier quality.”/g, '“Komitmen mereka untuk selalu menghargai karya seni kecil sangatlah unik. Menghabiskan waktu di Panggonan layaknya membaca sebuah antologi puisi rindu.”');
html = html.replace(/I’ve been in the industry for years, but the attention to detail and craftsmanship that Conc brings to the table is unmatched. Every step of the way\./g, 'Saya belum pernah menemukan restoran dengan karakter sekuat Panggonan. Setiap sudut bangunan menyuarakan kenangan indah masa lalu secara nyata.');
html = html.replace(/We worked with Conc on a large-scale office renovation, and their team exceeded every expectation. They were attentive to our needs, maintained exceptional quality."/g, 'Sebagai pecinta estentika, saya rasa Panggonan sangat luar biasa. Kombinasi arsitekturnya yang eksentrik benar-benar pantas diapresiasi setinggi-tingginya."');

// Other english texts spotted in HTML
html = html.replace(/>Choosing conc means partnering with a team that values quality, reliability, and innovation. We bring together decades of experience, skilled craftsmanship, and a commitment to your satisfaction.</g, '>Memilih Panggonan berarti Anda siap meresapi perpaduan nilai budaya Jawa dan estetika orisinil. Kami membagikan ratusan pengalaman otentik lewat setiap ornamen yang dipandang oleh mata Anda.<');

fs.writeFileSync('index.html', html, 'utf8');
console.log('Indonesian translation completed');
