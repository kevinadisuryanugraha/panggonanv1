/**
 * ADVANCED TRAFFIC DASHBOARD - PANGGONAN
 * Security, Data Visualization & Visitor Log Engine
 */

// 1. AUTHENTICATION LOGIC
const PASSWORD = "password";
const SESSION_KEY = "panggonan_dashboard_access";

// Cek apakah sudah login sebelumnya di sesi ini
document.addEventListener("DOMContentLoaded", () => {
    if (sessionStorage.getItem(SESSION_KEY) === "granted") {
        unlockDashboard(false);
    }
});

function verifyPassword() {
    const input = document.getElementById('auth-password').value;
    const errorMsg = document.getElementById('auth-error');

    if(input === PASSWORD) {
        errorMsg.classList.remove('show');
        // Simpan sesi ke browser
        sessionStorage.setItem(SESSION_KEY, "granted");
        unlockDashboard(true);
    } else {
        errorMsg.classList.add('show');
        const modal = document.querySelector('.auth-modal');
        modal.style.transform = "translateX(-10px)";
        setTimeout(() => modal.style.transform = "translateX(10px)", 100);
        setTimeout(() => modal.style.transform = "translateX(-10px)", 200);
        setTimeout(() => modal.style.transform = "translateX(0)", 300);
    }
}

function unlockDashboard(withAnimation) {
    if (withAnimation) {
        document.getElementById('auth-screen').classList.add('fade-out');
        setTimeout(() => {
            initDashboardComponents();
        }, 500);
    } else {
        initDashboardComponents();
    }
}

function initDashboardComponents() {
    document.getElementById('auth-screen').style.display = 'none';
    document.getElementById('main-dashboard').classList.remove('hidden');
    // Mencegah inisialisasi ganda jika di-refresh
    if (!window.dashboardInitialized) {
        initCharts();
        initVisitorLog();
        startLiveSimulation();
        startClock();
        window.dashboardInitialized = true;
    }
}

document.getElementById('auth-password').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') verifyPassword();
});

// Fungsi Logout
function logoutDashboard() {
    sessionStorage.removeItem(SESSION_KEY);
    window.location.reload();
}

// 2. CHART VISUALIZATIONS
function initCharts() {
    const labels = Array.from({length: 30}, (_, i) => `H-${30 - i}`);
    let currentVal = 200;
    const trafficData = labels.map(() => {
        currentVal = currentVal + (Math.random() * 40 - 15);
        return Math.floor(Math.max(currentVal, 50));
    });

    const ctxMain = document.getElementById('trafficChart').getContext('2d');
    let gradient = ctxMain.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.5)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

    new Chart(ctxMain, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Kunjungan Harian',
                data: trafficData,
                borderColor: '#6366f1',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#1e293b',
                pointBorderColor: '#6366f1',
                pointBorderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } },
                x: { grid: { display: false }, ticks: { maxTicksLimit: 10, color: '#94a3b8' } }
            }
        }
    });

    const ctxSource = document.getElementById('sourceChart').getContext('2d');
    new Chart(ctxSource, {
        type: 'doughnut',
        data: {
            labels: ['Pencarian Google', 'Instagram Link', 'Direct'],
            datasets: [{ data: [56, 30, 14], backgroundColor: ['#6366f1', '#10b981', '#f59e0b'], borderWidth: 0, hoverOffset: 4 }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', padding: 12, cornerRadius: 8 } }
        }
    });
}

// 3. LIVE SIMULATION
function startLiveSimulation() {
    const realtimeEl = document.getElementById('val-realtime');
    const totalVisitsEl = document.getElementById('val-kunjungan');
    let currentActive = 18;
    let totalVisits = 14258;

    setInterval(() => {
        const delta = Math.floor(Math.random() * 5) - 2;
        currentActive = Math.max(5, currentActive + delta);
        realtimeEl.innerText = currentActive;
        if (delta > 0 && Math.random() > 0.5) {
            totalVisits++;
            totalVisitsEl.innerText = totalVisits.toLocaleString('id-ID');
        }
    }, 3500);
}

// 4. LIVE CLOCK
function startClock() {
    const clockEl = document.getElementById('status-clock');
    function update() {
        const now = new Date();
        clockEl.textContent = now.toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'medium' });
    }
    update();
    setInterval(update, 1000);
}

// =============================================
// 5. VISITOR LOG TABLE ENGINE
// =============================================

// --- Data Generator ---
const PAGES_LIST = ['/beranda', '/menu', '/tentang-kami', '/layanan', '/jurnal', '/galeri', '/kontak', '/faq'];
const BROWSERS = ['Chrome 124', 'Safari 17.4', 'Firefox 125', 'Edge 124', 'Samsung Browser', 'Opera 109', 'Chrome Mobile 124', 'Safari Mobile'];
const DEVICES = [
    { name: 'iPhone 15', type: 'mobile', icon: 'fa-mobile-screen' },
    { name: 'Samsung S24', type: 'mobile', icon: 'fa-mobile-screen' },
    { name: 'Xiaomi 14', type: 'mobile', icon: 'fa-mobile-screen' },
    { name: 'OPPO Find X7', type: 'mobile', icon: 'fa-mobile-screen' },
    { name: 'Vivo V30', type: 'mobile', icon: 'fa-mobile-screen' },
    { name: 'Windows PC', type: 'desktop', icon: 'fa-desktop' },
    { name: 'MacBook Pro', type: 'desktop', icon: 'fa-laptop' },
    { name: 'iMac', type: 'desktop', icon: 'fa-desktop' },
    { name: 'iPad Pro', type: 'tablet', icon: 'fa-tablet-screen-button' },
    { name: 'Galaxy Tab S9', type: 'tablet', icon: 'fa-tablet-screen-button' },
];
const LOCATIONS = [
    'Jakarta Timur, ID', 'Depok, ID', 'Ciracas, ID', 'Bekasi, ID', 'Bogor, ID',
    'Tangerang, ID', 'Jakarta Selatan, ID', 'Jakarta Barat, ID', 'Bandung, ID',
    'Semarang, ID', 'Surabaya, ID', 'Yogyakarta, ID', 'Cibubur, ID',
    'Cileungsi, ID', 'Jakarta Pusat, ID', 'Jakarta Utara, ID',
];
const IP_PREFIXES = ['103.28', '114.124', '180.252', '36.72', '182.1', '203.130', '110.136', '112.215', '120.188', '202.67', '125.163', '27.50', '103.3', '139.228', '175.45'];
const STATUSES = ['active', 'ended', 'ended', 'ended', 'ended', 'ended', 'bounced']; // weighted toward ended

function randomIP() {
    const prefix = IP_PREFIXES[Math.floor(Math.random() * IP_PREFIXES.length)];
    return `${prefix}.${Math.floor(Math.random() * 255)}.${Math.floor(Math.random() * 255)}`;
}

function randomDuration(status) {
    if (status === 'bounced') return Math.floor(Math.random() * 8) + 1; // 1-8 seconds
    if (status === 'active') return Math.floor(Math.random() * 300) + 30; // 30s-5.5min
    return Math.floor(Math.random() * 600) + 10; // 10s-10min
}

function formatDuration(seconds) {
    if (seconds < 60) return `${seconds}s`;
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}m ${s < 10 ? '0' : ''}${s}s`;
}

function generateVisitorData(count, daysBack) {
    const data = [];
    const now = new Date();
    for (let i = 0; i < count; i++) {
        const date = new Date(now.getTime() - Math.random() * daysBack * 24 * 60 * 60 * 1000);
        const device = DEVICES[Math.floor(Math.random() * DEVICES.length)];
        const status = STATUSES[Math.floor(Math.random() * STATUSES.length)];
        const dur = randomDuration(status);
        data.push({
            date: date,
            ip: randomIP(),
            device: device,
            browser: BROWSERS[Math.floor(Math.random() * BROWSERS.length)],
            location: LOCATIONS[Math.floor(Math.random() * LOCATIONS.length)],
            page: PAGES_LIST[Math.floor(Math.random() * PAGES_LIST.length)],
            duration: dur,
            durationFormatted: formatDuration(dur),
            status: status,
        });
    }
    // Sort by date descending (newest first)
    data.sort((a, b) => b.date - a.date);
    return data;
}

// --- State ---
let ALL_VISITORS = [];
let filteredVisitors = [];
let currentPage = 1;
const ROWS_PER_PAGE = 20;
let currentSortCol = -1;
let currentSortAsc = true;

// --- Init ---
function initVisitorLog() {
    // Generate a full year of data (~8000 entries)
    ALL_VISITORS = generateVisitorData(8240, 365);
    filterVisitors('hari', document.querySelector('.filter-tab.active'));
}

// --- Filter ---
function filterVisitors(period, btnEl) {
    // Update active tab
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    btnEl.classList.add('active');

    const now = new Date();
    let cutoff;
    switch(period) {
        case 'hari':
            cutoff = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            break;
        case 'minggu':
            const dayOfWeek = now.getDay();
            cutoff = new Date(now.getFullYear(), now.getMonth(), now.getDate() - dayOfWeek);
            break;
        case 'bulan':
            cutoff = new Date(now.getFullYear(), now.getMonth(), 1);
            break;
        case 'tahun':
            cutoff = new Date(now.getFullYear(), 0, 1);
            break;
    }

    filteredVisitors = ALL_VISITORS.filter(v => v.date >= cutoff);
    currentPage = 1;
    document.getElementById('visitor-search').value = '';
    renderTable();
}

// --- Search ---
function searchVisitors(query) {
    const q = query.toLowerCase().trim();
    if (!q) {
        // Re-apply current filter
        const activeBtn = document.querySelector('.filter-tab.active');
        filterVisitors(activeBtn.dataset.filter, activeBtn);
        return;
    }
    filteredVisitors = filteredVisitors.filter(v =>
        v.ip.includes(q) ||
        v.location.toLowerCase().includes(q) ||
        v.page.toLowerCase().includes(q) ||
        v.device.name.toLowerCase().includes(q) ||
        v.browser.toLowerCase().includes(q)
    );
    currentPage = 1;
    renderTable();
}

// --- Sort ---
function sortTable(colIndex) {
    if (currentSortCol === colIndex) {
        currentSortAsc = !currentSortAsc;
    } else {
        currentSortCol = colIndex;
        currentSortAsc = true;
    }
    
    filteredVisitors.sort((a, b) => {
        let valA, valB;
        switch(colIndex) {
            case 0: return 0; // No = index, skip
            case 1: valA = a.date.getTime(); valB = b.date.getTime(); break;
            case 2: valA = a.ip; valB = b.ip; break;
            case 5: valA = a.location; valB = b.location; break;
            case 7: valA = a.duration; valB = b.duration; break;
            default: return 0;
        }
        if (typeof valA === 'string') {
            return currentSortAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
        }
        return currentSortAsc ? valA - valB : valB - valA;
    });

    currentPage = 1;
    renderTable();
}

// --- Pagination ---
function changePage(dir) {
    const totalPages = Math.ceil(filteredVisitors.length / ROWS_PER_PAGE);
    currentPage = Math.max(1, Math.min(currentPage + dir, totalPages));
    renderTable();
}

// --- Render ---
function renderTable() {
    const tbody = document.getElementById('visitor-tbody');
    const total = filteredVisitors.length;
    const totalPages = Math.max(1, Math.ceil(total / ROWS_PER_PAGE));
    
    // Guard page
    if (currentPage > totalPages) currentPage = totalPages;
    
    const start = (currentPage - 1) * ROWS_PER_PAGE;
    const end = Math.min(start + ROWS_PER_PAGE, total);
    const pageData = filteredVisitors.slice(start, end);

    // Build rows
    let html = '';
    pageData.forEach((v, idx) => {
        const rowNum = start + idx + 1;
        const dateStr = v.date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        const timeStr = v.date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

        const statusLabel = v.status === 'active' ? 'Aktif' : v.status === 'bounced' ? 'Bounce' : 'Selesai';

        html += `<tr style="animation-delay: ${idx * 0.03}s">
            <td style="color: var(--text-muted); font-weight:600;">${rowNum}</td>
            <td>${dateStr}<br><span style="color:var(--text-muted);font-size:0.75rem">${timeStr}</span></td>
            <td class="ip-cell">${v.ip}</td>
            <td><span class="device-badge ${v.device.type}"><i class="fas ${v.device.icon}"></i> ${v.device.name}</span></td>
            <td>${v.browser}</td>
            <td>${v.location}</td>
            <td><span class="page-pill">${v.page}</span></td>
            <td class="duration-cell">${v.durationFormatted}</td>
            <td><span class="status-dot ${v.status}">${statusLabel}</span></td>
        </tr>`;
    });

    tbody.innerHTML = html;

    // Update footer
    document.getElementById('log-count-badge').textContent = `${total.toLocaleString('id-ID')} entri`;
    document.getElementById('table-info').textContent = total > 0
        ? `Menampilkan ${start + 1}-${end} dari ${total.toLocaleString('id-ID')} entri`
        : 'Tidak ada data ditemukan';
    document.getElementById('page-indicator').textContent = `Halaman ${currentPage} / ${totalPages}`;
}
