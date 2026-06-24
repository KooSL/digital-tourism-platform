const chatToggle = document.getElementById("chatToggle");
const chatContainer = document.getElementById("chatContainer");
const closeChat = document.getElementById("closeChat");
const chatMessages = document.getElementById("chatMessages");

chatToggle.onclick = () => {
    chatContainer.style.display = "flex";

    if (!chatMessages.innerHTML) {
        addMessage("bot", "Namaste, How can I help you with?");
    }
};
closeChat.onclick = () => (chatContainer.style.display = "none");

document.getElementById("userInput").addEventListener("keypress", function (e) {
    if (e.key === "Enter") sendMessage();
});

function sendMessage() {
    const input = document.getElementById("userInput");
    const text = input.value.trim();
    if (!text) return;

    addMessage("user", text);
    input.value = "";

    fetch("api/chatbot", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "message=" + encodeURIComponent(text),
    })
        .then((res) => res.text())
        .then((reply) => addMessage("bot", reply));
}

function addMessage(type, text) {
    const msg = document.createElement("div");
    msg.className = `msg ${type}`;
    msg.innerText = text;

    chatMessages.appendChild(msg);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}
