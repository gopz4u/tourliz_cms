const CACHE_NAME = 'tourliz-cache-v3';
const OFFLINE_URL = '/offline.html';

const ASSETS_TO_CACHE = [
    OFFLINE_URL,
    '/favicon.ico',
    '/img/tourliz_logo.png',
    '/img/pwa-icon-192.png',
    '/img/pwa-icon-512.png',
    '/img/apple-touch-icon.png',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css',
    'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
    'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
    'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap'
];

// Install event: cache initial resources
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(ASSETS_TO_CACHE);
        }).then(() => self.skipWaiting())
    );
});

// Activate event: clean up old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event: Network-first for pages, Cache-first for assets
self.addEventListener('fetch', event => {
    const request = event.request;
    const url = new URL(request.url);

    // Skip non-GET requests or requests to non-http/https protocols
    if (request.method !== 'GET' || (!request.url.startsWith(self.location.origin) && !request.url.startsWith('http'))) {
        return;
    }

    // Determine if it is a page request (navigation)
    if (request.mode === 'navigate' || (request.headers.get('Accept') && request.headers.get('Accept').includes('text/html'))) {
        event.respondWith(
            fetch(request)
                .then(response => {
                    // Cache the newly fetched page
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(request, responseClone);
                    });
                    return response;
                })
                .catch(() => {
                    // Offline fallback: try to serve from cache, otherwise show offline page
                    return caches.match(request).then(cachedResponse => {
                        return cachedResponse || caches.match(OFFLINE_URL);
                    });
                })
        );
    } else {
        // Cache-first for static assets
        event.respondWith(
            caches.match(request).then(cachedResponse => {
                if (cachedResponse) {
                    return cachedResponse;
                }
                return fetch(request).then(response => {
                    // Cache static assets on the fly
                    const responseClone = response.clone();
                    // Only cache successful requests from our origin or CDNs
                    if (response.status === 200 && (url.origin === self.location.origin || url.hostname.includes('cdn') || url.hostname.includes('fonts'))) {
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(request, responseClone);
                        });
                    }
                    return response;
                });
            })
        );
    }
});
