let startTime = Date.now();

function sendTime() {
    let timeSpent = Math.floor((Date.now() - startTime) / 1000);

    if (timeSpent < 2) return;

    navigator.sendBeacon("api/track-time", JSON.stringify({
        package_id: currentTripId,
        time_spent: timeSpent
    }));
}

document.addEventListener("visibilitychange", () => {
    if (document.visibilityState === "hidden") {
        sendTime();
    }
});
