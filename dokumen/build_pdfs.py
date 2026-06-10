import os
import sys

# Define color palette (Hansco Blue & Gold theme)
COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B = 26, 54, 93    # #1a365d (Deep Blue)
COLOR_GOLD_R, COLOR_GOLD_G, COLOR_GOLD_B = 197, 168, 128         # #c5a880 (Muted Gold)
COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B = 44, 44, 44             # #2c2c2c (Dark Charcoal)
COLOR_LIGHT_R, COLOR_LIGHT_G, COLOR_LIGHT_B = 249, 246, 240       # #f9f6f0 (Light Cream)

def build_invoice_pricelist_pdf():
    from fpdf import FPDF
    
    pdf = FPDF()
    pdf.set_auto_page_break(auto=True, margin=15)
    pdf.add_page()
    
    # Fonts
    pdf.set_font("Helvetica", "B", 20)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 10, "INVOICE & PRICELIST", ln=True, align="C")
    
    pdf.set_font("Helvetica", "B", 11)
    pdf.set_text_color(100, 100, 100)
    pdf.cell(0, 8, "Pengembangan Website Premium & Executive Dashboard Analytics", ln=True, align="C")
    pdf.cell(0, 6, "Panggonan Resto", ln=True, align="C")
    pdf.ln(10)
    
    # Metadata Table
    pdf.set_fill_color(COLOR_LIGHT_R, COLOR_LIGHT_G, COLOR_LIGHT_B)
    pdf.set_draw_color(220, 220, 220)
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    
    metadata = [
        ("Dokumen", "Lembar rincian harga profesional & realisasi infrastruktur"),
        ("No. Dokumen", "INV-PRC/PR-HSC/IV/2026"),
        ("Penyedia Jasa", "Kevin - Founder HANSCO"),
        ("Penerima Jasa", "Manajemen Panggonan Resto"),
        ("Status", "Final & Realised (Lunas & Aktif)")
    ]
    
    for label, val in metadata:
        pdf.set_font("Helvetica", "B", 9)
        pdf.cell(40, 6.5, f"  {label}", border=1, fill=True)
        pdf.set_font("Helvetica", "", 9)
        pdf.cell(150, 6.5, f"  {val}", border=1, ln=True)
    pdf.ln(5)
    
    # Section Title
    pdf.set_font("Helvetica", "B", 12)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 8, "Rincian Invoice & Pricelist (Biaya Riil Realisasi)", ln=True)
    pdf.ln(1)
    
    # Table Header
    pdf.set_fill_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.set_text_color(255, 255, 255)
    pdf.set_font("Helvetica", "B", 9)
    
    headers = [("No", 10), ("Uraian", 95), ("Kategori", 25), ("Qty", 10), ("Masa Aktif", 25), ("Nilai (Rp)", 25)]
    for title, width in headers:
        pdf.cell(width, 7.5, title, border=1, fill=True, align="C")
    pdf.ln()
    
    # Table Content
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    pdf.set_font("Helvetica", "", 8.5)
    
    items = [
        ("1a", "Perancangan UI/UX & Desain Sistem", "Jasa", "1", "Sekali", "1.000.000"),
        ("1b", "Pengembangan Frontend & Interaktivitas", "Jasa", "1", "Sekali", "1.250.000"),
        ("1c", "Pengembangan Backend & Sistem Reservasi", "Jasa", "1", "Sekali", "1.000.000"),
        ("1d", "Pengembangan Executive Dashboard & Keamanan", "Jasa", "1", "Sekali", "750.000"),
        ("2", "Web Hosting Premium Nimbus Go (DomaiNesia)", "Infrastruktur", "1", "2 Tahun", "768.000"),
        ("3", "Domain Resmi panggonanresto.com", "Infrastruktur", "1", "1 Tahun", "GRATIS"),
        ("4", "Addon keamanan SSL (Enkripsi SHA2)", "Infrastruktur", "1", "1 Tahun", "115.000"),
        ("5", "Biaya Administrasi Transaksi (DomaiNesia)", "Infrastruktur", "1", "-", "2.001"),
        ("6", "Pajak PPN 11% atas infrastruktur server", "Pajak", "1", "-", "97.130")
    ]
    
    for item in items:
        pdf.cell(10, 6.5, item[0], border=1, align="C")
        pdf.cell(95, 6.5, " " + item[1], border=1)
        pdf.cell(25, 6.5, item[2], border=1, align="C")
        pdf.cell(10, 6.5, item[3], border=1, align="C")
        pdf.cell(25, 6.5, item[4], border=1, align="C")
        pdf.cell(25, 6.5, item[5], border=1, align="R")
        pdf.ln()
        
    # Totals Row
    pdf.set_font("Helvetica", "B", 9)
    pdf.cell(165, 6.5, "Subtotal Jasa HANSCO", border=1, align="R")
    pdf.cell(25, 6.5, "4.000.000", border=1, align="R", ln=True)
    
    pdf.cell(165, 6.5, "Total Realisasi Pengadaan Infrastruktur Server (Lunas 2 Tahun)", border=1, align="R")
    pdf.cell(25, 6.5, "982.131", border=1, align="R", ln=True)
    
    pdf.set_fill_color(240, 240, 240)
    pdf.cell(165, 7.5, "TOTAL INVESTASI AWAL (Lunas)", border=1, align="R", fill=True)
    pdf.cell(25, 7.5, "4.982.131", border=1, align="R", fill=True, ln=True)
    pdf.ln(5)
    
    # Maintenance Section
    pdf.set_font("Helvetica", "B", 12)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 8, "Paket Maintenance Bulanan (Opsional)", ln=True)
    pdf.ln(1)
    
    pdf.set_fill_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.set_text_color(255, 255, 255)
    pdf.set_font("Helvetica", "B", 9)
    pdf.cell(40, 7.5, " Paket", border=1, fill=True)
    pdf.cell(115, 7.5, " Cakupan Layanan", border=1, fill=True)
    pdf.cell(35, 7.5, " Harga / Bulan", border=1, fill=True, align="C")
    pdf.ln()
    
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    pdf.set_font("Helvetica", "", 8.5)
    
    m_plans = [
        ("Paket Dasar", "3-5x revisi/update konten ringan.", "Rp 350.000"),
        ("Paket Standar Bisnis", "10-15x revisi/update, laporan analitik, monitoring.", "Rp 850.000"),
        ("Paket Premium", "15x revisi, 1x halaman baru, bantuan 24/7, analitik.", "Rp 1.500.000")
    ]
    
    for plan in m_plans:
        pdf.cell(40, 6.5, " " + plan[0], border=1)
        pdf.cell(115, 6.5, " " + plan[1], border=1)
        pdf.cell(35, 6.5, plan[2], border=1, align="C", ln=True)
    pdf.ln(5)
    
    # Signatures
    pdf.set_font("Helvetica", "B", 10)
    pdf.cell(95, 5.5, "PIHAK PERTAMA (HANSCO)", ln=False, align="C")
    pdf.cell(95, 5.5, "PIHAK KEDUA (PANGGONAN RESTO)", ln=True, align="C")
    pdf.ln(8)
    pdf.cell(95, 5.5, "( Kevin )", ln=False, align="C")
    pdf.cell(95, 5.5, "( Masyuri Kurniawan )", ln=True, align="C")
    
    # Output file
    dest = os.path.join("dokumen", "Invoice_Pricelist_Panggonan_Resto_HANSCO.pdf")
    pdf.output(dest)
    print(f"Successfully generated: {dest}")

def build_spk_pdf():
    from fpdf import FPDF
    
    pdf = FPDF()
    pdf.set_auto_page_break(auto=True, margin=15)
    
    # Page 1: Header
    pdf.add_page()
    pdf.set_font("Helvetica", "B", 18)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 10, "SURAT PERJANJIAN KERJA SAMA", ln=True, align="C")
    
    pdf.set_font("Helvetica", "B", 11)
    pdf.set_text_color(100, 100, 100)
    pdf.cell(0, 8, "Pengembangan Website Premium & Executive Dashboard Analytics", ln=True, align="C")
    pdf.cell(0, 6, "Panggonan Resto", ln=True, align="C")
    pdf.ln(10)
    
    # Metadata Table
    pdf.set_fill_color(COLOR_LIGHT_R, COLOR_LIGHT_G, COLOR_LIGHT_B)
    pdf.set_draw_color(220, 220, 220)
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    
    metadata = [
        ("Dokumen", "Surat Perjanjian Kerja Sama (SPK) / SPK-04/HSC-PR/V/2026"),
        ("Dasar Acuan", "Proposal Serah Terima & Kolaborasi Digital tertanggal 29 April 2026"),
        ("Penyedia Jasa (P1)", "Kevin - Founder HANSCO"),
        ("Penerima Jasa (P2)", "Manajemen / Owner Panggonan Resto"),
        ("Status Dokumen", "Realised & Final (Siap Ditandatangani)")
    ]
    
    for label, val in metadata:
        pdf.set_font("Helvetica", "B", 9)
        pdf.cell(40, 8, f"  {label}", border=1, fill=True)
        pdf.set_font("Helvetica", "", 9)
        pdf.cell(150, 8, f"  {val}", border=1, ln=True)
    pdf.ln(10)
    
    # Content Paragraphs
    pdf.set_font("Helvetica", "B", 11)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 8, "A. DASAR DAN MAKSUD PERJANJIAN", ln=True)
    pdf.ln(2)
    pdf.set_font("Helvetica", "", 9.5)
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    
    text1 = ("Pada hari ini, para pihak sepakat untuk mengikatkan diri dalam Surat Perjanjian Kerja Sama (SPK) "
             "Pengembangan Website Premium & Executive Dashboard Analytics bagi Panggonan Resto. Perjanjian ini "
             "disusun dengan mengacu pada proposal serah terima dan realisasi pengadaan infrastruktur server hosting "
             "yang telah diselesaikan secara matang.")
    pdf.multi_cell(0, 6, text1)
    pdf.ln(6)
    
    pdf.set_font("Helvetica", "B", 11)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 8, "B. PARA PIHAK", ln=True)
    pdf.ln(2)
    
    pdf.set_font("Helvetica", "", 9.5)
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    
    p1 = "PIHAK PERTAMA: Kevin, selaku Founder HANSCO, bertindak untuk dan atas nama HANSCO sebagai penyedia jasa pengembangan website dan sistem digital."
    pdf.multi_cell(0, 6, p1)
    pdf.ln(4)
    
    p2 = "PIHAK KEDUA: Manajemen / Penanggung Jawab Panggonan Resto, bertindak untuk dan atas nama Panggonan Resto sebagai penerima hasil pekerjaan."
    pdf.multi_cell(0, 6, p2)
    pdf.ln(10)
    
    # Add page 2 of SPK
    pdf.add_page()
    pdf.set_font("Helvetica", "B", 11)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 8, "C. RUANG LINGKUP PEKERJAAN", ln=True)
    pdf.ln(2)
    
    pdf.set_font("Helvetica", "", 9)
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    
    scopes = [
        ("1. Beranda (Home)", "Halaman utama dengan visual premium, masonry gallery, dan CTA reservasi."),
        ("2. Tentang Kami", "Narasi filosofi ruang, sejarah Joglo, dan penjelmaan konsep upcycle."),
        ("3. Katalog Menu", "Pameran hidangan Jawa autentik terintegrasi dinamis dengan database."),
        ("4. Galeri Foto", "Masonry gallery dinamis berbasis database relasional terikat kategori."),
        ("5. Jurnal & Cerita", "Cerita puitis pengunjung terintegrasi CRUD approval backend."),
        ("6. Pusat FAQ", "Pertanyaan umum seputar layanan restoran untuk memangkas waktu CS."),
        ("7. Sistem Kontak", "Integrasi Maps lokasi dan pemesanan WhatsApp dinamis per cabang."),
        ("8. Executive Dashboard", "Dasbor analitik privat pelacak lalu lintas, reserva, menu, dan galeri.")
    ]
    
    for scope_title, scope_desc in scopes:
        pdf.set_font("Helvetica", "B", 9)
        pdf.cell(45, 6, "  " + scope_title)
        pdf.set_font("Helvetica", "", 9)
        pdf.cell(145, 6, ": " + scope_desc, ln=True)
    pdf.ln(8)
    
    # Costs
    pdf.set_font("Helvetica", "B", 11)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 8, "D. NILAI PEKERJAAN & BIAYA INFRASTRUKTUR", ln=True)
    pdf.ln(2)
    
    pdf.set_font("Helvetica", "", 9.5)
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    cost_text = ("1. Nilai Jasa Pengembangan Utama disepakati sebesar Rp 4.000.000 (empat juta rupiah) sekali bayar dengan rincian peruntukan:\n"
                 "   - Perancangan UI/UX & Desain Sistem: Rp 1.000.000 (estetika, responsivitas, arsitektur data)\n"
                 "   - Pengembangan Frontend & Interaktivitas: Rp 1.250.000 (antarmuka publik, galeri masonry, form)\n"
                 "   - Pengembangan Backend & Database Terpadu: Rp 1.000.000 (Vanilla PHP, PDO MySQL, WhatsApp chat)\n"
                 "   - Pengembangan Executive Dashboard & Keamanan: Rp 750.000 (CRUD admin panel, BCRYPT, unlink file)\n"
                 "2. Garansi pemeliharaan sistem gratis diberikan selama 1 bulan.\n"
                 "3. Pengadaan biaya server hosting premium DomaiNesia dibayarkan secara terpisah langsung ke Pihak Ketiga.")
    pdf.multi_cell(0, 6, cost_text)
    pdf.ln(6)
    
    # Realised Table (Nimbus Go details)
    pdf.set_font("Helvetica", "B", 10)
    pdf.cell(0, 6, "LAMPIRAN RINGKAS - REALISED BIAYA INFRASTRUKTUR RIIL (LUNAS)", ln=True)
    pdf.ln(2)
    
    pdf.set_fill_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.set_text_color(255, 255, 255)
    pdf.cell(115, 8, " Komponen Server", border=1, fill=True)
    pdf.cell(35, 8, " Masa Aktif", border=1, fill=True, align="C")
    pdf.cell(40, 8, " Nilai Biaya (Rp)", border=1, fill=True, align="C")
    pdf.ln()
    
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    pdf.set_font("Helvetica", "", 9)
    
    infra = [
        ("Web Hosting Premium Nimbus Go (DomaiNesia)", "2 Tahun (2026-2028)", "768.000"),
        ("Domain Resmi panggonanresto.com", "1 Tahun", "GRATIS"),
        ("Addon Sertifikat Keamanan SSL", "1 Tahun", "115.000"),
        ("Biaya Administrasi Transaksi", "-", "2.001"),
        ("Pajak PPN 11% (Infrastruktur Server)", "-", "97.130"),
        ("Total Pengadaan Server Terpasang (Lunas)", "2 Tahun", "982.131")
    ]
    
    for row in infra:
        is_bold = "Total" in row[0]
        pdf.set_font("Helvetica", "B" if is_bold else "", 9)
        pdf.cell(115, 8, " " + row[0], border=1)
        pdf.cell(35, 8, row[1], border=1, align="C")
        pdf.cell(40, 8, row[2], border=1, align="R", ln=True)
    pdf.ln(10)
    
    # Page 3 of SPK: F, G and Signatures
    pdf.add_page()
    pdf.set_font("Helvetica", "B", 11)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 8, "F. BATASAN TANGGUNG JAWAB & GARANSI TEKNIS", ln=True)
    pdf.ln(2)
    
    pdf.set_font("Helvetica", "", 9)
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    limitations = ("1. Batasan Pekerjaan (Scope Limit): Pekerjaan HANSCO terbatas pada 8 (delapan) ruang lingkup yang tercantum pada Bagian C. Setiap permintaan fitur baru, perubahan desain besar, atau modifikasi di luar daftar tersebut setelah kesepakatan ditandatangani akan dikenakan biaya tambahan (charge) yang dihitung secara proporsional.\n"
                   "2. Gugurnya Garansi: Garansi perbaikan bug/error gratis selama 1 (satu) bulan otomatis dinyatakan tidak berlaku (gugur) apabila PIHAK KEDUA atau pihak ketiga lain di luar persetujuan tertulis HANSCO melakukan perubahan, modifikasi, atau penyuntingan pada source code atau database sistem.\n"
                   "3. Layanan Pihak Ketiga & Force Majeure: HANSCO dibebaskan dari tuntutan ganti rugi jika terjadi gangguan layanan, pemadaman server (hosting downtime), kegagalan sistem pembayaran, atau pemblokiran domain/WhatsApp yang diakibatkan oleh penyedia pihak ketiga (DomaiNesia, WhatsApp Meta API, Google Maps) atau kejadian di luar kendali manusia (force majeure).\n"
                   "4. Penerimaan Otomatis (Deemed Acceptance): Apabila PIHAK KEDUA tidak memberikan umpan balik tertulis atau keluhan dalam waktu 7 (tujuh) hari kalender setelah penyerahan demo sistem akhir, maka pekerjaan dianggap telah diterima dengan sangat baik secara hukum. PIHAK KEDUA wajib menandatangani BAST dan melunasi pembayaran.\n"
                   "5. Batasan Waktu Dukungan: Setelah masa garansi 1 (satu) bulan berakhir, segala bentuk perbaikan bug atau pemeliharaan sistem akan dikenakan biaya jasa profesional ad-hoc tambahan, kecuali PIHAK KEDUA terikat kontrak layanan Monthly Maintenance.")
    pdf.multi_cell(0, 6, limitations)
    pdf.ln(6)
    
    pdf.set_font("Helvetica", "B", 11)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 8, "G. STATUS SERAH TERIMA & KEPEMILIKAN", ln=True)
    pdf.ln(2)
    
    pdf.set_font("Helvetica", "", 9)
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    handover = ("* PIHAK PERTAMA menyatakan bahwa seluruh infrastruktur website Panggonan Resto telah selesai dikembangkan dan siap diserahterimakan seiring penyelesaian kewajiban administratif.\n"
                "* Setelah kewajiban administratif terpenuhi, source code, aset digital, akses kredensial dasbor, dan kepemilikan mutlak kode menjadi hak milik penuh PIHAK KEDUA.")
    pdf.multi_cell(0, 6, handover)
    pdf.ln(12)
    
    # Signatures
    pdf.set_font("Helvetica", "B", 10)
    pdf.cell(95, 6, "PIHAK PERTAMA (HANSCO)", ln=False, align="C")
    pdf.cell(95, 6, "PIHAK KEDUA (PANGGONAN RESTO)", ln=True, align="C")
    pdf.ln(15)
    pdf.cell(95, 6, "( Kevin )", ln=False, align="C")
    pdf.cell(95, 6, "( Masyuri Kurniawan )", ln=True, align="C")
    
    # Output file
    dest = os.path.join("dokumen", "spk_panggonan_resto_profesional.pdf")
    pdf.output(dest)
    print(f"Successfully generated: {dest}")

def build_bast_pdf():
    from fpdf import FPDF
    
    pdf = FPDF()
    pdf.set_auto_page_break(auto=True, margin=15)
    pdf.add_page()
    
    pdf.set_font("Helvetica", "B", 18)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 10, "BERITA ACARA SERAH TERIMA (BAST)", ln=True, align="C")
    
    pdf.set_font("Helvetica", "B", 11)
    pdf.set_text_color(100, 100, 100)
    pdf.cell(0, 8, "Penyelesaian Pengembangan Website Premium & Executive Dashboard Analytics", ln=True, align="C")
    pdf.cell(0, 6, "Panggonan Resto", ln=True, align="C")
    pdf.ln(10)
    
    # Metadata Table
    pdf.set_fill_color(COLOR_LIGHT_R, COLOR_LIGHT_G, COLOR_LIGHT_B)
    pdf.set_draw_color(220, 220, 220)
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    
    metadata = [
        ("Dokumen", "Berita Acara Serah Terima (BAST) Pekerjaan"),
        ("Dasar Acuan", "1) Proposal Serah Terima & SPK Kerja Sama"),
        ("Penyedia Jasa", "Kevin - Founder HANSCO"),
        ("Penerima Jasa", "Manajemen / Owner Panggonan Resto"),
        ("Status Pekerjaan", "SELESAI 100% (Sempurna & Siap Pakai)"),
        ("Ruang Lingkup", "Serah terima source code, database, & akses dasbor admin.")
    ]
    
    for label, val in metadata:
        pdf.set_font("Helvetica", "B", 9)
        pdf.cell(40, 8, f"  {label}", border=1, fill=True)
        pdf.set_font("Helvetica", "", 9)
        pdf.cell(150, 8, f"  {val}", border=1, ln=True)
    pdf.ln(10)
    
    # Paragraphs
    pdf.set_font("Helvetica", "", 9.5)
    p_text = ("Pada hari ini, para pihak menerangkan bahwa seluruh proses pengembangan website premium dan "
              "executive dashboard analytics untuk Panggonan Resto telah diselesaikan dengan sangat baik oleh PIHAK PERTAMA "
              "dan telah diuji, divalidasi, serta diserahterimakan kepada PIHAK KEDUA untuk operasional bisnis, "
              "pemesanan reservasi online, dan wawasan pengunjung secara terpadu.")
    pdf.multi_cell(0, 6, p_text)
    pdf.ln(6)
    
    # Terms
    pdf.set_font("Helvetica", "B", 11)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 8, "PERNYATAAN SERAH TERIMA & GARANSI", ln=True)
    pdf.ln(2)
    
    pdf.set_font("Helvetica", "", 9.5)
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    terms = ("1. PIHAK PERTAMA menyerahkan seluruh source code, database MySQL terpusat, sertifikat SSL, domain, dan akses admin.\n"
             "2. PIHAK PERTAMA memberikan jaminan bebas bug/error (bug fixing) gratis selama 1 (satu) bulan pasca-serah terima.\n"
             "3. PIHAK KEDUA menyatakan menerima seluruh deliverables dalam kondisi baik dan layak digunakan.\n"
             "4. Realisasi pengadaan server lunas Rp 982.131 (Nimbus Go 2 Tahun) telah aktif terintegrasi dengan mulus.")
    pdf.multi_cell(0, 6, terms)
    pdf.ln(12)
    
    # Signatures
    pdf.set_font("Helvetica", "B", 10)
    pdf.cell(95, 6, "PIHAK PERTAMA (HANSCO)", ln=False, align="C")
    pdf.cell(95, 6, "PIHAK KEDUA (PANGGONAN RESTO)", ln=True, align="C")
    pdf.ln(15)
    pdf.cell(95, 6, "( Kevin )", ln=False, align="C")
    pdf.cell(95, 6, "( Masyuri Kurniawan )", ln=True, align="C")
    
    # Output file
    dest = os.path.join("dokumen", "BAST_Panggonan_Resto_HANSCO.pdf")
    pdf.output(dest)
    print(f"Successfully generated: {dest}")

def build_combined_agreement_pdf():
    from fpdf import FPDF
    
    pdf = FPDF()
    pdf.set_auto_page_break(auto=True, margin=15)
    pdf.add_page()
    
    # Front Page
    pdf.set_font("Helvetica", "B", 20)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 10, "BUNDEL DOKUMEN KERJA SAMA DIGITAL", ln=True, align="C")
    pdf.cell(0, 10, "& BERITA ACARA SERAH TERIMA", ln=True, align="C")
    pdf.ln(5)
    
    pdf.set_font("Helvetica", "B", 11)
    pdf.set_text_color(100, 100, 100)
    pdf.cell(0, 8, "Pengembangan Website Premium & Executive Dashboard Analytics", ln=True, align="C")
    pdf.cell(0, 6, "Panggonan Resto", ln=True, align="C")
    pdf.ln(15)
    
    pdf.set_font("Helvetica", "B", 14)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 10, "A N T A R A", ln=True, align="C")
    pdf.ln(5)
    
    # Sides
    pdf.set_fill_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.set_text_color(255, 255, 255)
    pdf.set_font("Helvetica", "B", 12)
    pdf.cell(92, 12, "HANSCO", border=1, fill=True, align="C")
    pdf.cell(6, 12, "", ln=False)
    pdf.cell(92, 12, "PANGGONAN RESTO", border=1, fill=True, align="C", ln=True)
    
    pdf.set_fill_color(COLOR_LIGHT_R, COLOR_LIGHT_G, COLOR_LIGHT_B)
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    pdf.set_font("Helvetica", "", 10)
    pdf.cell(92, 10, "(Penyedia Jasa)", border=1, fill=True, align="C")
    pdf.cell(6, 10, "", ln=False)
    pdf.cell(92, 10, "(Penerima Jasa)", border=1, fill=True, align="C", ln=True)
    pdf.ln(15)
    
    # Metadata Title
    pdf.set_font("Helvetica", "B", 12)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 8, "RINGKASAN IDENTITAS DOKUMEN BUNDEL", ln=True)
    pdf.ln(2)
    
    metadata = [
        ("Nama Dokumen", "Paket Kesepakatan Kerja Sama & Serah Terima Aset Digital"),
        ("Objek Kerja Sama", "Pengembangan Website Premium & Executive Dashboard Analytics"),
        ("Pihak Pertama (P1)", "Kevin - Founder HANSCO"),
        ("Pihak Kedua (P2)", "Manajemen / Penanggung Jawab Panggonan Resto"),
        ("Status Dokumen", "Final & Realised (Lunas & Aktif)"),
        ("Dasar Pelaksanaan", "Proposal Serah Terima & Kolaborasi Digital tertanggal 29 April 2026")
    ]
    
    pdf.set_draw_color(220, 220, 220)
    for label, val in metadata:
        pdf.set_font("Helvetica", "B", 9)
        pdf.cell(40, 8, f"  {label}", border=1, fill=True)
        pdf.set_font("Helvetica", "", 9)
        pdf.cell(150, 8, f"  {val}", border=1, ln=True)
    
    # Page 2: Bagian I & II
    pdf.add_page()
    pdf.set_font("Helvetica", "B", 12)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 7, "BAGIAN I - RINGKASAN EKSEKUTIF PROYEK", ln=True)
    pdf.ln(1)
    
    pdf.set_font("Helvetica", "", 9)
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    t1 = ("Proyek ini bertujuan untuk membangun Ekosistem Digital Independen yang premium bagi Panggonan Resto. "
          "Infrastruktur yang dikembangkan dirancang khusus untuk meningkatkan konversi reservasi online, memperkuat "
          "branding kultural Jawa klasik, dan memberikan wawasan analitik secara real-time kepada manajemen.\n\n"
          "Pendekatan teknologi menggunakan pemrograman modular PHP berbasis database relasional terpusat, "
          "dipadukan dengan desain Dark-Gold Glassmorphism yang sangat elegan dan responsif di segala perangkat.")
    pdf.multi_cell(0, 5, t1)
    pdf.ln(4)
    
    pdf.set_font("Helvetica", "B", 12)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 7, "BAGIAN II - SURAT PERJANJIAN KERJA SAMA (SPK)", ln=True)
    pdf.ln(1)
    
    pdf.set_font("Helvetica", "", 9)
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    t2 = ("Para pihak sepakat untuk mengikatkan diri dalam Surat Perjanjian Kerja Sama (SPK) dengan rincian:\n"
          "1. Ruang Lingkup: Website Premium Panggonan Resto dan Admin Dashboard Analytics Terpadu.\n"
          "2. Nilai Jasa Pengembangan Utama disepakati lunas sebesar Rp 4.000.000 sekali bayar.\n"
          "3. Pengecualian Biaya: Biaya domain, hosting, dan SSL berada di luar nilai jasa pengembangan.")
    pdf.multi_cell(0, 5, t2)
    pdf.ln(4)
    
    pdf.set_font("Helvetica", "B", 12)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 7, "BAGIAN III - BERITA ACARA SERAH TERIMA (BAST)", ln=True)
    pdf.ln(1)
    
    pdf.set_font("Helvetica", "", 9)
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    t3 = ("Berdasarkan evaluasi bersama, para pihak menyatakan:\n"
          "1. Seluruh pekerjaan pengembangan website dan dasbor relasional telah selesai 100% sempurna.\n"
          "2. Seluruh source code, database terpusat, dan akses admin diserahterimakan sepenuhnya kepada Pihak Kedua.\n"
          "3. Masa aktif pengadaan server lunas Rp 982.131 (Nimbus Go 2 Tahun) telah aktif berjalan dengan mulus.")
    pdf.multi_cell(0, 5, t3)
    pdf.ln(4)
    
    # Bagian IV, V & VI (Consecutive flow on Page 2)
    pdf.set_font("Helvetica", "B", 12)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 7, "BAGIAN IV - RINCIAN BIAYA & REALISED BIAYA INFRASTRUKTUR", ln=True)
    pdf.ln(1)
    
    pdf.set_fill_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.set_text_color(255, 255, 255)
    pdf.set_font("Helvetica", "B", 9)
    pdf.cell(115, 6.5, " Komponen", border=1, fill=True)
    pdf.cell(35, 6.5, " Masa Aktif", border=1, fill=True, align="C")
    pdf.cell(40, 6.5, " Nilai Riil (Rp)", border=1, fill=True, align="C")
    pdf.ln()
    
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    pdf.set_font("Helvetica", "", 9)
    
    infra = [
        ("Perancangan UI/UX & Desain Sistem HANSCO", "Sekali Bayar", "1.000.000"),
        ("Pengembangan Frontend & Interaktivitas HANSCO", "Sekali Bayar", "1.250.000"),
        ("Pengembangan Backend & Database Terpadu HANSCO", "Sekali Bayar", "1.000.000"),
        ("Pengembangan Executive Dashboard & Keamanan HANSCO", "Sekali Bayar", "750.000"),
        ("Web Hosting Premium Nimbus Go (DomaiNesia)", "2 Tahun (2026-2028)", "768.000"),
        ("Domain Resmi panggonanresto.com", "1 Tahun", "GRATIS"),
        ("Addon Sertifikat Keamanan SSL", "1 Tahun", "115.000"),
        ("Biaya Administrasi Transaksi", "-", "2.001"),
        ("Pajak PPN 11% (Infrastruktur Server)", "-", "97.130"),
        ("TOTAL INVESTASI AWAL REALISED (Jasa + Server)", "2 Tahun", "4.982.131")
    ]
    
    for row in infra:
        is_bold = "TOTAL" in row[0]
        pdf.set_font("Helvetica", "B" if is_bold else "", 8.5)
        if is_bold:
            pdf.set_fill_color(240, 240, 240)
        pdf.cell(115, 5.5, " " + row[0], border=1, fill=is_bold)
        pdf.cell(35, 5.5, row[1], border=1, align="C", fill=is_bold)
        pdf.cell(40, 5.5, row[2], border=1, align="R", fill=is_bold, ln=True)
    pdf.ln(4)
    
    pdf.set_font("Helvetica", "B", 11)
    pdf.set_text_color(COLOR_PRIMARY_R, COLOR_PRIMARY_G, COLOR_PRIMARY_B)
    pdf.cell(0, 7, "BAGIAN V - GARANSI, MAINTENANCE & BATASAN TANGGUNG JAWAB", ln=True)
    pdf.ln(1)
    pdf.set_font("Helvetica", "", 8.5)
    pdf.set_text_color(COLOR_TEXT_R, COLOR_TEXT_G, COLOR_TEXT_B)
    t5 = ("1. Garansi Teknis & Pembatalan: Garansi bebas bug gratis selama 1 bulan otomatis gugur jika PIHAK KEDUA atau pihak ketiga lain melakukan modifikasi mandiri pada berkas kode sumber (source code) tanpa izin tertulis HANSCO.\n"
          "2. Batasan Ruang Lingkup: Pekerjaan dibatasi oleh 8 ruang lingkup yang disepakati. Penambahan fitur atau halaman tambahan baru di luar kesepakatan awal akan dikenakan biaya tambahan proporsional.\n"
          "3. Batasan Pihak Ketiga & Force Majeure: HANSCO bebas dari tuntutan ganti rugi atas kegagalan teknis, pemadaman server (hosting downtime), pemblokiran API (WhatsApp, Google Maps), atau force majeure dari penyedia pihak ketiga (DomaiNesia).")
    pdf.multi_cell(0, 4.5, t5)
    pdf.ln(6)
    
    # Signatures
    pdf.set_font("Helvetica", "B", 10)
    pdf.cell(95, 5, "PIHAK PERTAMA (HANSCO)", ln=False, align="C")
    pdf.cell(95, 5, "PIHAK KEDUA (PANGGONAN RESTO)", ln=True, align="C")
    pdf.ln(8)
    pdf.cell(95, 5, "( Kevin )", ln=False, align="C")
    pdf.cell(95, 5, "( Masyuri Kurniawan )", ln=True, align="C")
    
    # Output file
    dest = os.path.join("dokumen", "Dokumen_Kerja_Sama_BAST_Panggonan_Resto_HANSCO.pdf")
    pdf.output(dest)
    print(f"Successfully generated: {dest}")

if __name__ == "__main__":
    try:
        build_invoice_pricelist_pdf()
        build_spk_pdf()
        build_bast_pdf()
        build_combined_agreement_pdf()
        print("\nAll 4 PDFs generated beautifully!")
    except Exception as e:
        print(f"\nPDF GENERATION ERROR: {e}")
        sys.exit(1)
