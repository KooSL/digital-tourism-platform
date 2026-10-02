const chatToggle = document.getElementById("chatToggle");
const chatContainer = document.getElementById("chatContainer");
const closeChat = document.getElementById("closeChat");
const chatMessages = document.getElementById("chatMessages");

// Predefined questions and fixed answers (no DB needed)
const quickQuestions = [
  {
    question: "How can I book a bus ticket?",
    answer:
      "You can book a ticket by choosing your route and date on the home page, selecting your seat, and completing the payment.",
  },
  {
    question: "What trips are available?",
    answer:
      "We offer several trips and routes. Please visit the Trips page to see current options, dates and prices.",
  },
  {
    question: "How can I contact support?",
    answer:
      "You can reach our support team through the Contact page, or call us during office hours.",
  },
];

function showQuickQuestions() {
  const wrapper = document.createElement("div");
  wrapper.className = "quick-questions";
  wrapper.id = "quickQuestions";

  quickQuestions.forEach((item) => {
    const btn = document.createElement("button");
    btn.className = "quick-question-btn";
    btn.type = "button";
    btn.textContent = item.question;

    btn.onclick = () => {
      removeQuickQuestions();
      addMessage("user", item.question);

      // small delay so it feels natural
      showTypingIndicator();
      setTimeout(() => {
        removeTypingIndicator();
        addMessage("bot", item.answer);
      }, 600);
    };

    wrapper.appendChild(btn);
  });

  chatMessages.appendChild(wrapper);
  chatMessages.scrollTop = chatMessages.scrollHeight;
}

function removeQuickQuestions() {
  const el = document.getElementById("quickQuestions");
  if (el) el.remove();
}

chatToggle.onclick = () => {
  chatContainer.style.display = "flex";
  chatToggle.classList.add("active");

  // Disable background scrolling and touch gestures on mobile
  if (window.innerWidth <= 768) {
    document.body.classList.add("chatbot-open");
  }

  if (!chatMessages.innerHTML) {
    addMessage("bot", "Namaste 🙏, How can I help you with? 😊");
    showQuickQuestions();
  }
};

closeChat.onclick = () => {
  chatContainer.style.display = "none";
  chatToggle.classList.remove("active");

  // Re-enable background scrolling and touch gestures
  document.body.classList.remove("chatbot-open");
};

document.getElementById("userInput").addEventListener("keypress", function (e) {
  if (e.key === "Enter") {
    sendMessage();
  }
});

function sendMessage() {
  const input = document.getElementById("userInput");
  const text = input.value.trim();

  if (!text) {
    return;
  }

  removeQuickQuestions();
  addMessage("user", text);

  input.value = "";

  showTypingIndicator();

  /*
    |--------------------------------------------------------------------------
    | Stop waiting after 25 seconds
    |--------------------------------------------------------------------------
    */

  const controller = new AbortController();

  const timeout = setTimeout(() => {
    controller.abort();
  }, 25000);

  fetch("api/cbassistant", {
    method: "POST",

    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },

    body: "message=" + encodeURIComponent(text),

    signal: controller.signal,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("HTTP " + response.status);
      }

      return response.text();
    })

    .then((reply) => {
      clearTimeout(timeout);

      removeTypingIndicator();

      addMessage("bot", reply);
    })

    .catch((error) => {
      clearTimeout(timeout);

      removeTypingIndicator();

      if (error.name === "AbortError") {
        addMessage("bot", "The response is taking too long. Please try again.");
      } else {
        addMessage("bot", "Sorry, something went wrong. Please try again.");
      }
    });
}

function showTypingIndicator() {
  const typing = document.createElement("div");

  typing.className = "typing-indicator";

  typing.id = "typingIndicator";

  typing.innerHTML = "<span></span><span></span><span></span>";

  chatMessages.appendChild(typing);

  chatMessages.scrollTop = chatMessages.scrollHeight;
}

function removeTypingIndicator() {
  const typing = document.getElementById("typingIndicator");

  if (typing) {
    typing.remove();
  }
}

function addMessage(type, text) {
  const msg = document.createElement("div");

  msg.className = `msg ${type}`;

  msg.innerText = text;

  chatMessages.appendChild(msg);

  chatMessages.scrollTop = chatMessages.scrollHeight;
}
