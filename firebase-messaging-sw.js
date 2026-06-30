importScripts(
  "https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js",
);
importScripts(
  "https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging-compat.js",
);

firebase.initializeApp({
  apiKey: "AIzaSyBEf-lc9cvMTEvidO_NmvkAfRRcWBzK17w",
  authDomain: "digitaltourismplatform-01.firebaseapp.com",
  projectId: "digitaltourismplatform-01",
  storageBucket: "digitaltourismplatform-01.firebasestorage.app",
  messagingSenderId: "73754956727",
  appId: "1:73754956727:web:821f7d4525735d17bd8e32",
});

const messaging = firebase.messaging();

// Handle background notifications
messaging.onBackgroundMessage(function (payload) {
  console.log("Background message received:", payload);

  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: "/assets/images/logo.png", // your logo path
    badge: "/assets/images/badge.png",
    data: payload.data,
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click
self.addEventListener("notificationclick", function (event) {
  event.notification.close();
  const url = event.notification.data?.url || "/admin/dashboard";
  event.waitUntil(clients.openWindow(url));
});
