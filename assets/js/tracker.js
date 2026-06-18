/**
 * Panggonan Resto — Dynamic Self-Hosted Web Traffic Tracker
 * Automatically tracks page views, session duration, and click conversions.
 */

(function() {
    /* set to true to enable debug console output */
    var DEBUG = false;
    function _log() { if (DEBUG) console.log.apply(console, arguments); }
    function _warn() { if (DEBUG) console.warn.apply(console, arguments); }
    function _error() { if (DEBUG) console.error.apply(console, arguments); }

    document.addEventListener("DOMContentLoaded", () => {
        initTracker();
    });

    async function getClientIp() {
        try {
            // Cek cache sesi agar tidak berulang kali fetch API eksternal
            const cached = sessionStorage.getItem('client_public_ip');
            if (cached) return cached;

            // Fetch IP publik dengan batas waktu timeout 1.2 detik
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 1200);

            const res = await fetch('https://api.ipify.org?format=json', {
                signal: controller.signal
            });
            clearTimeout(timeoutId);

            if (res.ok) {
                const json = await res.json();
                if (json.ip) {
                    sessionStorage.setItem('client_public_ip', json.ip);
                    return json.ip;
                }
            }
        } catch (e) {
            console.warn('[Tracker] Gagal mengambil IP publik untuk uji coba lokal:', e.message);
        }
        return null;
    }

    async function initTracker() {
        // 1. Resolve Dynamic Backend API URL berdasarkan path script
        const trackerScript = document.querySelector('script[src*="tracker.js"]');
        let trackerUrl = 'admin/track_visitor.php'; // default root fallback
        
        if (trackerScript) {
            const srcAttr = trackerScript.getAttribute('src');
            if (srcAttr) {
                // Pertahankan folder traversal relatif (misal: "../assets/js/tracker.js")
                trackerUrl = srcAttr.replace('assets/js/tracker.js', 'admin/track_visitor.php');
            }
        } else {
            // Failsafe cadangan menggunakan document.currentScript
            const scriptSrc = document.currentScript ? document.currentScript.src : '';
            if (scriptSrc && scriptSrc.includes('assets/js/tracker.js')) {
                trackerUrl = scriptSrc.replace('assets/js/tracker.js', 'admin/track_visitor.php');
            } else {
                trackerUrl = '../admin/track_visitor.php';
            }
        }

        // 2. Identify Page Name
        const pageName = getCleanPageName();

        // 3. Dapatkan IP Publik (Penting untuk akurasi lokasi saat testing di localhost)
        const clientIp = await getClientIp();

        // 4. Log Pageview (Instant on load)
        sendTrackingData(trackerUrl, {
            action: 'pageview',
            page: pageName,
            client_ip: clientIp
        });

        // 5. Keep-Alive Ping (Setiap 10 detik)
        setInterval(() => {
            sendTrackingData(trackerUrl, {
                action: 'keepalive',
                page: pageName,
                client_ip: clientIp
            });
        }, 10000);

        // 6. Hook WhatsApp Conversions & Clicks
        setupConversionListeners(trackerUrl, pageName);

        // 7. Inject PWA Manifest dynamically
        injectPWAManifest(trackerUrl);

        // 8. Register PWA Service Worker dynamically
        registerPWAServiceWorker(trackerUrl);
    }

    function getCleanPageName() {
        const path = window.location.pathname.toLowerCase();
        
        if (path.includes('/menu/')) return 'Menu';
        if (path.includes('/about-us/')) return 'Tentang Kami';
        if (path.includes('/services/')) return 'Layanan';
        if (path.includes('/gallery/')) return 'Galeri';
        if (path.includes('/faq/')) return 'Tanya Jawab';
        if (path.includes('/blog/')) return 'Jurnal';
        if (path.includes('/contact-us/')) return 'Hubungi Kami';
        
        // If it's homepage or root
        if (path.endsWith('/') || path.endsWith('/index.html') || path.endsWith('/index.php')) {
            return 'Beranda';
        }
        
        // Default to title
        return document.title.split('—')[0].trim() || 'Halaman Utama';
    }

    function sendTrackingData(url, data) {
        if (!url) return;
        
        fetch(url, {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            },
            keepalive: true // ensures request finishes even if page closes
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(res => {
            _log(`%c[Tracker] Success: ${data.action} ->`, 'color: #10b981; font-weight: bold;', res);
        })
        .catch(err => {
            _error(`%c[Tracker] Failed to sync ${data.action}:`, 'color: #ef4444; font-weight: bold;', err);
        });
    }

    function setupConversionListeners(url, currentPage) {
        // Track clicks on any WhatsApp or Google Maps link
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (link && link.href) {
                const href = link.href.toLowerCase();
                if (href.includes('wa.me') || href.includes('whatsapp.com/send') || href.includes('api.whatsapp.com')) {
                    // It's a WhatsApp click conversion!
                    sendTrackingData(url, {
                        action: 'conversion',
                        conversion_type: 'whatsapp_click',
                        page: currentPage
                    });
                } else if (href.includes('maps.google.com') || href.includes('google.com/maps')) {
                    // It's a Google Maps direction click conversion!
                    sendTrackingData(url, {
                        action: 'conversion',
                        conversion_type: 'maps_click',
                        page: currentPage
                    });
                }
            }
        });

        // Track successful Reservation form submissions
        // 1. WhatsApp form submit
        const waForm = document.getElementById('wa-reservation-form');
        if (waForm) {
            waForm.addEventListener('submit', () => {
                sendTrackingData(url, {
                    action: 'conversion',
                    conversion_type: 'whatsapp_click', // Form submission redirects to WhatsApp
                    page: currentPage
                });
            });
        }

        // 2. Custom Jurnal Story form submit
        const storyForm = document.getElementById('panggonan-story-form');
        if (storyForm) {
            storyForm.addEventListener('submit', () => {
                sendTrackingData(url, {
                    action: 'conversion',
                    conversion_type: 'story_submit',
                    page: currentPage
                });
            });
        }
    }

    function injectPWAManifest(trackerUrl) {
        try {
            // Selesaikan alamat manifest.json secara dinamis berdasarkan trackerUrl
            const manifestUrl = trackerUrl.replace('admin/track_visitor.php', 'manifest.json');
            
            // Periksa apakah tag manifest sudah ada di head dokumen
            let manifestLink = document.querySelector('link[rel="manifest"]');
            if (!manifestLink) {
                manifestLink = document.createElement('link');
                manifestLink.rel = 'manifest';
                manifestLink.href = manifestUrl;
                document.head.appendChild(manifestLink);
                _log('%c[PWA] Dynamic Manifest link injected successfully:', 'color: #d4af37;', manifestUrl);
            }
        } catch (e) {
            _error('[PWA] Gagal menyuntikkan link manifest:', e.message);
        }
    }

    function registerPWAServiceWorker(trackerUrl) {
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                try {
                    // Selesaikan alamat sw.js secara dinamis berdasarkan trackerUrl
                    const swUrl = trackerUrl.replace('admin/track_visitor.php', 'sw.js');
                    
                    navigator.serviceWorker.register(swUrl)
                        .then(reg => {
                            _log('%c[PWA] Service Worker registered successfully! Scope:', 'color: #10b981; font-weight: bold;', reg.scope);
                        })
                        .catch(err => {
                            console.warn('[PWA] Service Worker registration failed:', err.message);
                        });
                } catch (e) {
                    _error('[PWA] Gagal menyelesaikan URL Service Worker:', e.message);
                }
            });
        }
    }
})();
