const CACHE_NAME = 'bimmo-v1';
const ASSETS_TO_CACHE = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/img/bimmo_favicon.png',
    '/img/bimmo.png'
];

// Install Service Worker
self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('PWA Debug: Service Worker caching assets');
            return Promise.allSettled(
                ASSETS_TO_CACHE.map(url => {
                    return cache.add(url).catch(err => {
                        console.warn(`PWA Debug: Failed to cache ${url}:`, err);
                    });
                })
            );
        })
    );
});

// Activate Service Worker
self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        console.log('PWA Debug: Service Worker clearing old cache');
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
});

// Fetch events (Required for PWA)
self.addEventListener('fetch', (event) => {
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});
