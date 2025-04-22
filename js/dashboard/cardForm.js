/**
 * Card Form Component
 * Handles the add card popup and form submission
 */

document.addEventListener("DOMContentLoaded", function () {
  // Add Card Popup functionality
  const addCardBtn = document.getElementById("addCardBtn");
  const addCardPopup = document.getElementById("addCardPopup");
  const closeAddCardPopupBtns = addCardPopup
    ? addCardPopup.querySelectorAll(".close-popup, #cancelAddCardBtn")
    : [];
  const addCardForm = document.getElementById("addCardForm");

  if (addCardBtn && addCardPopup && addCardForm) {
    // Show popup when Add Card button is clicked
    addCardBtn.addEventListener("click", function () {
      // Reset the form before showing the popup
      addCardForm.reset();

      addCardPopup.classList.add("show");

      // Focus the first input for better UX
      setTimeout(() => {
        const firstInput = addCardPopup.querySelector("input, select");
        if (firstInput) firstInput.focus();
      }, 100);

      // Add ESC key support
      document.addEventListener("keydown", handleAddCardEscKey);
    });

    // Function to close popup
    function closeAddCardPopup() {
      addCardPopup.classList.remove("show");
      document.removeEventListener("keydown", handleAddCardEscKey);
    }

    // Close popup when X or Cancel is clicked
    closeAddCardPopupBtns.forEach((btn) => {
      btn.addEventListener("click", closeAddCardPopup);
    });

    // Close when clicking outside
    addCardPopup.addEventListener("click", function (e) {
      if (e.target === addCardPopup) {
        closeAddCardPopup();
      }
    });

    // ESC key handler
    function handleAddCardEscKey(e) {
      if (e.key === "Escape") {
        closeAddCardPopup();
      }
    }

    // Handle form submission via AJAX
    addCardForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // Create form data
      const formData = new FormData(this);

      // Send AJAX request
      fetch("index.php?path=card/store", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Reset the form
            this.reset();

            // Close the Add Card popup
            closeAddCardPopup();

            // Show success notification
            showNotification("Card added successfully!", "success");

            // Show the Card Details Modal with the bank's data
            showCardDetailsModal(data.bank_id, data.bank_name);
          } else {
            // Display error notification
            showNotification(
              data.error || "Failed to add card. Please try again.",
              "error"
            );
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          showNotification("An error occurred. Please try again.", "error");
        });
    });

    // Function to show the Card Details Modal for a specific bank
    function showCardDetailsModal(bankId, bankName) {
      const modal = document.getElementById("cardDetailsModal");
      const modalBankName = document.getElementById("modalBankName");
      const cardsTableContainer = document.getElementById(
        "cardsTableContainer"
      );

      if (modal && modalBankName) {
        // Display loading state
        modalBankName.textContent = bankName || "Loading...";
        cardsTableContainer.innerHTML =
          '<div class="loading">Loading cards...</div>';

        // Show modal with animation
        modal.classList.add("show");
        modal.style.display = "block";
        document.body.style.overflow = "hidden"; // Prevent scrolling behind modal

        // Add keyboard support for ESC key to close modal
        document.addEventListener("keydown", handleEscKey);

        // Fetch fresh data from the server
        fetch(`index.php?path=card/getBankWithCardsJson&bank_id=${bankId}`)
          .then((response) => response.json())
          .then((data) => {
            if (data.error) {
              cardsTableContainer.innerHTML = `<div class="error-message">${data.error}</div>`;
              return;
            }

            const bankData = data.bank;

            // Set bank name in modal header
            modalBankName.textContent = bankData.bank_name;

            // Generate the cards table
            let tableHTML = "";
            if (bankData.cards && bankData.cards.length > 0) {
              tableHTML = `
                                <table class="cards-table">
                                    <thead>
                                        <tr>
                                            <th>Card Name</th>
                                            <th>Association</th>
                                            <th>Chip Type</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
                                            <th>Expiration Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;

              bankData.cards.forEach((card) => {
                tableHTML += `
                                    <tr>
                                        <td>${card.card_name}</td>
                                        <td>${card.association}</td>
                                        <td>${card.chip_type}</td>
                                        <td>${card.card_type}</td>
                                        <td class="card-quantity">${card.card_quantity}</td>
                                        <td class="card-expiry">${card.expired_at}</td>
                                        <td class="action-buttons">
                                            <button class="action-btn view" title="View Details" data-card-id="${card.card_id}">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <button class="action-btn report" title="View Transactions" data-card-id="${card.card_id}">
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `;
              });

              tableHTML += `
                                    </tbody>
                                </table>
                            `;
            } else {
              tableHTML =
                '<div class="no-cards-message">No cards available for this bank.</div>';
            }

            cardsTableContainer.innerHTML = tableHTML;

            // Add event listeners to view detail buttons
            const viewButtons =
              cardsTableContainer.querySelectorAll(".action-btn.view");
            viewButtons.forEach((button) => {
              button.addEventListener("click", function (e) {
                e.preventDefault();
                const cardId = this.getAttribute("data-card-id");
                showCardDetails(cardId, bankData);
              });
            });

            // Add event listeners to view transactions buttons
            const transButtons =
              cardsTableContainer.querySelectorAll(".action-btn.report");
            transButtons.forEach((button) => {
              button.addEventListener("click", function (e) {
                e.preventDefault();
                const cardId = this.getAttribute("data-card-id");
                fetchAndShowTransactions(cardId, bankData);
              });
            });
          })
          .catch((error) => {
            console.error("Error:", error);
            cardsTableContainer.innerHTML = `
                            <div class="error-message">
                                <p>Failed to load cards. Please try again later.</p>
                            </div>
                        `;
          });
      }
    }

    // Function to handle ESC key press for the modal
    function handleEscKey(event) {
      if (event.key === "Escape") {
        const modal = document.getElementById("cardDetailsModal");
        if (modal) {
          modal.classList.remove("show");
          setTimeout(() => {
            modal.style.display = "none";
            document.body.style.overflow = ""; // Re-enable scrolling
            document.removeEventListener("keydown", handleEscKey);
          }, 300);
        }
      }
    }
  }
});
