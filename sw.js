// Service Worker para GeeksPOS PWA Pedidos Online
const CACHE_NAME = 'geekspos-pwa-v1';
const STATIC_ASSETS = [
    '/app-assets/images/ico/icon-192.png',
    '/app-assets/images/ico/icon-512.png',
    '/app-assets/images/ico/favicon-32.png',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch((err) => {
                console.warn('[PWA] Cache pre-fetch non-critical error:', err);
            });
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        })
    );
    return self.clients.claim();
});

// Network-first with cache fallback
self.addEventListener('fetch', (event) => {
    const req = event.request;
    const url = new URL(req.url);

    // Dynamic API endpoints should always go to network directly
    if (req.method !== 'GET' || url.pathname.includes('/chat') || url.pathname.includes('/confirm_order') || url.pathname.includes('/upload_comprobante')) {
        return;
    }

    event.respondWith(
        fetch(req)
            .then((networkResponse) => {
                // Cache static assets on the fly
                if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(req, responseClone);
                    });
                }
                return networkResponse;
            })
            .catch(() => {
                return caches.match(req);
            })
    );
});
