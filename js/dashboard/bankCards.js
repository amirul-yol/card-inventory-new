/**
 * Bank Cards Component
 * Handles the expand/collapse functionality for bank cards
 */

document.addEventListener("DOMContentLoaded", function () {
  // Bank cards expand/collapse functionality
  const bankCards = document.querySelectorAll(".bank-card");
  bankCards.forEach((card) => {
    card.addEventListener("click", function () {
      const bankId = this.getAttribute("data-bank-id");
      const table = document.getElementById("bank-" + bankId);
      const icon = this.querySelector(".expand-icon");

      if (table.style.display === "none" || table.style.display === "") {
        table.style.display = "block";
        icon.classList.add("open");
      } else {
        table.style.display = "none";
        icon.classList.remove("open");
      }
    });
  });
});
