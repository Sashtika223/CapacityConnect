const CACHE_NAME = 'capacity-connect-v2';
const OFFLINE_URL = '/offline';
const OUTBOX_DB = 'capacity-connect-outbox';

const ASSETS_TO_CACHE = [
  '/offline',
  '/css/app.css',
  '/manifest.json'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keyList) => {
      return Promise.all(
        keyList.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') {
    event.respondWith(fetch(event.request).catch(() => queueRequest(event.request)));
    return;
  }

  // Network-first for navigation requests
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request)
        .then((response) => {
          // Cache successful navigation responses for offline use
          const clone = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
          return response;
        })
        .catch(() => {
          // Only show offline page when network truly fails
          return caches.match(event.request).then((cached) => {
            return cached || caches.match(OFFLINE_URL);
          });
        })
    );
  } else {
    // Network-first for assets too, with cache fallback
    event.respondWith(
      fetch(event.request)
        .then((response) => {
          const clone = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
          return response;
        })
        .catch(() => {
          return caches.match(event.request);
        })
    );
  }
});

self.addEventListener('sync', (event) => {
  if (event.tag === 'capacity-connect-sync') {
    event.waitUntil(replayQueuedRequests());
  }
});

function openOutbox() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open(OUTBOX_DB, 1);
    request.onupgradeneeded = () => request.result.createObjectStore('requests', { autoIncrement: true });
    request.onsuccess = () => resolve(request.result);
    request.onerror = () => reject(request.error);
  });
}

async function queueRequest(request) {
  const database = await openOutbox();
  const body = await request.clone().text();
  await new Promise((resolve, reject) => {
    const transaction = database.transaction('requests', 'readwrite');
    transaction.objectStore('requests').add({ url: request.url, method: request.method, headers: [...request.headers], body });
    transaction.oncomplete = resolve;
    transaction.onerror = () => reject(transaction.error);
  });
  if ('sync' in self.registration) await self.registration.sync.register('capacity-connect-sync');
  return new Response(JSON.stringify({ queued: true }), { status: 202, headers: { 'Content-Type': 'application/json' } });
}

async function replayQueuedRequests() {
  const database = await openOutbox();
  const queued = await new Promise((resolve, reject) => {
    const transaction = database.transaction('requests', 'readonly');
    const store = transaction.objectStore('requests');
    const entriesRequest = store.getAll();
    const keysRequest = store.getAllKeys();
    entriesRequest.onsuccess = () => resolve({ entries: entriesRequest.result, keys: keysRequest.result });
    request.onerror = () => reject(request.error);
  });
  for (let index = 0; index < queued.entries.length; index++) {
    const entry = queued.entries[index];
    try {
      await fetch(entry.url, { method: entry.method, headers: Object.fromEntries(entry.headers), body: entry.body });
      await new Promise((resolve, reject) => {
        const transaction = database.transaction('requests', 'readwrite');
        transaction.objectStore('requests').delete(queued.keys[index]);
        transaction.oncomplete = resolve;
        transaction.onerror = () => reject(transaction.error);
      });
    } catch {
      break;
    }
  }
}
