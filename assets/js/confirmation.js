let confirmModal = document.getElementById("confirmModal");

let confirmBtn = document.getElementById("confirmBtn");

let cancelBtn = document.getElementById("cancelBtn");

function showConfirm(url, message = "Are you sure?") {
  document.getElementById("confirmMessage").innerText = message;

  confirmBtn.href = url;

  confirmModal.style.display = "flex";
}

cancelBtn.onclick = function () {
  confirmModal.style.display = "none";
};

window.onclick = function (e) {
  if (e.target == confirmModal) {
    confirmModal.style.display = "none";
  }
};
