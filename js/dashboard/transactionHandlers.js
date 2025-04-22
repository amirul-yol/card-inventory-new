/**
 * Transaction Handlers Component
 * Handles deposit and edit transaction popups/forms
 */

document.addEventListener("DOMContentLoaded", function () {
  // Function to show edit transaction popup
  window.showEditTransactionPopup = function (transactionId, quantity, cardId) {
    const editTransactionPopup = document.getElementById(
      "editTransactionPopup"
    );
    const editTransactionForm = document.getElementById("editTransactionForm");
    const transactionIdInput = document.getElementById("edit_transaction_id");
    const cardIdInput = document.getElementById("edit_card_id");
    const quantityInput = document.getElementById("edit_quantity");
    const closePopupBtn = editTransactionPopup.querySelector(".close-popup");
    const cancelBtn = document.getElementById("cancelEditTransactionBtn");

    // Set form values
    transactionIdInput.value = transactionId;
    cardIdInput.value = cardId;
    quantityInput.value = quantity;

    // Show popup
    editTransactionPopup.classList.add("show");

    // Focus quantity input for better UX
    setTimeout(() => {
      quantityInput.focus();
      quantityInput.select(); // Select the text for easy editing
    }, 100);

    // Function to close popup
    function closeEditTransactionPopup() {
      editTransactionPopup.classList.remove("show");
      document.removeEventListener("keydown", handleEditTransactionEscKey);
    }

    // Close popup events
    closePopupBtn.addEventListener("click", closeEditTransactionPopup);
    if (cancelBtn) {
      cancelBtn.addEventListener("click", closeEditTransactionPopup);
    }

    // Also close when clicking outside
    editTransactionPopup.addEventListener("click", function (e) {
      if (e.target === editTransactionPopup) {
        closeEditTransactionPopup();
      }
    });

    // Add ESC key support
    function handleEditTransactionEscKey(e) {
      if (e.key === "Escape") {
        closeEditTransactionPopup();
      }
    }

    document.addEventListener("keydown", handleEditTransactionEscKey);
  };

  // Function to show deposit popup
  window.showDepositPopup = function (cardId) {
    const depositPopup = document.getElementById("depositCardPopup");
    const depositCardIdInput = document.getElementById("deposit_card_id");
    const quantityInput = document.getElementById("quantity");
    const closePopupBtn = document.querySelector(".close-popup");
    const cancelBtn = document.getElementById("cancelDepositBtn");
    const cardTransactionsContainer = document.getElementById(
      "cardTransactionsContainer"
    );

    // Set the card ID in the form
    depositCardIdInput.value = cardId;

    // Reset quantity input
    quantityInput.value = "";

    // Show popup
    depositPopup.classList.add("show");

    // Focus quantity input for better UX
    setTimeout(() => {
      quantityInput.focus();
    }, 100);

    // Function to close popup
    function closeDepositPopup() {
      depositPopup.classList.remove("show");
      document.removeEventListener("keydown", handlePopupEscKey);
    }

    // Close popup events
    closePopupBtn.addEventListener("click", closeDepositPopup);
    cancelBtn.addEventListener("click", closeDepositPopup);

    // Also close when clicking outside
    depositPopup.addEventListener("click", function (e) {
      if (e.target === depositPopup) {
        closeDepositPopup();
      }
    });

    // Add ESC key support
    function handlePopupEscKey(e) {
      if (e.key === "Escape") {
        closeDepositPopup();
      }
    }

    document.addEventListener("keydown", handlePopupEscKey);

    // Handle form submission via AJAX
    const depositForm = document.getElementById("depositCardForm");
    depositForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // Create form data
      const formData = new FormData(this);
      const currentCardId = depositCardIdInput.value;

      // Send AJAX request
      fetch("index.php?path=card/processDepositCard", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Close the deposit popup
            closeDepositPopup();

            // Show success notification
            showNotification(
              `Successfully deposited ${data.quantity} cards`,
              "success"
            );

            // Refresh transactions view if it's open
            if (
              cardTransactionsContainer &&
              cardTransactionsContainer.style.display === "block"
            ) {
              fetchAndShowTransactions(currentCardId, null);
            }
          } else {
            // Display error notification
            showNotification(
              data.error || "Failed to deposit card. Please try again.",
              "error"
            );
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          showNotification("An error occurred. Please try again.", "error");
        });
    });
  };
});
