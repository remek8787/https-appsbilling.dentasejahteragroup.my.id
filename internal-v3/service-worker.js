const CACHE_NAME = 'ebilling-dsg-v3-20260719-network-infrastructure';
const ASSET_VERSION = '20260713-ebilling-dsg-brand';
const APP_SHELL = [
  '/v3/offline.php',
  '/v3/manifest-pwa-receipt-d-20260715.json',
  '/v3/assets/adminlte-clone.css?v=20260719-network-infrastructure',
  '/v3/assets/v3-ajax.js?v=20260719-network-infrastructure',
  '/v3/assets/dentanet-logo-20260715.jpg',
  '/v3/assets/denta-net-logo.jpg',
  '/v3/assets/pwa-dentanet-receipt-d-20260715-512.png',
  '/v3/assets/pwa-icon.png',
  '/v3/assets/pwa-dentanet-receipt-d-20260715-192.png',
  '/v3/assets/pwa-icon-192.png',
  '/v3/assets/apple-dentanet-receipt-d-20260715.png',
  '/v3/assets/apple-touch-icon.png',
  '/v3/assets/pwa-icon.svg'
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
      .then(keys => Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') self.skipWaiting();
});

self.addEventListener('fetch', event => {
  const req = event.request;
  if (req.method !== 'GET') return;

  const url = new URL(req.url);
  if (url.origin !== location.origin || !url.pathname.startsWith('/v3/')) return;

  const accepts = req.headers.get('accept') || '';
  const isNavigation = req.mode === 'navigate' || accepts.includes('text/html');
  const isCssOrJs = req.destination === 'style' || req.destination === 'script' || url.pathname.endsWith('.css') || url.pathname.endsWith('.js');

  // HTML/PHP pages must always be network-first so table markup/layout changes appear immediately.
  if (isNavigation || url.pathname.endsWith('.php') || url.pathname === '/v3/' || url.pathname === '/v3/index.php') {
    event.respondWith(fetch(req).catch(() => caches.match('/v3/offline.php')));
    return;
  }

  // CSS/JS are network-first with cache fallback. This prevents the Ctrl+F5-only layout bug.
  if (isCssOrJs) {
    event.respondWith(
      fetch(req, { cache: 'no-store' })
        .then(res => {
          if (res.ok) {
            const copy = res.clone();
            caches.open(CACHE_NAME).then(cache => cache.put(req, copy));
          }
          return res;
        })
        .catch(() => caches.match(req))
    );
    return;
  }

  // Static images/fonts/manifest may stay cache-first for speed.
  event.respondWith(
    caches.match(req).then(cached => cached || fetch(req).then(res => {
      if (res.ok && ['image','font','manifest'].includes(req.destination)) {
        const copy = res.clone();
        caches.open(CACHE_NAME).then(cache => cache.put(req, copy));
      }
      return res;
    }).catch(() => cached))
  );
});
