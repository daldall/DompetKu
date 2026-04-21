const CACHE_NAME = "laravel-pwa-1776763915";
const OFFLINE_URL = "/offline.html";

const FILES_TO_CACHE = [
    "/",
    OFFLINE_URL
];

// Pre-cache critical resources
self.addEventListener("install", (event) => {
    console.log('[Laravel PWA] Service Worker installing...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(FILES_TO_CACHE))
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

    // ✅ Never cache non-GET requests (fix Cache.put POST error)
    if (request.method !== 'GET') {
        event.respondWith(fetch(request));
        return;
    }

    // ✅ Handle page navigation (offline fallback)
    if (request.mode === "navigate") {
        event.respondWith(
            fetch(request)
                .catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    // ✅ Cache-first for static assets
    if (
        request.destination === "style" ||
        request.destination === "script" ||
        request.destination === "image" ||
        request.destination === "font"
    ) {
        event.respondWith(
            caches.match(request)
                .then(cached => {
                    return cached || fetch(request).then(response => {
                        return caches.open(CACHE_NAME).then(cache => {
                            cache.put(request, response.clone());
                            return response;
                        });
                    });
                })
        );
        return;
    }

    // ✅ Default: network-first with cache fallback
    event.respondWith(
        fetch(request)
            .then(response => {
                return caches.open(CACHE_NAME).then(cache => {
                    cache.put(request, response.clone());
                    return response;
                });
            })
            .catch(async (error) => {
                // Retry failed API requests if Background Sync is supported
                if (request.method === 'POST' && 'SyncManager' in self) {
                    // We can't easily queue it here because we can't access IndexedDB easily without a library or boilerplate
                    // But we've already handled form submissions in background-sync.js
                }
                return caches.match(request);
            })
    );
});

// Background Sync
self.addEventListener('sync', (event) => {
    if (event.tag === 'laravel-pwa-sync') {
        event.waitUntil(syncRequests());
    }
});

async function syncRequests() {
    const db = await openDB();
    const tx = db.transaction('offline-requests', 'readonly');
    const store = tx.objectStore('offline-requests');
    const requests = await getAllRequests(store);

    for (const req of requests) {
        try {
            const response = await fetch(req.url, {
                method: req.method,
                headers: req.headers,
                body: req.body
            });

            if (response.ok) {
                const deleteTx = db.transaction('offline-requests', 'readwrite');
                deleteTx.objectStore('offline-requests').delete(req.id);
            }
        } catch (err) {
            console.error('[Laravel PWA] Sync failed for:', req.url, err);
        }
    }
}

function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('laravel-pwa-sync', 1);
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

function getAllRequests(store) {
    return new Promise((resolve, reject) => {
        const request = store.getAll();
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}
