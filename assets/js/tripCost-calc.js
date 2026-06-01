// const pricePerPerson = pricePerPerson;

const personsInput = document.getElementById("persons");
const discountText = document.getElementById("discountText");
const totalAmount = document.getElementById("totalAmount");

function calculateTotal() {
  let persons = parseInt(personsInput.value) || 1;

  let subtotal = pricePerPerson * persons;

  let discountRate = 0;

  if (persons >= 10 && persons <= 15) {
    discountRate = 20;
  } else if (persons >= 5 && persons < 10) {
    discountRate = 10;
  }

  let discountAmount = subtotal * (discountRate / 100);
  let finalAmount = subtotal - discountAmount;

  discountText.textContent = `${discountRate}% (NPR ${discountAmount.toLocaleString()})`;

  totalAmount.textContent = finalAmount.toLocaleString();
}

personsInput.addEventListener("input", calculateTotal);

calculateTotal();
