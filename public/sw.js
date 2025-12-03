self.addEventListener("install", (event) => {
    self.skipWaiting(); // Langsung aktifkan
});

self.addEventListener("activate", (event) => {
    event.waitUntil(clients.claim());
});

// Handle saat notifikasi diklik
self.addEventListener("notificationclick", (event) => {
    event.notification.close(); // Tutup notif

    const urlToOpen = event.notification.data
        ? event.notification.data.url
        : "/";

    event.waitUntil(
        clients
            .matchAll({ type: "window", includeUncontrolled: true })
            .then((windowClients) => {
                // Jika ada tab yang terbuka, fokuskan
                for (let i = 0; i < windowClients.length; i++) {
                    const client = windowClients[i];
                    if (client.url === urlToOpen && "focus" in client) {
                        return client.focus();
                    }
                }
                // Jika tidak ada, buka tab baru
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});
