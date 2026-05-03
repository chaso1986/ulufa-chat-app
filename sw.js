const CACHE_NAME = "chat-app-v1";

self.addEventListener("install", e => {
  e.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll([
        "/chat-app/",
        "/chat-app/chat/index.php",
        "/chat-app/assets/css/style.css",
        "/chat-app/assets/js/chat.js"
      ]);
    })
  );
});

self.addEventListener("fetch", e => {
  e.respondWith(
    caches.match(e.request).then(response => {
      return response || fetch(e.request);
    })
  );
});