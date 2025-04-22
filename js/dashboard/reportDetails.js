/**
 * Report Details Component
 * Handles report details modal functionality
 */

document.addEventListener("DOMContentLoaded", function () {
  // Report Details Modal functionality
  const reportButtons = document.querySelectorAll(".btn-view-report");
  const reportDetailsModal = document.getElementById("reportDetailsModal");
  const closeReportModalBtns = reportDetailsModal
    ? reportDetailsModal.querySelectorAll(".close-modal")
    : [];

  if (reportButtons.length && reportDetailsModal) {
    // Show modal when View Report button is clicked
    reportButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const reportId = this.closest(".report-carousel-item").getAttribute(
          "data-report-id"
        );

        // In a real implementation, you would load the report data from the server
        // Now we'll be setting up for that - we'd eventually make an AJAX call like:
        // fetch(`index.php?path=report/getReportDetailsJson&report_id=${reportId}`)

        // For now, we'll just use the status from the card
        const reportCard = this.closest(".report-carousel-item");
        const statusEl = reportCard.querySelector(".report-status");
        const isPending = statusEl.classList.contains("status-pending");
        const bankNameEl = reportCard.querySelector(".report-bank-name");
        const bankName = bankNameEl
          ? bankNameEl.textContent.trim()
          : "Unknown Bank";
        const dateEl = reportCard.querySelector(".report-date");
        const reportDate = dateEl ? dateEl.textContent.trim() : "-";

        // Update report title and ID
        const reportBankName = document.getElementById("reportBankName");
        const reportIdEl = document.getElementById("reportId");
        if (reportBankName) reportBankName.textContent = bankName;
        if (reportIdEl) reportIdEl.textContent = "#" + reportId;

        // Update report date
        const reportDateEl = document.getElementById("reportDate");
        if (reportDateEl) reportDateEl.textContent = reportDate;

        // Update modal content based on report status
        const reportStatusBadge = document.getElementById("reportStatusBadge");
        if (reportStatusBadge) {
          const status = statusEl.textContent.trim();
          reportStatusBadge.textContent = status;

          // Set appropriate color based on status
          if (status === "Pending") {
            reportStatusBadge.style.backgroundColor = "#f5a623";
          } else if (status === "Verified") {
            reportStatusBadge.style.backgroundColor = "#47c98e";
          } else if (status === "Rejected") {
            reportStatusBadge.style.backgroundColor = "#f44336";
          }
        }

        // Update action buttons based on user role and report status
        const verifyBtn = document.getElementById("switchToVerifyViewBtn");
        if (verifyBtn) {
          // Check if user is Production Officer (in a real app, check from server data)
          const isPO = verifyBtn.classList.contains("btn-verify");
          verifyBtn.style.display = isPending && isPO ? "flex" : "none";
        }

        // Show modal
        reportDetailsModal.classList.add("show");
        reportDetailsModal.style.display = "block";
        document.body.style.overflow = "hidden"; // Prevent scrolling

        // Add keyboard support for ESC key
        document.addEventListener("keydown", handleReportModalEscKey);
      });
    });

    // Also attach event listeners to carousel items
    const reportCarouselItems = document.querySelectorAll(
      ".report-carousel-item"
    );
    reportCarouselItems.forEach((card) => {
      card.addEventListener("click", function () {
        const reportId = this.getAttribute("data-report-id");

        // In a real implementation, you would load the report data from the server
        // Now we'll be setting up for that - we'd eventually make an AJAX call like:
        // fetch(`index.php?path=report/getReportDetailsJson&report_id=${reportId}`)

        // For now, we'll just use the status from the card
        const statusEl = this.querySelector(".report-status");
        const isPending = statusEl.classList.contains("status-pending");
        const bankNameEl = this.querySelector(".report-bank-name");
        const bankName = bankNameEl
          ? bankNameEl.textContent.trim()
          : "Unknown Bank";
        const dateEl = this.querySelector(".report-date");
        const reportDate = dateEl ? dateEl.textContent.trim() : "-";

        // Update report title and ID
        const reportBankName = document.getElementById("reportBankName");
        const reportIdEl = document.getElementById("reportId");
        if (reportBankName) reportBankName.textContent = bankName;
        if (reportIdEl) reportIdEl.textContent = "#" + reportId;

        // Update report date
        const reportDateEl = document.getElementById("reportDate");
        if (reportDateEl) reportDateEl.textContent = reportDate;

        // Update modal content based on report status
        const reportStatusBadge = document.getElementById("reportStatusBadge");
        if (reportStatusBadge) {
          const status = statusEl.textContent.trim();
          reportStatusBadge.textContent = status;

          // Set appropriate color based on status
          if (status === "Pending") {
            reportStatusBadge.style.backgroundColor = "#f5a623";
          } else if (status === "Verified") {
            reportStatusBadge.style.backgroundColor = "#47c98e";
          } else if (status === "Rejected") {
            reportStatusBadge.style.backgroundColor = "#f44336";
          }
        }

        // Update action buttons based on user role and report status
        const verifyBtn = document.getElementById("switchToVerifyViewBtn");
        if (verifyBtn) {
          // Check if user is Production Officer (in a real app, check from server data)
          const isPO = verifyBtn.classList.contains("btn-verify");
          verifyBtn.style.display = isPending && isPO ? "flex" : "none";
        }

        // Show modal
        reportDetailsModal.classList.add("show");
        reportDetailsModal.style.display = "block";
        document.body.style.overflow = "hidden"; // Prevent scrolling

        // Add keyboard support for ESC key
        document.addEventListener("keydown", handleReportModalEscKey);
      });
    });

    // Close modal functions
    if (closeReportModalBtns.length) {
      closeReportModalBtns.forEach((btn) => {
        btn.addEventListener("click", closeReportModal);
      });
    }

    // Close when clicking outside
    window.addEventListener("click", function (event) {
      if (event.target === reportDetailsModal) {
        closeReportModal();
      }
    });

    // Function to close modal
    function closeReportModal() {
      reportDetailsModal.classList.remove("show");
      setTimeout(() => {
        reportDetailsModal.style.display = "none";
        document.body.style.overflow = ""; // Re-enable scrolling

        // Switch back to the overview view
        switchToOverviewView();

        // Remove keyboard listener
        document.removeEventListener("keydown", handleReportModalEscKey);
      }, 300);
    }

    // ESC key handler
    function handleReportModalEscKey(e) {
      if (e.key === "Escape") {
        closeReportModal();
      }
    }

    // Switch between report views
    const reportOverviewContainer = document.getElementById(
      "reportOverviewContainer"
    );
    const reportVerificationContainer = document.getElementById(
      "reportVerificationContainer"
    );
    const rejectCardContainer = document.getElementById("rejectCardContainer");
    const switchToVerifyViewBtn = document.getElementById(
      "switchToVerifyViewBtn"
    );
    const backToReportOverviewBtn = document.getElementById(
      "backToReportOverviewBtn"
    );

    // Function to switch to overview view
    function switchToOverviewView() {
      if (
        reportOverviewContainer &&
        reportVerificationContainer &&
        rejectCardContainer
      ) {
        reportOverviewContainer.style.display = "block";
        reportVerificationContainer.style.display = "none";
        rejectCardContainer.style.display = "none";
      }
    }

    // Switch to verification view
    if (switchToVerifyViewBtn && reportVerificationContainer) {
      switchToVerifyViewBtn.addEventListener("click", function () {
        reportOverviewContainer.style.display = "none";
        reportVerificationContainer.style.display = "block";
      });
    }

    // Back to overview from verification view
    if (backToReportOverviewBtn) {
      backToReportOverviewBtn.addEventListener("click", switchToOverviewView);
    }

    // Reject card functionality
    const rejectButtons = reportDetailsModal.querySelectorAll(".btn-reject");
    const backToVerificationBtn = document.getElementById(
      "backToVerificationBtn"
    );

    // Show reject card form when a reject button is clicked
    if (rejectButtons.length && rejectCardContainer) {
      rejectButtons.forEach((btn) => {
        btn.addEventListener("click", function () {
          const transactionId = this.getAttribute("data-transaction-id");

          // In a real implementation, you would get the card details from the server
          // For this demo, we'll use placeholder values
          const cardNameInput = document.getElementById("reject-card-name");
          const originalQuantityInput = document.getElementById(
            "reject-original-quantity"
          );
          const transactionIdInput = document.getElementById(
            "reject-transaction-id"
          );

          if (cardNameInput && originalQuantityInput && transactionIdInput) {
            const row = this.closest("tr");
            const cardName = row.cells[0].textContent;
            const quantity = row.cells[1].textContent;

            cardNameInput.value = cardName;
            originalQuantityInput.value = quantity;
            transactionIdInput.value = transactionId;
          }

          // Show reject view
          reportVerificationContainer.style.display = "none";
          rejectCardContainer.style.display = "block";
        });
      });
    }

    // Back to verification view from reject card form
    if (backToVerificationBtn) {
      backToVerificationBtn.addEventListener("click", function () {
        rejectCardContainer.style.display = "none";
        reportVerificationContainer.style.display = "block";
      });
    }

    // Cancel reject card form
    const cancelRejectBtn = document.getElementById("cancelRejectBtn");
    if (cancelRejectBtn) {
      cancelRejectBtn.addEventListener("click", function () {
        rejectCardContainer.style.display = "none";
        reportVerificationContainer.style.display = "block";
      });
    }
  }
});
