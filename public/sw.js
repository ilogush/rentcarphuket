const CACHE_NAME = 'rentcar-phuket-static-v5';
const STATIC_ASSETS = [
  '/assets/css/tailwind.min.css',
  '/assets/css/styles.min.css',
  '/assets/css/toast.min.css',
  '/assets/css/monkey-theme.css',
  '/assets/js/calculator.min.js',
  '/assets/images/monkey-logo.webp',
  '/assets/images/placeholder.webp',
  '/favicon-32x32.png',
  '/android-chrome-192x192.png'
];

const isSameOrigin = (url) => url.origin === self.location.origin;

const isStaticAsset = (url) => {
  if (!isSameOrigin(url)) return false;

  return (
    url.pathname.startsWith('/assets/') ||
    url.pathname === '/manifest.json' ||
    url.pathname === '/favicon.ico' ||
    url.pathname === '/favicon-32x32.png' ||
    url.pathname === '/apple-touch-icon.png' ||
    url.pathname.startsWith('/android-chrome-')
  );
};

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(STATIC_ASSETS))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then((cacheNames) => Promise.all(
        cacheNames
          .filter((cacheName) => cacheName !== CACHE_NAME)
          .map((cacheName) => caches.delete(cacheName))
      ))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const { request } = event;

  if (request.method !== 'GET') return;

  const url = new URL(request.url);
  if (!isStaticAsset(url) || url.pathname.startsWith('/admin') || url.pathname.startsWith('/api/')) {
    return;
  }

  event.respondWith(
    caches.match(request)
      .then((cachedResponse) => {
        if (cachedResponse) return cachedResponse;

        return fetch(request).then((networkResponse) => {
          if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
            return networkResponse;
          }

          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(request, responseToCache));

          return networkResponse;
        });
      })
  );
});
