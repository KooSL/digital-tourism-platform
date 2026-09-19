const personsInput = document.getElementById("persons");
const discountText = document.getElementById("discountText");
const totalAmount = document.getElementById("totalAmount");

const fullAmountText = document.getElementById("fullAmountText");
const depositAmountText = document.getElementById("depositAmountText");
const remainingAmountText = document.getElementById("remainingAmountText");

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

  // Full-vs-deposit display. This is for the user's benefit only - the
  // actual amount charged is always recalculated server-side from the
  // real tour price before anything is sent to eSewa, so a manipulated
  // value here can't change what gets billed.
  let depositAmount = finalAmount * 0.1;
  let remainingAmount = finalAmount - depositAmount;

  if (fullAmountText) fullAmountText.textContent = finalAmount.toLocaleString();
  if (depositAmountText)
    depositAmountText.textContent = depositAmount.toLocaleString(undefined, {
      maximumFractionDigits: 2,
    });
  if (remainingAmountText)
    remainingAmountText.textContent = remainingAmount.toLocaleString(
      undefined,
      { maximumFractionDigits: 2 },
    );
}

personsInput.addEventListener("input", calculateTotal);

calculateTotal();
