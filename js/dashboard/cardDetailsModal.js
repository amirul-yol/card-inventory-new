/**
 * Card Details Modal Component
 * Handles the card details modal functionality, including:
 * - Opening and closing the modal
 * - Showing card details
 * - Showing card transactions
 * - Handling deposit and edit transaction functionality
 */

document.addEventListener("DOMContentLoaded", function () {
  // Modal functionality for bank cards
  const bankCardItems = document.querySelectorAll(".bank-card-item");
  const modal = document.getElementById("cardDetailsModal");
  const closeModal = document.querySelector(".close-modal");
  const modalBankName = document.getElementById("modalBankName");
  const cardsTableContainer = document.getElementById("cardsTableContainer");

  // Variables for handling ESC key
  let handleEscKey;

  if (bankCardItems.length && modal) {
    // Show modal when bank card is clicked
    bankCardItems.forEach((card) => {
      card.addEventListener("click", function () {
        const bankId = this.getAttribute("data-bank-id");
        const bankData = banksWithCards[bankId];

        if (bankData) {
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

          // Show modal with animation
          modal.classList.add("show");
          modal.style.display = "block";
          document.body.style.overflow = "hidden"; // Prevent scrolling behind modal

          // Add keyboard support for ESC key to close modal
          document.addEventListener("keydown", handleEscKey);
        }
      });
    });

    // Define ESC key handler
    handleEscKey = function (event) {
      if (event.key === "Escape") {
        closeCardModal();
      }
    };

    // Close modal when X is clicked
    if (closeModal) {
      closeModal.addEventListener("click", closeCardModal);
    }

    // Close modal when clicking outside the modal content
    window.addEventListener("click", function (event) {
      if (event.target === modal) {
        closeCardModal();
      }
    });

    // Function to close modal
    function closeCardModal() {
      modal.classList.remove("show");
      setTimeout(() => {
        modal.style.display = "none";
        document.body.style.overflow = ""; // Re-enable scrolling
        // Remove keyboard listener when modal is closed
        document.removeEventListener("keydown", handleEscKey);
        // Reset to cards table view when modal is closed
        switchToCardsTable();
      }, 300); // Wait for the transition to complete
    }

    // Function to show card details
    function showCardDetails(cardId, bankData) {
      const cardsTableContainer = document.getElementById(
        "cardsTableContainer"
      );
      const cardDetailContainer = document.getElementById(
        "cardDetailContainer"
      );
      const cardDetailContent = cardDetailContainer.querySelector(
        ".card-detail-content"
      );

      // Find the card data
      let cardData = null;
      if (bankData && bankData.cards) {
        bankData.cards.forEach((card) => {
          if (card.card_id == cardId) {
            cardData = card;
          }
        });
      }

      if (!cardData) {
        return;
      }

      // Create card details HTML
      const cardDetailsHTML = `
                <div class="card-details">
                    <div class="card-title">
                        <div class="card-title-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h3>${cardData.card_name}</h3>
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Card ID</div>
                            <div class="detail-value">#${cardData.card_id}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Bank</div>
                            <div class="detail-value">${bankData.bank_name}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Association</div>
                            <div class="detail-value">${cardData.association}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Chip Type</div>
                            <div class="detail-value">${cardData.chip_type}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Card Type</div>
                            <div class="detail-value">${cardData.card_type}</div>
                        </div>
                        <div class="detail-item highlight">
                            <div class="detail-label">Quantity</div>
                            <div class="detail-value">${cardData.card_quantity}</div>
                        </div>
                        <div class="detail-item secondary">
                            <div class="detail-label">Expiration Date</div>
                            <div class="detail-value">${cardData.expired_at}</div>
                        </div>
                    </div>
                </div>
            `;

      // Insert the HTML
      cardDetailContent.innerHTML = cardDetailsHTML;

      // Switch views
      cardsTableContainer.style.display = "none";
      cardDetailContainer.style.display = "block";

      // Setup back button
      const backBtn = document.getElementById("backToTableBtn");
      if (backBtn) {
        backBtn.addEventListener("click", switchToCardsTable);
      }
    }

    // Function to switch back to cards table
    function switchToCardsTable() {
      const cardsTableContainer = document.getElementById(
        "cardsTableContainer"
      );
      const cardDetailContainer = document.getElementById(
        "cardDetailContainer"
      );
      const cardTransactionsContainer = document.getElementById(
        "cardTransactionsContainer"
      );

      if (
        cardsTableContainer &&
        cardDetailContainer &&
        cardTransactionsContainer
      ) {
        cardsTableContainer.style.display = "block";
        cardDetailContainer.style.display = "none";
        cardTransactionsContainer.style.display = "none";
      }
    }

    // Function to fetch and show transactions
    function fetchAndShowTransactions(cardId, bankData) {
      const cardsTableContainer = document.getElementById(
        "cardsTableContainer"
      );
      const cardTransactionsContainer = document.getElementById(
        "cardTransactionsContainer"
      );
      const transactionsContent = cardTransactionsContainer.querySelector(
        ".transactions-content"
      );

      // Display a loading state
      transactionsContent.innerHTML =
        '<div class="loading">Loading transactions...</div>';

      // Show the transactions container while loading
      cardsTableContainer.style.display = "none";
      cardTransactionsContainer.style.display = "block";

      // Setup back button
      const backBtn = document.getElementById("backToTableFromTransBtn");
      if (backBtn) {
        backBtn.addEventListener("click", switchToCardsTable);
      }

      // Fetch transaction data from the server
      fetch(`index.php?path=card/getTransactionsJson&card_id=${cardId}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            transactionsContent.innerHTML = `<div class="error-message">${data.error}</div>`;
            return;
          }

          const card = data.card;
          const transactions = data.transactions;

          // Create transactions HTML
          let transactionsHTML = `
                        <div class="transactions-header">
                            <div class="card-info">
                                <div class="card-info-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="card-info-text">
                                    <h3>${card.name}</h3>
                                    <p>Transaction History</p>
                                </div>
                            </div>
                            <button class="deposit-btn" data-card-id="${card.id}">
                                <i class="fas fa-plus-circle"></i> Deposit Card
                            </button>
                        </div>
                    `;

          if (transactions.length > 0) {
            transactionsHTML += `
                            <table class="transactions-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Quantity</th>
                                        <th>Date</th>
                                        <th>Remarks</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

            transactions.forEach((transaction) => {
              transactionsHTML += `
                                <tr>
                                    <td>#${transaction.id}</td>
                                    <td class="transaction-quantity">${transaction.quantity}</td>
                                    <td class="transaction-date">${transaction.transaction_date}</td>
                                    <td class="transaction-remarks">${transaction.remarks}</td>
                                    <td>
                                        <button class="edit-transaction-btn" data-transaction-id="${transaction.id}" data-quantity="${transaction.quantity}">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            `;
            });

            transactionsHTML += `
                                </tbody>
                            </table>
                        `;
          } else {
            transactionsHTML += `
                            <div class="no-transactions">
                                <p>No transactions found for this card.</p>
                            </div>
                        `;
          }

          // Insert the HTML
          transactionsContent.innerHTML = transactionsHTML;

          // Add event listener to deposit button
          const depositBtn = transactionsContent.querySelector(".deposit-btn");
          if (depositBtn) {
            depositBtn.addEventListener("click", function (e) {
              e.preventDefault();
              const cardId = this.getAttribute("data-card-id");
              showDepositPopup(cardId);
            });
          }

          // Add event listeners to edit transaction buttons
          const editTransactionBtns = transactionsContent.querySelectorAll(
            ".edit-transaction-btn"
          );
          if (editTransactionBtns.length > 0) {
            editTransactionBtns.forEach((btn) => {
              btn.addEventListener("click", function (e) {
                e.preventDefault();
                const transactionId = this.getAttribute("data-transaction-id");
                const quantity = this.getAttribute("data-quantity");
                showEditTransactionPopup(transactionId, quantity, card.id);
              });
            });
          }
        })
        .catch((error) => {
          console.error("Error fetching transactions:", error);
          transactionsContent.innerHTML = `
                        <div class="error-message">
                            <p>Failed to load transactions. Please try again later.</p>
                        </div>
                    `;
        });
    }
  }
});
