/**
 * Bank Management Component
 * Handles bank-related functionality including:
 * - View All Banks popup
 * - Add Bank popup
 */

document.addEventListener("DOMContentLoaded", function () {
  // View All Banks Popup functionality
  const viewAllBanksBtn = document.getElementById("viewAllBanksBtn");
  const allBanksPopup = document.getElementById("allBanksPopup");
  const closeAllBanksPopupBtns = allBanksPopup
    ? allBanksPopup.querySelectorAll(".close-popup")
    : [];

  // Add Bank Popup functionality
  const showAddBankBtn = document.getElementById("showAddBankBtn");
  const addBankPopup = document.getElementById("addBankPopup");
  const closeAddBankPopupBtns = addBankPopup
    ? addBankPopup.querySelectorAll(".close-popup, #cancelAddBankBtn")
    : [];
  const backToBanksBtn = document.getElementById("backToBanksBtn");

  if (viewAllBanksBtn && allBanksPopup) {
    // Show popup when View All Banks button is clicked
    viewAllBanksBtn.addEventListener("click", function (e) {
      e.preventDefault();
      allBanksPopup.classList.add("show");

      // Add ESC key support
      document.addEventListener("keydown", handleAllBanksEscKey);
    });

    // Function to close popup
    function closeAllBanksPopup() {
      allBanksPopup.classList.remove("show");
      document.removeEventListener("keydown", handleAllBanksEscKey);
    }

    // Close popup when X is clicked
    closeAllBanksPopupBtns.forEach((btn) => {
      btn.addEventListener("click", closeAllBanksPopup);
    });

    // Close when clicking outside
    allBanksPopup.addEventListener("click", function (e) {
      if (e.target === allBanksPopup) {
        closeAllBanksPopup();
      }
    });

    // ESC key handler
    function handleAllBanksEscKey(e) {
      if (e.key === "Escape") {
        closeAllBanksPopup();
      }
    }

    // Add Bank functionality
    if (showAddBankBtn && addBankPopup) {
      const addBankForm = document.getElementById("addBankForm");

      // Show Add Bank popup when button is clicked
      showAddBankBtn.addEventListener("click", function () {
        // Reset the form before showing
        if (addBankForm) {
          addBankForm.reset();
        }

        // Hide the All Banks popup temporarily
        allBanksPopup.classList.remove("show");

        // Show the Add Bank popup
        addBankPopup.classList.add("show");

        // Focus the first input for better UX
        setTimeout(() => {
          const firstInput = addBankPopup.querySelector("input");
          if (firstInput) firstInput.focus();
        }, 100);

        // Add ESC key support
        document.addEventListener("keydown", handleAddBankEscKey);
      });

      // Function to close Add Bank popup
      function closeAddBankPopup() {
        addBankPopup.classList.remove("show");
        document.removeEventListener("keydown", handleAddBankEscKey);
      }

      // Close Add Bank popup when X or Cancel is clicked
      closeAddBankPopupBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
          closeAddBankPopup();
          // Show All Banks popup again
          allBanksPopup.classList.add("show");
        });
      });

      // Back button functionality
      if (backToBanksBtn) {
        backToBanksBtn.addEventListener("click", function () {
          closeAddBankPopup();
          // Show All Banks popup again
          allBanksPopup.classList.add("show");
        });
      }

      // Close when clicking outside
      addBankPopup.addEventListener("click", function (e) {
        if (e.target === addBankPopup) {
          closeAddBankPopup();
          // Show All Banks popup again
          allBanksPopup.classList.add("show");
        }
      });

      // ESC key handler for Add Bank popup
      function handleAddBankEscKey(e) {
        if (e.key === "Escape") {
          closeAddBankPopup();
          // Show All Banks popup again
          allBanksPopup.classList.add("show");
        }
      }

      // Handle Add Bank form submission
      if (addBankForm) {
        addBankForm.addEventListener("submit", function (e) {
          e.preventDefault();

          // Create form data
          const formData = new FormData(this);

          // Send AJAX request
          fetch("index.php?path=bank/store", {
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

                // Close the Add Bank popup
                closeAddBankPopup();

                // Show success notification
                showNotification("Bank added successfully!", "success");

                // Refresh the banks list or redirect
                if (data.redirect) {
                  window.location.href = data.redirect;
                } else {
                  // Show All Banks popup again with refreshed data
                  // In a real implementation, you would also refresh the bank list
                  allBanksPopup.classList.add("show");
                }
              } else {
                // Display error notification
                showNotification(
                  data.error || "Failed to add bank. Please try again.",
                  "error"
                );
              }
            })
            .catch((error) => {
              console.error("Error:", error);
              showNotification("An error occurred. Please try again.", "error");
            });
        });
      }
    }
  }
});
