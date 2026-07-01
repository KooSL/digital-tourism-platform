const firebaseConfig = {
  apiKey: "AIzaSyBEf-lc9cvMTEvidO_NmvkAfRRcWBzK17w",
  authDomain: "digitaltourismplatform-01.firebaseapp.com",
  projectId: "digitaltourismplatform-01",
  storageBucket: "digitaltourismplatform-01.firebasestorage.app",
  messagingSenderId: "73754956727",
  appId: "1:73754956727:web:821f7d4525735d17bd8e32",
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

const VAPID_KEY =
  "BCuwDW11Wf7vwWdgiX5Cv3rF6BTKz3_KmjN6YiZIdgTV4TBOafHFXI0jMrGs9mm9SU6mWTiS-vmwz2Lezt2g7Y4";

function requestNotificationPermission() {
  Notification.requestPermission().then((permission) => {
    if (permission === "granted") {
      console.log("Notification permission granted.");
      getAdminToken();
    } else {
      console.warn("Notification permission denied.");
    }
  });
}

function getAdminToken() {
  navigator.serviceWorker
    .register("../firebase-messaging-sw.js")
    .then((registration) => {
      messaging
        .getToken({
          vapidKey: VAPID_KEY,
          serviceWorkerRegistration: registration,
        })
        .then((token) => {
          if (token) {
            // console.log("FCM Token:", token);
            saveTokenToServer(token);
          } else {
            console.warn("No token. Request permission first.");
          }
        })
        .catch((err) => console.error("Error getting token:", err));
    })
    .catch((err) => console.error("SW registration failed:", err));
}

function saveTokenToServer(token) {
  fetch("api/save-fcm-token", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ token: token }),
  })
    .then((res) => res.json())
    .then((data) => console.log("Token saved:", data))
    .catch((err) => console.error("Error saving token:", err));
}

// Handle foreground notifications (when admin tab is open)
messaging.onMessage((payload) => {
  console.log("Foreground message:", payload);
  showToastNotification(payload.notification.title, payload.notification.body);
});

// Simple toast notification for foreground
function showToastNotification(title, body) {
  const toast = document.createElement("div");
  toast.style.cssText = `
        position: absolute; top: 70px; right: 30px;
        background: var(--success-green); color: #fff; padding: 12px 14px;
        border-radius: 6px; font-size: 14px; animation: fadeIn 0.4s ease;
        cursor: pointer; font-weight: 500; border-left: 4px solid var(--orange);
    `;
  toast.innerHTML = `<strong>${title}</strong><br><small>${body}</small>`;
  toast.onclick = () => toast.remove();
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 6000);
}

// Auto-request permission when admin loads the page
document.addEventListener("DOMContentLoaded", () => {
  if ("Notification" in window && "serviceWorker" in navigator) {
    requestNotificationPermission();
  }
});
