const CACHE_NAME = "dompetku-pwa-static-1776763915";
const OFFLINE_URL = "/offline.html";

// Security: only cache truly static public assets.
// Never pre-cache '/' because it can redirect to authenticated pages.
const FILES_TO_CACHE = [
    OFFLINE_URL,
    "/manifest.json",
    "/favicon.ico",
    "/logo.png",
    "/heroimage.jpeg",
    "/icon-72x72.png",
    "/icon-96x96.png",
    "/icon-128x128.png",
    "/icon-144x144.png",
    "/icon-152x152.png",
    "/icon-192x192.png",
    "/icon-384x384.png",
    "/icon-512x512.png",
];

// Pre-cache critical resources
self.addEventListener("install", (event) => {
    console.log('[Laravel PWA] Service Worker installing...');
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(FILES_TO_CACHE))
    );
});

// Remove old caches
self.addEventListener("activate", (event) => {
    console.log('[Laravel PWA] Service Worker activated.');
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.map(key => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            )
        )
    );
    self.clients.claim();
});

// Listen for skip waiting message
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// Fetch strategy
self.addEventListener("fetch", (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Never interfere with non-GET.
    if (request.method !== 'GET') {
        return;
    }

    // Only handle same-origin requests.
    if (url.origin !== self.location.origin) {
        return;
    }

    // Navigation: network-first, offline fallback. Do NOT cache HTML pages.
    if (request.mode === "navigate") {
        event.respondWith(
            fetch(request).catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    // Cache-first for static assets only.
    const isStaticAsset = (
        request.destination === "style" ||
        request.destination === "script" ||
        request.destination === "image" ||
        request.destination === "font"
    );

    if (!isStaticAsset) {
        // Security: do not cache other requests (HTML, JSON, etc.)
        return;
    }

    event.respondWith(
        caches.match(request).then((cached) => {
            if (cached) return cached;

            return fetch(request).then((response) => {
                // Only cache successful basic responses.
                if (!response || response.status !== 200 || response.type !== 'basic') {
                    return response;
                }

                const copy = response.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                return response;
            });
        })
    );
});
