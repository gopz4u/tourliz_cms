const CACHE_NAME = 'tourliz-pwa-v5';
const OFFLINE_URL = '/offline.html';

// Only public shell assets are precached. Authenticated CMS pages and responses
// must never be stored in the browser cache by the service worker.
const APP_SHELL = [
    OFFLINE_URL,
    '/favicon.ico',
    '/manifest.json',
    '/img/tourliz_logo.png',
    '/img/pwa-icon-192.png',
    '/img/pwa-icon-512.png',
    '/img/apple-touch-icon.png'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(APP_SHELL))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys()
            .then(names => Promise.all(
                names.filter(name => name !== CACHE_NAME).map(name => caches.delete(name))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    const request = event.request;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // Never intercept third-party assets, APIs, uploads, or authenticated files.
    if (url.origin !== self.location.origin ||
        url.pathname.startsWith('/api/') ||
        url.pathname.startsWith('/storage/')) {
        return;
    }

    // HTML is network-only. When offline, show a generic page instead of a
    // previously cached authenticated screen containing customer information.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    const isPublicAsset = APP_SHELL.includes(url.pathname) ||
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/css/') ||
        url.pathname.startsWith('/js/') ||
        url.pathname.startsWith('/img/');

    if (!isPublicAsset) {
        return;
    }

    // Stale-while-revalidate keeps the UI quick while refreshing branded assets.
    event.respondWith(
        caches.match(request).then(cached => {
            const fresh = fetch(request).then(response => {
                if (response.ok) {
                    caches.open(CACHE_NAME).then(cache => cache.put(request, response.clone()));
                }
                return response;
            }).catch(() => cached);

            return cached || fresh;
        })
    );
});

self.addEventListener('message', event => {
    if (event.data === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

self.addEventListener('push', event => {
    let payload = {
        title: 'Tourliz follow-up',
        body: 'A sales follow-up needs your attention.',
        url: '/admin/follow-ups',
        tag: 'tourliz-follow-up'
    };

    try {
        if (event.data) {
            payload = Object.assign(payload, event.data.json());
        }
    } catch (error) {
        // Invalid payloads should still produce a useful, generic alert.
    }

    event.waitUntil(
        self.registration.showNotification(payload.title, {
            body: payload.body,
            icon: '/img/pwa-icon-192.png',
            badge: '/img/pwa-icon-192.png',
            tag: payload.tag || 'tourliz-follow-up',
            data: { url: payload.url || '/admin/follow-ups' }
        })
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();

    const targetUrl = new URL(
        (event.notification.data && event.notification.data.url) || '/admin/b2c-itineraries',
        self.location.origin
    ).href;

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            for (const client of windowClients) {
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }

            return clients.openWindow(targetUrl);
        })
    );
});
