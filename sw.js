/* sw.js - InoSakti PWA Service Worker (safe for PHP sites) */

const CACHE_NAME = "inosakti-v4";
const BASE = new URL(self.registration.scope).pathname.replace(/\/$/, "");

function withBase(path) {
  return `${BASE}${path}`;
}

// Precache minimal (jangan kebanyakan untuk PHP)
const PRECACHE = [
  withBase("/"),
  withBase("/index.php"),
  withBase("/assets/img/favicon-192.png"),
  withBase("/assets/img/favicon-512.png"),
];

// Halaman yang aman untuk dicache (public, tidak personal)
function isCacheablePage(url) {
  const p = url.pathname;

  return (
    (p === withBase("/") ||
      p === withBase("/index.php") ||
      p.startsWith(withBase("/pages/services/")) ||
      p.startsWith(withBase("/pages/subpages/"))) &&
    !p.startsWith(withBase("/pages/products/"))
  );
}

// Asset statis (boleh cache-first)
function isStaticAsset(url) {
  const p = url.pathname;
  return (
    p.startsWith(withBase("/assets/")) ||
    p.endsWith(".css") ||
    p.endsWith(".js") ||
    p.endsWith(".png") ||
    p.endsWith(".jpg") ||
    p.endsWith(".jpeg") ||
    p.endsWith(".webp") ||
    p.endsWith(".svg") ||
    p.endsWith(".ico") ||
    p.endsWith(".woff2") ||
    p.endsWith(".ttf")
  );
}

// Route yang jelas-jelas dinamis / sensitif (jangan dicache)
function isSensitive(url) {
  const p = url.pathname;
  return (
    p.includes("/login") ||
    p.includes("/logout") ||
    p.includes("/admin") ||
    p.includes("/dashboard") ||
    p.includes("/cart") ||
    p.includes("/checkout") ||
    p.includes("/api") ||
    p.includes("/inc/") ||
    p.includes("/upload") ||
    p.startsWith(withBase("/pages/products/")) // shop/product biasanya pakai query & berubah
  );
}

self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE))
  );
  self.skipWaiting();
});

self.addEventListener("activate", (event) => {
  event.waitUntil(
    (async () => {
      // Hapus cache lama
      const keys = await caches.keys();
      await Promise.all(
        keys
          .filter((k) => k.startsWith("inosakti-") && k !== CACHE_NAME)
          .map((k) => caches.delete(k))
      );

      await self.clients.claim();
    })()
  );
});

self.addEventListener("fetch", (event) => {
  const req = event.request;
  const url = new URL(req.url);

  // Hanya handle origin sendiri
  if (url.origin !== self.location.origin) return;

  // Jangan cache request selain GET
  if (req.method !== "GET") return;

  // Navigasi halaman (HTML)
  if (req.mode === "navigate") {
    // Jangan cache halaman sensitif/dinamis
    if (isSensitive(url) || !isCacheablePage(url)) {
      event.respondWith(
        fetch(req).catch(() => caches.match(withBase("/")))
      );
      return;
    }

    // Network-first untuk halaman public yang boleh dicache
    event.respondWith(
      fetch(req)
        .then((res) => {
          const copy = res.clone();
          caches.open(CACHE_NAME).then((c) => c.put(req, copy));
          return res;
        })
        .catch(() =>
          caches.match(req).then((r) => r || caches.match(withBase("/")))
        )
    );
    return;
  }

  // Asset statis: cache-first + populate cache
  if (isStaticAsset(url) && !isSensitive(url)) {
    event.respondWith(
      caches.match(req).then((cached) => {
        if (cached) return cached;

        return fetch(req).then((res) => {
          // kalau response bukan OK, jangan dicache
          if (!res || res.status !== 200) return res;

          const copy = res.clone();
          caches.open(CACHE_NAME).then((c) => c.put(req, copy));
          return res;
        });
      })
    );
    return;
  }

  // Default: network
  event.respondWith(fetch(req));
});
