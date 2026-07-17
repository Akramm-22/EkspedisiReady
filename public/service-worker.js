const VERSION = 'drgekspedisi-v3';
const STATIC_CACHE = `${VERSION}-static`;
const PAGE_CACHE = `${VERSION}-pages`;
const OFFLINE_URL = '/offline.html';

const PRECACHE_URLS = [
    OFFLINE_URL,
    '/manifest.json',
    '/icons/icon.svg',
    '/icons/icon-maskable.svg',
    '/pwa-register.js',
];

const PRIVATE_PREFIXES = [
    '/admin',
    '/customer',
    '/courier',
    '/login',
    '/logout',
    '/shipments',
    '/pembayaran',
];

const isPrivatePath = (pathname) =>
    PRIVATE_PREFIXES.some((prefix) => pathname === prefix || pathname.startsWith(`${prefix}/`));

const isCacheableResponse = (response) =>
    response && response.ok && (response.type === 'basic' || response.type === 'cors');

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(STATIC_CACHE).then((cache) => cache.addAll(PRECACHE_URLS)));
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        Promise.all([
            caches.keys().then((keys) =>
                Promise.all(
                    keys
                        .filter((key) => key.startsWith('drgekspedisi-') && ![STATIC_CACHE, PAGE_CACHE].includes(key))
                        .map((key) => caches.delete(key))
                )
            ),
            self.clients.claim(),
        ])
    );
});

const networkOnlyWithOfflineFallback = async (request) => {
    try {
        return await fetch(request);
    } catch (error) {
        if (request.mode === 'navigate') {
            return (await caches.match(OFFLINE_URL)) || Response.error();
        }

        throw error;
    }
};

const networkFirstPage = async (request) => {
    const cache = await caches.open(PAGE_CACHE);

    try {
        const response = await fetch(request);
        if (isCacheableResponse(response)) {
            await cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        return (await cache.match(request)) || (await caches.match(OFFLINE_URL)) || Response.error();
    }
};

const staleWhileRevalidate = async (request) => {
    const cache = await caches.open(STATIC_CACHE);
    const cached = await cache.match(request);

    const network = fetch(request)
        .then(async (response) => {
            if (isCacheableResponse(response)) {
                await cache.put(request, response.clone());
            }
            return response;
        })
        .catch(() => cached);

    return cached || network;
};

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (url.pathname.startsWith('/api/')) {
        event.respondWith(
            fetch(request).catch(
                () =>
                    new Response(JSON.stringify({ message: 'Tidak ada koneksi. Silakan coba lagi saat online.' }), {
                        status: 503,
                        headers: { 'Content-Type': 'application/json', 'Cache-Control': 'no-store' },
                    })
            )
        );
        return;
    }

    const isPageRequest = request.mode === 'navigate' || request.headers.get('X-Inertia') === 'true';

    if (isPageRequest) {
        event.respondWith(
            isPrivatePath(url.pathname)
                ? networkOnlyWithOfflineFallback(request)
                : networkFirstPage(request)
        );
        return;
    }

    if (['style', 'script', 'image', 'font'].includes(request.destination)) {
        event.respondWith(staleWhileRevalidate(request));
    }
});

self.addEventListener('message', (event) => {
    if (event.data?.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

self.addEventListener('sync', (event) => {
    if (event.tag === 'drg-offline-sync') {
        event.waitUntil(
            self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clients) => {
                clients.forEach((client) => client.postMessage({ type: 'TRIGGER_OFFLINE_SYNC' }));
            })
        );
    }
});

self.addEventListener('push', (event) => {
    const data = event.data?.json() ?? {};

    event.waitUntil(
        self.registration.showNotification(data.notification?.title ?? 'drgEkspedisi', {
            body: data.notification?.body ?? '',
            icon: '/icons/icon.svg',
            badge: '/icons/icon.svg',
            data: data.data ?? {},
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = event.notification.data?.url || '/';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(async (clients) => {
            const existing = clients.find((client) => new URL(client.url).origin === self.location.origin);
            if (existing) {
                await existing.focus();
                existing.navigate(targetUrl);
                return;
            }
            await self.clients.openWindow(targetUrl);
        })
    );
});
