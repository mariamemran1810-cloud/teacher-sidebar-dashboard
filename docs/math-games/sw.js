/* Service Worker — ألعاب الرياضيات (تعمل بدون إنترنت)
   عند تعديل أي ملف: ارفع رقم CACHE لتجدد النسخة المخزّنة لدى الزوار */
const CACHE = 'math-games-v2';

const ASSETS = [
  './',
  './index.html',
  './css/style.css',
  './js/helpers.js',
  './js/curriculum.js',
  './js/games.js',
  './js/app.js',
  './js/data/g1.js',
  './js/data/g2.js',
  './js/data/g3.js',
  './manifest.json',
  './icons/icon-192.png',
  './icons/icon-512.png',
  './icons/icon-maskable-512.png',
  './icons/apple-touch-icon.png',
  './icons/favicon-48.png'
];

self.addEventListener('install', (e) => {
  self.skipWaiting();
  e.waitUntil(caches.open(CACHE).then((c) => Promise.allSettled(ASSETS.map((a) => c.add(a)))));
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys()
      .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (e) => {
  const req = e.request;
  if (req.method !== 'GET') return;
  const url = new URL(req.url);

  if (url.origin === location.origin) {
    e.respondWith(
      caches.match(req).then((hit) => {
        if (hit) return hit;
        return fetch(req).then((res) => {
          if (res && res.ok) {
            const copy = res.clone();
            caches.open(CACHE).then((c) => c.put(req, copy));
          }
          return res;
        }).catch(() => (req.mode === 'navigate' ? caches.match('./index.html') : Response.error()));
      })
    );
    return;
  }

  // نطاق خارجي (الخطوط): الشبكة أولاً، والذاكرة المؤقتة كاحتياط
  e.respondWith(
    fetch(req).then((res) => {
      if (res && (res.type === 'basic' || res.type === 'cors')) {
        const copy = res.clone();
        caches.open(CACHE).then((c) => c.put(req, copy));
      }
      return res;
    }).catch(() => caches.match(req))
  );
});
