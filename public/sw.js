// Service Worker básico para Web Push
self.addEventListener('push', function(event) {
  try {
    const data = event.data ? event.data.json() : {};
    const title = data.title || 'Fiscalizer';
    const options = {
      body: data.body || '',
      icon: '/icons/icon-192x192.png',
      data: { url: (data.action || '/') }
    };
    event.waitUntil(self.registration.showNotification(title, options));
  } catch (e) {
    // Fallback simples
    event.waitUntil(self.registration.showNotification('Fiscalizer', { body: 'Nova notificação.' }));
  }
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  const url = (event.notification && event.notification.data && event.notification.data.url) || '/';
  event.waitUntil(clients.openWindow(url));
});