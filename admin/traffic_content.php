<!-- TRAFFIC ANALYTICS CONTENT -->
<div class="traffic-dashboard-wrapper">
  
  <div class="traffic-subnav">
    <button class="nav-link-btn active" onclick="switchView('view-ringkasan', this)"><i class="fas fa-chart-line"></i> Ringkasan Trafik</button>
    <button class="nav-link-btn inactive" onclick="switchView('view-demografi', this)"><i class="fas fa-users"></i> Demografi</button>
    <button class="nav-link-btn inactive" onclick="switchView('view-konversi', this)"><i class="fas fa-bullseye"></i> Perilaku Konversi</button>
    <button class="nav-link-btn inactive" onclick="switchView('view-pengaturan', this)"><i class="fas fa-cog"></i> Pengaturan GA4</button>
  </div>

  <div class="page-title" style="margin-bottom: 24px; display: flex; flex-wrap: wrap; align-items: center; gap: 12px;">
    <h1 id="topbar-title" style="font-family: var(--font-sora); font-size: 1.5rem; margin: 0;">Real-time Traffic Overview</h1>
    <span class="pulse-indicator" id="topbar-pulse" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.85rem; color: #10b981; padding: 4px 10px; background: rgba(16, 185, 129, 0.1); border-radius: 20px;">
      <span class="dot" style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; box-shadow: 0 0 8px #10b981; animation: pulse 1.5s infinite;"></span> Live Sync
    </span>
    
    <div style="display: flex; gap: 10px; margin-left: auto;">
      <button onclick="loadTrafficData(); const icon = this.querySelector('i'); icon.classList.add('fa-spin'); setTimeout(() => icon.classList.remove('fa-spin'), 700);" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 6px 14px; border-radius: 20px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; font-family: var(--font-inter); font-weight: 500; transition: background 0.2s;" title="Segarkan Data Sekarang">
        <i class="fas fa-sync-alt"></i> Refresh Data
      </button>
      <button onclick="if(confirm('Apakah Anda yakin ingin MENGHAPUS SEMUA riwayat trafik untuk memulai pelacakan baru dari nol (0)?')) { resetTrafficData(); }" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 6px 14px; border-radius: 20px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; font-family: var(--font-inter); font-weight: 500; transition: background 0.2s;" title="Mulai pelacakan baru dari nol">
        <i class="fas fa-trash-alt"></i> Reset Pelacakan (Mulai Baru)
      </button>
    </div>
  </div>

  <!-- VIEW CONTENT WRAPPER -->
  <div id="view-ringkasan" class="view-section" style="display: block">
    <!-- Metrics Cards -->
    <div class="metrics-grid traffic-metrics-grid">
      <div class="metric-card glass">
        <div class="metric-header">
          <span class="title">Total Kunjungan (Bulan Ini)</span>
          <i class="fas fa-users" style="color: #6366f1"></i>
        </div>
        <div class="metric-value" id="val-kunjungan">14,258</div>
        <div class="metric-trend positive">
          <i class="fas fa-arrow-up"></i> 12.4% vs bulan lalu
        </div>
      </div>

      <div class="metric-card glass">
        <div class="metric-header">
          <span class="title">Klik Reservasi / WA</span>
          <i class="fas fa-mouse-pointer" style="color: #10b981"></i>
        </div>
        <div class="metric-value" id="val-wa-clicks">0</div>
        <div class="metric-trend positive">
          <i class="fas fa-arrow-up"></i> 5.8% vs bulan lalu
        </div>
      </div>

      <div class="metric-card glass">
        <div class="metric-header">
          <span class="title">Durasi Sesi Rata-Rata</span>
          <i class="fas fa-clock" style="color: #f59e0b"></i>
        </div>
        <div class="metric-value" id="val-avg-duration">00m 00s</div>
        <div class="metric-trend neutral">
          <i class="fas fa-minus"></i> stabil
        </div>
      </div>

      <div class="metric-card glass">
        <div class="metric-header">
          <span class="title">Pengunjung Aktif Saat Ini</span>
          <i class="fas fa-broadcast-tower" style="color: #ef4444"></i>
        </div>
        <div class="metric-value pulse-text" id="val-realtime">18</div>
        <div class="metric-trend">Diperbarui 1 detik lalu</div>
      </div>
    </div>

    <!-- Charts Area -->
    <div class="charts-wrap">
      <div class="chart-box glass main-chart">
        <h3>Tren Traffic 30 Hari Terakhir</h3>
        <div class="chart-container">
          <canvas id="trafficChart"></canvas>
        </div>
      </div>

      <div class="chart-box glass side-chart">
        <h3>Sumber Kunjungan (Traffic Source)</h3>
        <div class="chart-container">
          <canvas id="sourceChart"></canvas>
        </div>
        <br />
        <ul class="source-list">
          <li>
            <span class="color-dot" style="background: #6366f1"></span>
            Pencarian Google (Search) <span class="perc">56%</span>
          </li>
          <li>
            <span class="color-dot" style="background: #10b981"></span>
            Instagram Link <span class="perc">30%</span>
          </li>
          <li>
            <span class="color-dot" style="background: #f59e0b"></span>
            Tautan Langsung (Direct) <span class="perc">14%</span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Visitor Log Table Section -->
    <div class="visitor-log-section glass">
      <div class="log-header">
        <div class="log-title-area">
          <h3><i class="fas fa-list-alt"></i> Log Pengunjung Website</h3>
          <span class="log-badge" id="log-count-badge">248 entri</span>
        </div>
        <div class="log-controls">
          <div class="filter-tabs">
            <button
              class="filter-tab active"
              data-filter="hari"
              onclick="filterVisitors('hari', this)"
            >
              Hari Ini
            </button>
            <button
              class="filter-tab"
              data-filter="minggu"
              onclick="filterVisitors('minggu', this)"
            >
              Minggu Ini
            </button>
            <button
              class="filter-tab"
              data-filter="bulan"
              onclick="filterVisitors('bulan', this)"
            >
              Bulan Ini
            </button>
            <button
              class="filter-tab"
              data-filter="tahun"
              onclick="filterVisitors('tahun', this)"
            >
              Tahun Ini
            </button>
          </div>
          <div class="log-search">
            <i class="fas fa-search"></i>
            <input
              type="text"
              id="visitor-search"
              placeholder="Cari IP / Lokasi / Halaman..."
              oninput="searchVisitors(this.value)"
            />
          </div>
        </div>
      </div>

      <div class="table-wrapper">
        <table class="visitor-table" id="visitor-table">
          <thead>
            <tr>
              <th class="sortable" onclick="sortTable(0)">
                No <i class="fas fa-sort"></i>
              </th>
              <th class="sortable" onclick="sortTable(1)">
                Tanggal & Waktu <i class="fas fa-sort"></i>
              </th>
              <th class="sortable" onclick="sortTable(2)">
                Alamat IP <i class="fas fa-sort"></i>
              </th>
              <th>Perangkat</th>
              <th>Browser</th>
              <th class="sortable" onclick="sortTable(5)">
                Lokasi <i class="fas fa-sort"></i>
              </th>
              <th>Halaman Dikunjungi</th>
              <th class="sortable" onclick="sortTable(7)">
                Durasi <i class="fas fa-sort"></i>
              </th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="visitor-tbody">
            <!-- Diisi oleh JavaScript -->
          </tbody>
        </table>
      </div>

      <div class="table-footer">
        <div class="table-info" id="table-info">
          Menampilkan 1-20 dari 248 entri
        </div>
        <div class="table-pagination">
          <button class="page-btn" onclick="changePage(-1)">
            <i class="fas fa-chevron-left"></i>
          </button>
          <span class="page-indicator" id="page-indicator"
            >Halaman 1 / 13</span
          >
          <button class="page-btn" onclick="changePage(1)">
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- End of view-ringkasan -->

  <!-- DEMOGRAFI VIEW -->
  <div id="view-demografi" class="view-section" style="display: none">
    <div class="metrics-grid traffic-metrics-grid">
      <div class="metric-card glass">
        <div class="metric-header">
          <span class="title">Total Pengguna Unik</span
          ><i class="fas fa-user-check" style="color: #6366f1"></i>
        </div>
        <div class="metric-value" id="val-unique-users">0</div>
      </div>
      <div class="metric-card glass">
        <div class="metric-header">
          <span class="title">Rasio Pria / Wanita</span
          ><i class="fas fa-venus-mars" style="color: #f43f5e"></i>
        </div>
        <div class="metric-value" id="val-gender-ratio">0% / 0%</div>
      </div>
      <div class="metric-card glass">
        <div class="metric-header">
          <span class="title">Rentang Usia Dominan</span
          ><i class="fas fa-id-card" style="color: #10b981"></i>
        </div>
        <div class="metric-value" id="val-age-dominant">-</div>
      </div>
    </div>
    <div class="charts-wrap" style="margin-top: 30px">
      <div class="chart-box glass main-chart">
        <h3>Distribusi Usia Pengunjung</h3>
        <div class="chart-container">
          <canvas id="ageChart"></canvas>
        </div>
      </div>
      <div class="chart-box glass side-chart">
        <h3>Perangkat yang Digunakan</h3>
        <div class="chart-container">
          <canvas id="deviceChart"></canvas>
        </div>
      </div>
    </div>
    <div class="visitor-log-section glass" style="margin-top: 30px">
      <div class="log-header">
        <h3>
          <i class="fas fa-map-marker-alt"></i> Top 5 Lokasi Pengunjung
        </h3>
      </div>
      <div class="table-wrapper">
        <table class="visitor-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Kota / Wilayah</th>
              <th>Jumlah Kunjungan</th>
              <th>Persentase</th>
            </tr>
          </thead>
          <tbody id="top-locations-tbody">
            <tr>
              <td colspan="4" style="text-align: center; color: var(--text-muted); font-style: italic; padding: 20px;">Tidak ada data lokasi.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- KONVERSI VIEW -->
  <div id="view-konversi" class="view-section" style="display: none">
    <div class="metrics-grid traffic-metrics-grid">
      <div class="metric-card glass">
        <div class="metric-header">
          <span class="title">Conversion Rate</span
          ><i class="fas fa-percentage" style="color: #10b981"></i>
        </div>
        <div class="metric-value" id="val-conversion-rate">0%</div>
        <div class="metric-trend positive">
          <i class="fas fa-arrow-up"></i> 1.2% vs bulan lalu
        </div>
      </div>
      <div class="metric-card glass">
        <div class="metric-header">
          <span class="title">Total Reservasi WA</span
          ><i class="fab fa-whatsapp" style="color: #22c55e"></i>
        </div>
        <div class="metric-value" id="val-conv-wa">0</div>
      </div>
      <div class="metric-card glass">
        <div class="metric-header">
          <span class="title">Klik Maps / Arah</span
          ><i class="fas fa-map-signs" style="color: #3b82f6"></i>
        </div>
        <div class="metric-value" id="val-conv-maps">0</div>
      </div>
    </div>
    <div class="charts-wrap" style="margin-top: 30px">
      <div class="chart-box glass" style="flex: 1">
        <h3>Funnel Konversi Pelanggan</h3>
        <div
          style="
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
          "
        >
          <div
            style="
              background: rgba(99, 102, 241, 0.2);
              padding: 15px;
              border-radius: 8px;
              border-left: 4px solid #6366f1;
            "
          >
            <div style="font-size: 0.85rem; color: var(--text-muted)">
              Langkah 1: Kunjungan Website (Traffic)
            </div>
            <div style="font-size: 1.5rem; font-weight: bold" id="val-funnel-step1">
              0
              <span style="font-size: 0.8rem; font-weight: normal"
                >100%</span
              >
            </div>
          </div>
          <div style="text-align: center; color: var(--text-muted)">
            <i class="fas fa-arrow-down"></i>
          </div>
          <div
            style="
              background: rgba(16, 185, 129, 0.2);
              padding: 15px;
              border-radius: 8px;
              border-left: 4px solid #10b981;
              width: 80%;
              margin: 0 auto;
            "
          >
            <div style="font-size: 0.85rem; color: var(--text-muted)">
              Langkah 2: Buka Halaman Menu & Kontak
            </div>
            <div style="font-size: 1.5rem; font-weight: bold" id="val-funnel-step2">
              0
              <span style="font-size: 0.8rem; font-weight: normal"
                >0%</span
              >
            </div>
          </div>
          <div style="text-align: center; color: var(--text-muted)">
            <i class="fas fa-arrow-down"></i>
          </div>
          <div
            style="
              background: rgba(245, 158, 11, 0.2);
              padding: 15px;
              border-radius: 8px;
              border-left: 4px solid #f59e0b;
              width: 60%;
              margin: 0 auto;
            "
          >
            <div style="font-size: 0.85rem; color: var(--text-muted)">
              Langkah 3: Klik Reservasi WA (Konversi)
            </div>
            <div style="font-size: 1.5rem; font-weight: bold" id="val-funnel-step3">
              0
              <span style="font-size: 0.8rem; font-weight: normal"
                >0%</span
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- PENGATURAN VIEW -->
  <div id="view-pengaturan" class="view-section" style="display: none">
    <div class="chart-box glass" style="max-width: 600px; margin: 0 auto">
      <h3>
        <i class="fab fa-google"></i> Konfigurasi Google Analytics 4
      </h3>
      <p
        style="
          color: var(--text-muted);
          font-size: 0.9rem;
          margin-bottom: 25px;
          line-height: 1.5;
        "
      >
        Hubungkan dasbor ini dengan properti GA4 Anda untuk menghentikan
        mode simulasi dan mulai menarik data analitik pengunjung secara
        langsung (Real-time).
      </p>
      <div style="margin-bottom: 20px">
        <label
          style="
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
          "
          >Measurement ID (G-XXXXXXX)</label
        >
        <input
          type="text"
          class="ga4-input"
          placeholder="Misal: G-PX9Y2Z8A"
          value="G-PX9Y2Z"
          style="
            width: 100%;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px 15px;
            border-radius: 8px;
            color: white;
            font-family: monospace;
          "
        />
      </div>
      <div style="margin-bottom: 25px">
        <label
          style="
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
          "
          >Google Analytics Data API Key (JSON Path)</label
        >
        <input
          type="password"
          class="ga4-input"
          placeholder="Masukkan path credentials json..."
          value="*************************"
          style="
            width: 100%;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px 15px;
            border-radius: 8px;
            color: white;
            font-family: monospace;
          "
        />
      </div>
      <div style="display: flex; gap: 15px">
        <button
          class="btn-auth"
          style="flex: 1"
          onclick="alert('Konfigurasi GA4 Berhasil Disimpan!')"
        >
          <i class="fas fa-save"></i> Simpan Konfigurasi
        </button>
        <button
          class="btn-auth"
          style="background: rgba(255, 255, 255, 0.1); width: auto"
          onclick="
            alert(
              'Uji koneksi ke server Google Analytics API sukses! Status: 200 OK',
            )
          "
        >
          <i class="fas fa-plug"></i> Test Koneksi
        </button>
      </div>
    </div>
  </div>

  <div class="status-bar glass" style="margin-top: 24px;">
    <i class="fas fa-shield-alt"></i> Google Analytics 4 (Data Stream:
    G-PX9Y2Z) Connected • Tembok Api Enkripsi AES-256 Aktif •
    <span id="status-clock"></span>
  </div>

</div>
