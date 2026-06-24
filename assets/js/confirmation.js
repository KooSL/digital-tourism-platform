let confirmModal = document.getElementById("confirmModal");

let confirmBtn = document.getElementById("confirmBtn");
let cancelBtn = document.getElementById("cancelBtn");

let actionUrl = "";
let actionForm = null;

function showConfirm(target, message = "Are you sure?") {
  document.getElementById("confirmMessage").innerText = message;

  if (typeof target === "string") {
    actionUrl = target;
    actionForm = null;
  } else {
    actionForm = target;
    actionUrl = "";
  }

  confirmModal.style.display = "flex";
}

confirmBtn.onclick = function (e) {
  e.preventDefault();

  if (actionUrl !== "") {
    window.location.href = actionUrl;
  }

  if (actionForm !== null) {
    actionForm.requestSubmit();
  }

  confirmModal.style.display = "none";

  actionUrl = "";
  actionForm = null;
};

cancelBtn.onclick = function () {
  confirmModal.style.display = "none";

  actionUrl = "";
  actionForm = null;
};

window.onclick = function (e) {
  if (e.target === confirmModal) {
    confirmModal.style.display = "none";
  }
};
