<?php
session_start();

// Security check: Only authenticated admins can export data
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';

$type = $_GET['type'] ?? 'reservations';

// Set proper filenames and headers for Excel download
$timestamp = date('Ymd_His');
$filename = "Laporan_Panggonan_" . ucfirst($type) . "_" . $timestamp . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);

// Fetch the appropriate data
if ($type === 'journals') {
    $stmt = $pdo->query("SELECT * FROM journals ORDER BY id DESC");
    $data = $stmt->fetchAll();
    $title = "LAPORAN DATA JURNAL & CERITA PELANGGAN";
} else {
    $stmt = $pdo->query("SELECT * FROM reservations ORDER BY reservation_date DESC, id DESC");
    $data = $stmt->fetchAll();
    $type = 'reservations'; // fallback/safety
    $title = "LAPORAN DATA RESERVASI PELANGGAN";
}

$printDate = date('d M Y H:i:s');
$adminUser = $_SESSION['admin_username'] ?? 'Administrator';

?>
<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
  <meta charset="utf-8" />
  <!--[if gte mso 9]>
  <xml>
    <x:ExcelWorkbook>
      <x:ExcelWorksheets>
        <x:ExcelWorksheet>
          <x:Name><?php echo ucfirst($type); ?></x:Name>
          <x:WorksheetOptions>
            <x:DisplayGridlines/>
          </x:WorksheetOptions>
        </x:ExcelWorksheet>
      </x:ExcelWorksheets>
    </x:ExcelWorkbook>
  </xml>
  <![endif]-->
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      color: #333333;
    }
    .title-area {
      margin-bottom: 20px;
    }
    .report-title {
      font-size: 16pt;
      font-weight: bold;
      color: #121622;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
    }
    .meta-table {
      margin-bottom: 25px;
      font-size: 9pt;
      color: #555555;
    }
    .meta-label {
      font-weight: bold;
      width: 120px;
    }
    .data-table {
      border-collapse: collapse;
      width: 100%;
    }
    .data-table th {
      background-color: #121622;
      color: #ffffff;
      font-weight: bold;
      font-size: 10pt;
      border: 1px solid #1a2035;
      padding: 12px 10px;
      text-align: left;
    }
    .data-table td {
      border: 1px solid #e2e8f0;
      padding: 10px;
      font-size: 9.5pt;
      vertical-align: middle;
    }
    /* Alternate row styling for neat spacing */
    .data-table tr:nth-child(even) td {
      background-color: #f8fafc;
    }
    /* Dynamic Status Badge Colors inside Excel */
    .badge-confirmed {
      background-color: #d1fae5;
      color: #065f46;
      font-weight: bold;
      padding: 4px 8px;
      border-radius: 4px;
      text-align: center;
      border: 1px solid #a7f3d0;
    }
    .badge-pending {
      background-color: #fef3c7;
      color: #92400e;
      font-weight: bold;
      padding: 4px 8px;
      border-radius: 4px;
      text-align: center;
      border: 1px solid #fde68a;
    }
    .badge-cancelled {
      background-color: #fee2e2;
      color: #991b1b;
      font-weight: bold;
      padding: 4px 8px;
      border-radius: 4px;
      text-align: center;
      border: 1px solid #fca5a5;
    }
    .badge-general {
      background-color: #e2e8f0;
      color: #334155;
      padding: 4px 8px;
      border-radius: 4px;
      text-align: center;
      border: 1px solid #cbd5e1;
    }
    .text-center {
      text-align: center;
    }
    .text-bold {
      font-weight: bold;
    }
    .accent-gold {
      color: #d4af37;
    }
  </style>
</head>
<body>

  <!-- REPORT TITLE & METADATA -->
  <div class="title-area">
    <table border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="4" class="report-title"><?php echo $title; ?></td>
      </tr>
      <tr>
        <td colspan="4" style="font-size: 11pt; color: #d4af37; font-weight: bold; padding-bottom: 10px;">PANGGONAN RESTO — GDC DEPOK &amp; CIRACAS</td>
      </tr>
    </table>
    
    <table border="0" cellpadding="0" cellspacing="0" class="meta-table">
      <tr>
        <td class="meta-label">Tanggal Cetak</td>
        <td>: <?php echo $printDate; ?> WIB</td>
      </tr>
      <tr>
        <td class="meta-label">Dicetak Oleh</td>
        <td>: <?php echo htmlspecialchars($adminUser ?? ''); ?> (Tim Manajemen)</td>
      </tr>
      <tr>
        <td class="meta-label">Total Entri</td>
        <td>: <?php echo count($data); ?> baris data</td>
      </tr>
    </table>
  </div>

  <!-- MAIN DATA TABLE -->
  <table class="data-table" border="1" cellpadding="5" cellspacing="0">
    <thead>
      <?php if ($type === 'reservations'): ?>
        <tr>
          <th style="width: 50px; text-align: center;">No</th>
          <th style="width: 60px; text-align: center;">ID</th>
          <th style="width: 180px;">Nama Pelanggan</th>
          <th style="width: 150px;">Nomor WhatsApp</th>
          <th style="width: 120px;">Cabang</th>
          <th style="width: 140px;">Jenis Acara</th>
          <th style="width: 130px; text-align: center;">Tanggal Acara</th>
          <th style="width: 90px; text-align: center;">Waktu</th>
          <th style="width: 70px; text-align: center;">Pax</th>
          <th style="width: 250px;">Catatan</th>
          <th style="width: 120px; text-align: center;">Status</th>
          <th style="width: 160px; text-align: center;">Tanggal Pengajuan</th>
        </tr>
      <?php else: ?>
        <tr>
          <th style="width: 50px; text-align: center;">No</th>
          <th style="width: 60px; text-align: center;">ID</th>
          <th style="width: 200px;">Penulis</th>
          <th style="width: 180px;">Kategori / Label Tanggal</th>
          <th style="width: 250px;">Kutipan (Quotes)</th>
          <th style="width: 450px;">Deskripsi Cerita Lengkap</th>
          <th style="width: 110px; text-align: center;">Tipe Media</th>
          <th style="width: 250px;">Media URL</th>
          <th style="width: 110px; text-align: center;">Status</th>
          <th style="width: 160px; text-align: center;">Tanggal Dibuat</th>
        </tr>
      <?php endif; ?>
    </thead>
    <tbody>
      <?php 
      $no = 1;
      if (empty($data)): 
        $colSpan = ($type === 'reservations') ? 12 : 10;
      ?>
        <tr>
          <td colspan="<?php echo $colSpan; ?>" style="text-align: center; color: #888888; font-style: italic; padding: 20px;">
            Tidak ada data yang tersedia untuk dicetak.
          </td>
        </tr>
      <?php 
      else:
        foreach ($data as $row): 
      ?>
        <?php if ($type === 'reservations'): ?>
          <tr>
            <td style="text-align: center;"><?php echo $no++; ?></td>
            <td style="text-align: center;" class="text-bold">#<?php echo $row['id']; ?></td>
            <td class="text-bold"><?php echo htmlspecialchars($row['name'] ?? ''); ?></td>
            <td>
              <?php echo !empty($row['phone']) ? htmlspecialchars($row['phone'] ?? '') : '-'; ?>
            </td>
            <td><?php echo htmlspecialchars($row['branch'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($row['event_type'] ?? ''); ?></td>
            <td style="text-align: center;">
              <?php echo date('d M Y', strtotime($row['reservation_date'])); ?>
            </td>
            <td style="text-align: center;">
              <?php echo !empty($row['reservation_time']) ? date('H:i', strtotime($row['reservation_time'])) . ' WIB' : '-'; ?>
            </td>
            <td style="text-align: center;" class="text-bold accent-gold"><?php echo (int)$row['pax']; ?></td>
            <td><?php echo htmlspecialchars($row['note'] ?? ''); ?></td>
            <td style="text-align: center;">
              <?php if ($row['status'] === 'confirmed'): ?>
                <div class="badge-confirmed">Dikonfirmasi</div>
              <?php elseif ($row['status'] === 'cancelled'): ?>
                <div class="badge-cancelled">Dibatalkan</div>
              <?php else: ?>
                <div class="badge-pending">Menunggu</div>
              <?php endif; ?>
            </td>
            <td style="text-align: center; color: #666666;">
              <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
            </td>
          </tr>
        <?php else: ?>
          <tr>
            <td style="text-align: center;"><?php echo $no++; ?></td>
            <td style="text-align: center;" class="text-bold">#<?php echo $row['id']; ?></td>
            <td class="text-bold"><?php echo htmlspecialchars($row['author'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($row['date_label'] ?? ''); ?></td>
            <td style="font-style: italic; color: #555555;"><?php echo htmlspecialchars($row['quote'] ?? ''); ?></td>
            <td><?php echo strip_tags($row['text'] ?? ''); ?></td>
            <td style="text-align: center;">
              <span class="badge-general"><?php echo htmlspecialchars($row['media_type'] ?? ''); ?></span>
            </td>
            <td><?php echo htmlspecialchars($row['media_url'] ?? ''); ?></td>
            <td style="text-align: center;">
              <?php if ($row['status'] === 'approved'): ?>
                <div class="badge-confirmed">Aktif</div>
              <?php else: ?>
                <div class="badge-pending">Pending</div>
              <?php endif; ?>
            </td>
            <td style="text-align: center; color: #666666;">
              <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
            </td>
          </tr>
        <?php endif; ?>
      <?php 
        endforeach;
      endif; 
      ?>
    </tbody>
  </table>

</body>
</html>
