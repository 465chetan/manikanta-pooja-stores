/* ============================================================
   SERVICE WORKER — Sri Manikanta Pooja Stores PWA
   Strategy: Cache-first for static assets, Network-first for API
   ============================================================ */

const CACHE_NAME = 'smps-v1';
const OFFLINE_URL = '/index.html';

// Static assets to cache immediately on install
const PRECACHE_ASSETS = [
  '/index.html',
  '/shop.html',
  '/cart.html',
  '/about.html',
  '/contact.html',
  '/css/style.css',
  '/css/components.css',
  '/css/home.css',
  '/css/shop.css',
  '/css/mobile.css',
  '/js/main.js',
  '/js/home.js',
  '/js/shop.js',
  '/js/cart.js',
  '/manifest.json',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
];

// ── INSTALL: Pre-cache all static assets ─────────────────────
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(PRECACHE_ASSETS).catch((err) => {
        console.warn('[SW] Pre-cache partial failure:', err);
      });
    }).then(() => self.skipWaiting())
  );
});

// ── ACTIVATE: Clean up old caches ────────────────────────────
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME)
          .map((name) => caches.delete(name))
      );
    }).then(() => self.clients.claim())
  );
});

// ── FETCH: Handle all requests ───────────────────────────────
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET requests
  if (request.method !== 'GET') return;

  // Skip cross-origin requests (CDN fonts, etc.)
  if (url.origin !== location.origin) return;

  // API calls: Network-first, fallback to cache
  if (url.pathname.startsWith('/api/')) {
    event.respondWith(networkFirst(request));
    return;
  }

  // Static assets: Cache-first, update in background
  event.respondWith(cacheFirst(request));
});

// ── Cache-First Strategy ──────────────────────────────────────
async function cacheFirst(request) {
  const cached = await caches.match(request);
  if (cached) {
    // Update cache in background (stale-while-revalidate)
    fetch(request).then((response) => {
      if (response && response.status === 200) {
        caches.open(CACHE_NAME).then(cache => cache.put(request, response));
      }
    }).catch(() => {});
    return cached;
  }
  try {
    const response = await fetch(request);
    if (response && response.status === 200) {
      const cache = await caches.open(CACHE_NAME);
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    // Offline fallback for navigation requests
    if (request.mode === 'navigate') {
      return caches.match(OFFLINE_URL);
    }
    return new Response('Offline', { status: 503 });
  }
}

// ── Network-First Strategy ────────────────────────────────────
async function networkFirst(request) {
  try {
    const response = await fetch(request);
    if (response && response.status === 200) {
      const cache = await caches.open(CACHE_NAME);
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    const cached = await caches.match(request);
    return cached || new Response(JSON.stringify({ error: 'Offline' }), {
      status: 503,
      headers: { 'Content-Type': 'application/json' }
    });
  }
}
