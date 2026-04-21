const SYNC_STORE_NAME = 'offline-requests';
const DB_NAME = 'laravel-pwa-sync';
const DB_VERSION = 1;

function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);
        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains(SYNC_STORE_NAME)) {
                db.createObjectStore(SYNC_STORE_NAME, { keyPath: 'id', autoIncrement: true });
            }
        };
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

async function queueRequest(request) {
    let body;
    let headers = Object.fromEntries(request.headers.entries());

    if (request.headers.get('content-type') && request.headers.get('content-type').includes('multipart/form-data')) {
        // FormData needs special handling if we want to serialize it
        const formData = await request.clone().formData();
        body = JSON.stringify(Object.fromEntries(formData.entries()));
        headers['content-type'] = 'application/json'; // Convert to JSON for easier sync
    } else {
        body = await request.clone().text();
    }

    const db = await openDB();
    const tx = db.transaction(SYNC_STORE_NAME, 'readwrite');
    const store = tx.objectStore(SYNC_STORE_NAME);

    const serializedRequest = {
        url: request.url,
        method: request.method,
        headers: headers,
        body: body,
        timestamp: Date.now()
    };

    store.add(serializedRequest);

    if ('serviceWorker' in navigator && 'SyncManager' in window) {
        try {
            const registration = await navigator.serviceWorker.ready;
            await registration.sync.register('laravel-pwa-sync');
        } catch (e) {
            console.error('[Laravel PWA] Background Sync registration failed:', e);
        }
    }
}

// Intercept form submissions
document.addEventListener('submit', async (event) => {
    if (!navigator.onLine) {
        const form = event.target;
        if (form.method.toLowerCase() === 'post') {
            event.preventDefault();
            const formData = new FormData(form);
            const url = form.action;

            const request = new Request(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });

            await queueRequest(request);
            alert('You are offline. Your form submission has been queued and will be synced when you are back online.');
        }
    }
});

// Optional: Intercept fetch requests (might be too intrusive if not careful)
// This is just a helper that developers can use
window.laravelPwaSync = {
    queue: queueRequest
};
