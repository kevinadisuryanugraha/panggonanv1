<?php
session_start();

// Security check: Only authenticated admins can view print layout
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';

$type = $_GET['type'] ?? 'reservations';

// Fetch appropriate data
if ($type === 'journals') {
    $stmt = $pdo->query("SELECT * FROM journals ORDER BY id DESC");
    $data = $stmt->fetchAll();
    $title = "Laporan Jurnal & Cerita Pelanggan";
    $subtitle = "Daftar cerita puitis & visual pelanggan Panggonan Resto";
} else {
    $stmt = $pdo->query("SELECT * FROM reservations ORDER BY reservation_date DESC, id DESC");
    $data = $stmt->fetchAll();
    $type = 'reservations'; // fallback/safety
    $title = "Laporan Reservasi Pelanggan";
    $subtitle = "Data pemesanan meja & event untuk Cabang GDC Depok & Ciracas";
}

$printDate = date('d F Y, H:i');
$adminUser = $_SESSION['admin_username'] ?? 'Administrator';

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>Panggonan Resto — <?php echo htmlspecialchars($title ?? ''); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com" rel="preconnect" />
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous" />
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    :root {
      --primary-gold: #d4af37;
      --bg-dark: #121622;
      --text-main: #202430;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
      --font-sora: 'Sora', sans-serif;
      --font-inter: 'Inter', sans-serif;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background-color: #ffffff;
      color: var(--text-main);
      font-family: var(--font-inter);
      font-size: 9.5pt;
      line-height: 1.5;
      padding: 40px;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    /* REPORT HEADER (EXECUTIVE LAYOUT) */
    .report-header {
      border-bottom: 2px solid var(--primary-gold);
      padding-bottom: 24px;
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
    }

    .brand-logo-area h1 {
      font-family: var(--font-sora);
      font-size: 24pt;
      font-weight: 800;
      color: var(--bg-dark);
      letter-spacing: -0.03em;
      line-height: 1;
    }

    .brand-logo-area span {
      color: var(--primary-gold);
    }

    .brand-logo-area p {
      font-family: var(--font-sora);
      font-size: 9pt;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      margin-top: 4px;
      color: var(--text-muted);
    }

    .report-title-area {
      text-align: right;
    }

    .report-title-area h2 {
      font-family: var(--font-sora);
      font-size: 16pt;
      font-weight: 700;
      color: var(--bg-dark);
      margin-bottom: 4px;
    }

    .report-title-area p {
      font-size: 9.5pt;
      color: var(--text-muted);
    }

    /* METADATA RINGKASAN */
    .metadata-section {
      background-color: #f8fafc;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 16px 24px;
      margin-bottom: 35px;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 20px;
    }

    .meta-block {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    .meta-block .label {
      font-size: 8pt;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-muted);
    }

    .meta-block .value {
      font-weight: 600;
      color: var(--bg-dark);
    }

    /* TABLE LAYOUT */
    .report-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
      page-break-inside: auto;
    }

    .report-table tr {
      page-break-inside: avoid;
      page-break-after: auto;
    }

    .report-table th {
      background-color: var(--bg-dark);
      color: #ffffff;
      font-family: var(--font-sora);
      font-weight: 700;
      font-size: 8.5pt;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      padding: 12px 14px;
      border: 1px solid var(--bg-dark);
      text-align: left;
    }

    .report-table td {
      padding: 12px 14px;
      border: 1px solid var(--border-color);
      vertical-align: top;
      font-size: 9pt;
      line-height: 1.4;
    }

    .report-table tbody tr:nth-child(even) {
      background-color: #f8fafc;
    }

    .text-center {
      text-align: center !important;
    }

    .text-right {
      text-align: right !important;
    }

    .text-bold {
      font-weight: 600;
    }

    .pax-count {
      color: var(--bg-dark);
      font-family: var(--font-sora);
      font-weight: 700;
    }

    /* BADGES */
    .badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 8pt;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.02em;
      text-align: center;
      white-space: nowrap;
    }

    .badge-confirmed {
      background-color: #d1fae5;
      color: #065f46;
    }

    .badge-pending {
      background-color: #fef3c7;
      color: #92400e;
    }

    .badge-cancelled {
      background-color: #fee2e2;
      color: #991b1b;
    }

    .badge-general {
      background-color: #f1f5f9;
      color: #334155;
      border: 1px solid var(--border-color);
    }

    /* UTILITY FLOATING BUTTON (HIDDEN DURING PRINT) */
    .print-actions {
      position: fixed;
      bottom: 30px;
      right: 30px;
      display: flex;
      gap: 12px;
      z-index: 10000;
    }

    .btn-print {
      background-color: var(--bg-dark);
      color: #ffffff;
      border: none;
      border-radius: 50px;
      padding: 14px 28px;
      font-family: var(--font-sora);
      font-weight: 700;
      font-size: 10pt;
      cursor: pointer;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      transition: all 0.3s;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
    }

    .btn-print:hover {
      background-color: #1a2033;
      transform: translateY(-2px);
    }

    .btn-print.secondary {
      background-color: #ffffff;
      color: var(--bg-dark);
      border: 1px solid var(--border-color);
    }

    .btn-print.secondary:hover {
      background-color: #f8fafc;
    }

    /* PRINT SPECIFIC CSS ENGINE OVERRIDES */
    @media print {
      body {
        padding: 0;
        color: #000000;
      }
      .print-actions {
        display: none !important;
      }
      @page {
        size: A4 landscape;
        margin: 1.5cm;
      }
      /* Ensure colors print properly */
      .report-header {
        border-bottom-color: #d4af37 !important;
      }
      .report-table th {
        background-color: #121622 !important;
        color: #ffffff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .badge-confirmed {
        background-color: #d1fae5 !important;
        color: #065f46 !important;
      }
      .badge-pending {
        background-color: #fef3c7 !important;
        color: #92400e !important;
      }
      .badge-cancelled {
        background-color: #fee2e2 !important;
        color: #991b1b !important;
      }
    }
  </style>
</head>
<body>

  <!-- UTILITY ACTIONS (HIDDEN ON PRINT) -->
  <div class="print-actions">
    <button onclick="window.close()" class="btn-print secondary">
      <i class="fas fa-times"></i> Tutup Halaman
    </button>
    <button onclick="window.print()" class="btn-print">
      <i class="fas fa-print"></i> Cetak Laporan
    </button>
  </div>

  <!-- BRAND HEADER -->
  <header class="report-header">
    <div class="brand-logo-area">
      <h1>Panggonan<span>.</span></h1>
      <p>Editorial Resto &amp; Community Hub</p>
    </div>
    <div class="report-title-area">
      <h2><?php echo htmlspecialchars($title ?? ''); ?></h2>
      <p><?php echo htmlspecialchars($subtitle ?? ''); ?></p>
    </div>
  </header>

  <!-- REPORT METADATA SUMMARY -->
  <section class="metadata-section">
    <div class="meta-block">
      <span class="label">Dicetak Oleh</span>
      <span class="value"><?php echo htmlspecialchars($adminUser ?? ''); ?> (Tim Manajemen)</span>
    </div>
    <div class="meta-block">
      <span class="label">Tanggal Dokumen</span>
      <span class="value"><?php echo $printDate; ?> WIB</span>
    </div>
    <div class="meta-block text-center">
      <span class="label">Total Entri Data</span>
      <span class="value" style="font-family: var(--font-sora); font-size: 11pt; color: var(--primary-gold);"><?php echo count($data); ?> Baris</span>
    </div>
    <div class="meta-block text-right">
      <span class="label">Status Dokumen</span>
      <span class="value" style="color: #10b981;"><i class="fas fa-shield-alt"></i> Terverifikasi Sistem</span>
    </div>
  </section>

  <!-- DATA TABLE -->
  <table class="report-table">
    <thead>
      <?php if ($type === 'reservations'): ?>
        <tr>
          <th style="width: 5%;" class="text-center">No</th>
          <th style="width: 8%;" class="text-center">ID</th>
          <th style="width: 17%;">Nama Pelanggan</th>
          <th style="width: 13%;">WhatsApp</th>
          <th style="width: 10%;">Cabang</th>
          <th style="width: 12%;">Acara</th>
          <th style="width: 12%;">Tgl Acara</th>
          <th style="width: 5%;" class="text-center">Pax</th>
          <th style="width: 18%;">Catatan</th>
          <th style="width: 10%;" class="text-center">Status</th>
        </tr>
      <?php else: ?>
        <tr>
          <th style="width: 5%;" class="text-center">No</th>
          <th style="width: 8%;" class="text-center">ID</th>
          <th style="width: 18%;">Penulis</th>
          <th style="width: 15%;">Kategori / Tanggal</th>
          <th style="width: 18%;">Quotes</th>
          <th style="width: 25%;">Detail Cerita Lengkap</th>
          <th style="width: 8%;" class="text-center">Media</th>
          <th style="width: 10%;" class="text-center">Status</th>
        </tr>
      <?php endif; ?>
    </thead>
    <tbody>
      <?php 
      $no = 1;
      if (empty($data)): 
        $colSpan = ($type === 'reservations') ? 10 : 8;
      ?>
        <tr>
          <td colspan="<?php echo $colSpan; ?>" class="text-center" style="color: var(--text-muted); font-style: italic; padding: 30px;">
            Tidak ada data laporan yang tersedia saat ini.
          </td>
        </tr>
      <?php 
      else:
        foreach ($data as $row): 
      ?>
        <?php if ($type === 'reservations'): ?>
          <tr>
            <td class="text-center" style="color: var(--text-muted);"><?php echo $no++; ?></td>
            <td class="text-center text-bold">#<?php echo $row['id']; ?></td>
            <td class="text-bold"><?php echo htmlspecialchars($row['name'] ?? ''); ?></td>
            <td>
              <?php echo !empty($row['phone']) ? htmlspecialchars($row['phone'] ?? '') : '-'; ?>
            </td>
            <td>
              <span class="badge badge-general" style="padding: 2px 6px; font-size: 7.5pt; font-weight: 600;">
                <?php echo htmlspecialchars($row['branch'] ?? ''); ?>
              </span>
            </td>
            <td><?php echo htmlspecialchars($row['event_type'] ?? ''); ?></td>
            <td>
              <span class="text-bold"><?php echo date('d M Y', strtotime($row['reservation_date'])); ?></span>
              <?php if (!empty($row['reservation_time'])): ?>
                <br /><span style="font-size: 8pt; color: var(--text-muted);"><?php echo date('H:i', strtotime($row['reservation_time'])); ?> WIB</span>
              <?php endif; ?>
            </td>
            <td class="text-center pax-count"><?php echo (int)$row['pax']; ?></td>
            <td style="font-size: 8.5pt; color: #334155;"><?php echo htmlspecialchars($row['note'] ?? ''); ?></td>
            <td class="text-center" style="vertical-align: middle;">
              <?php if ($row['status'] === 'confirmed'): ?>
                <span class="badge badge-confirmed">Dikonfirmasi</span>
              <?php elseif ($row['status'] === 'cancelled'): ?>
                <span class="badge badge-cancelled">Batal</span>
              <?php else: ?>
                <span class="badge badge-pending">Menunggu</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php else: ?>
          <tr>
            <td class="text-center" style="color: var(--text-muted);"><?php echo $no++; ?></td>
            <td class="text-center text-bold">#<?php echo $row['id']; ?></td>
            <td class="text-bold"><?php echo htmlspecialchars($row['author'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($row['date_label'] ?? ''); ?></td>
            <td style="font-style: italic; font-size: 8.5pt; color: #334155;"><?php echo htmlspecialchars($row['quote'] ?? ''); ?></td>
            <td style="font-size: 8.5pt; color: #475569;"><?php echo strip_tags($row['text'] ?? ''); ?></td>
            <td class="text-center" style="vertical-align: middle;">
              <span class="badge badge-general"><?php echo htmlspecialchars($row['media_type'] ?? ''); ?></span>
            </td>
            <td class="text-center" style="vertical-align: middle;">
              <?php if ($row['status'] === 'approved'): ?>
                <span class="badge badge-confirmed">Aktif</span>
              <?php else: ?>
                <span class="badge badge-pending">Pending</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endif; ?>
      <?php 
        endforeach;
      endif; 
      ?>
    </tbody>
  </table>

  <!-- AUTO PRINT TRIGGER -->
  <script>
    window.onload = function() {
      // Auto open print dialog box
      setTimeout(function() {
        window.print();
      }, 500);
    }
  </script>

</body>
</html>
