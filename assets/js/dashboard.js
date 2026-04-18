/**
 * ADVANCED TRAFFIC DASHBOARD - PANGGONAN
 * Security and Data Visualization Scripts
 */

// 1. AUTHENTICATION LOGIC
const PASSWORD = "password"; // Set by user

function verifyPassword() {
    const input = document.getElementById('auth-password').value;
    const errorMsg = document.getElementById('auth-error');
    
    if(input === PASSWORD) {
        // Success
        errorMsg.classList.remove('show');
        document.getElementById('auth-screen').classList.add('fade-out');
        
        setTimeout(() => {
            document.getElementById('auth-screen').style.display = 'none';
            document.getElementById('main-dashboard').classList.remove('hidden');
            // Initialize charts only after dashboard is visible so they size correctly
            initCharts();
            startLiveSimulation();
        }, 500);
    } else {
        // Fail
        errorMsg.classList.add('show');
        // minor shake animation
        const modal = document.querySelector('.auth-modal');
        modal.style.transform = "translateX(-10px)";
        setTimeout(() => modal.style.transform = "translateX(10px)", 100);
        setTimeout(() => modal.style.transform = "translateX(-10px)", 200);
        setTimeout(() => modal.style.transform = "translateX(0)", 300);
    }
}

// Allow Enter key to submit
document.getElementById('auth-password').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        verifyPassword();
    }
});

// 2. CHART VISUALIZATIONS (CHART.JS)
function initCharts() {
    // Generate some believable dummy data for the last 30 days
    const labels = Array.from({length: 30}, (_, i) => `H-${30 - i}`);
    
    // Base trend + noise
    let currentVal = 200;
    const trafficData = labels.map(() => {
        currentVal = currentVal + (Math.random() * 40 - 15); // Slight upward trend
        return Math.floor(Math.max(currentVal, 50));
    });

    // --- Main Traffic Chart ---
    const ctxMain = document.getElementById('trafficChart').getContext('2d');
    
    // Gradient fill
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
                tension: 0.4 // curves
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
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { maxTicksLimit: 10, color: '#94a3b8' }
                }
            }
        }
    });

    // --- Source Chart (Doughnut) ---
    const ctxSource = document.getElementById('sourceChart').getContext('2d');
    new Chart(ctxSource, {
        type: 'doughnut',
        data: {
            labels: ['Pencarian Google', 'Instagram Link', 'Direct'],
            datasets: [{
                data: [56, 30, 14],
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    padding: 12,
                    cornerRadius: 8,
                }
            }
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
        // Fluctuate active users slightly
        const delta = Math.floor(Math.random() * 5) - 2; // -2, -1, 0, 1, 2
        currentActive = Math.max(5, currentActive + delta);
        realtimeEl.innerText = currentActive;

        // If active user goes up, maybe total visit goes up
        if (delta > 0 && Math.random() > 0.5) {
            totalVisits++;
            totalVisitsEl.innerText = totalVisits.toLocaleString('id-ID'); // Format 14.258
        }
    }, 3500);
}
