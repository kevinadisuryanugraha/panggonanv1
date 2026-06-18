/**
 * Panggonan Resto — Progressive Web App Service Worker
 * Implements a smart network-first caching strategy for dynamic HTML pages and
 * a cache-first strategy for static assets, while fully bypassing admin zones.
 */

const CACHE_NAME = 'panggonan-pwa-static-v3';
const DYNAMIC_CACHE_NAME = 'panggonan-pwa-dynamic-v3';

// Core static assets to cache on installation
const STATIC_ASSETS = [
    './index.html',
    './manifest.json',
    './assets/css/style.css',
    './assets/css/custom.css',
    './assets/css/home.css',
    './assets/css/menu.css',
    './assets/js/jquery-3.5.1.min.js',
    './assets/js/script.js',
    './assets/js/tracker.js',
    './assets/images/logo.webp',
    './assets/images/icon-192x192.png',
    './assets/images/icon-512x512.png'
];

// 1. INSTALL EVENT: Pre-cache core shell assets
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            console.log('[Service Worker] Pre-caching offline shell assets');
            return cache.addAll(STATIC_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

// 2. ACTIVATE EVENT: Clean up stale caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.map(key => {
                    if (key !== CACHE_NAME && key !== DYNAMIC_CACHE_NAME) {
                        console.log('[Service Worker] Cleaning up stale cache:', key);
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// 3. FETCH EVENT: Apply caching strategy
self.addEventListener('fetch', event => {
    const request = event.request;
    const url = new URL(request.url);

    // CRITICAL SECURITY BYPASS: Skip caching and interception for all admin related files and analytics API
    if (
        url.pathname.includes('/admin/') || 
        url.pathname.includes('/admin-traffic/') ||
        url.pathname.includes('get_traffic_data.php') || 
        url.pathname.includes('track_visitor.php') ||
        request.method !== 'GET'
    ) {
        // Let the browser handle these requests directly from the network
        return;
    }

    // A. STRATEGY FOR HTML PAGES: Network-First, Cache-Fallback
    // This guarantees that online users always see real-time updates (e.g. menu, contact info)
    // while offline users fall back to their last cached version.
    if (request.headers.get('accept') && request.headers.get('accept').includes('text/html')) {
        event.respondWith(
            fetch(request)
                .then(response => {
                    // Cache the successful response dynamically
                    const responseClone = response.clone();
                    caches.open(DYNAMIC_CACHE_NAME).then(cache => {
                        cache.put(request, responseClone);
                    });
                    return response;
                })
                .catch(() => {
                    // Offline fallback: load from static or dynamic cache
                    return caches.match(request).then(cachedResponse => {
                        if (cachedResponse) {
                            return cachedResponse;
                        }
                        // Default offline fallback for unmatched HTML pages
                        return caches.match('./index.html');
                    });
                })
        );
        return;
    }

    // B. STRATEGY FOR STATIC ASSETS (CSS, JS, Fonts, Images): Cache-First, Network-Fallback
    // Makes page load extremely fast by pulling assets directly from local storage.
    event.respondWith(
        caches.match(request).then(cachedResponse => {
            if (cachedResponse) {
                return cachedResponse;
            }

            return fetch(request).then(response => {
                // Do not cache third-party scripts or error responses
                if (!response || response.status !== 200 || response.type !== 'basic') {
                    return response;
                }

                const responseClone = response.clone();
                caches.open(DYNAMIC_CACHE_NAME).then(cache => {
                    cache.put(request, responseClone);
                });
                return response;
            });
        })
    );
});
