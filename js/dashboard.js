// Report modal functionality
document.addEventListener("DOMContentLoaded", function () {
  const reportDetailsModal = document.getElementById("reportDetailsModal");
  const reportCards = document.querySelectorAll(
    ".report-carousel-item, .report-card"
  );
  let currentReportId = null;

  // Function to load and display report details
  function loadReportDetails(reportId) {
    currentReportId = reportId; // Store the current report ID
    fetch(`api/getReportDetails.php?report_id=${reportId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          showNotification("Error loading report details", "error");
          return;
        }

        // Update modal with real data
        document.getElementById("reportId").textContent = `#${data.id}`;
        document.getElementById("reportBankName").textContent = data.bank_name;
        document.getElementById("reportDate").textContent = data.report_date;
        document.getElementById("reportCreator").textContent =
          data.created_by || "Not available";
        document.getElementById("reportVerifier").textContent =
          data.verified_by || "Not verified yet";

        // Update bank logo
        const bankLogo = document.querySelector(".bank-logo-lg");
        bankLogo.src = data.bank_logo || "uploads/logos/default_bank.png";
        bankLogo.alt = data.bank_name;

        // Update status badge
        const statusBadge = document.getElementById("reportStatusBadge");
        statusBadge.textContent = data.status;
        statusBadge.className =
          "report-status-badge status-" + data.status.toLowerCase();

        // Update cards table
        const tableBody = document.getElementById("reportCardsTableBody");
        tableBody.innerHTML = "";

        data.cards.forEach((card) => {
          const row = document.createElement("tr");
          row.innerHTML = `
                        <td>${card.card_name}</td>
                        <td>${card.quantity}</td>
                        <td>${card.remarks}</td>
                        <td>${card.rejected_quantity}</td>
                    `;
          tableBody.appendChild(row);
        });

        // Show the modal
        reportDetailsModal.style.display = "block";
      })
      .catch((error) => {
        console.error("Error:", error);
        showNotification("Error loading report details", "error");
      });
  }

  // Add click event listeners to report cards
  reportCards.forEach((card) => {
    card.addEventListener("click", function () {
      const reportId = this.dataset.reportId;
      loadReportDetails(reportId);
    });
  });

  // Handle PDF generation
  document
    .getElementById("generatePdfBtn")
    .addEventListener("click", function (e) {
      e.preventDefault();
      if (currentReportId) {
        window.location.href = `index.php?path=report/download&report_id=${currentReportId}`;
      }
    });

  // Close modal functionality
  const closeButtons = document.querySelectorAll(".close-modal");
  closeButtons.forEach((button) => {
    button.addEventListener("click", function () {
      reportDetailsModal.style.display = "none";
    });
  });

  // Close modal when clicking outside
  window.addEventListener("click", function (event) {
    if (event.target === reportDetailsModal) {
      reportDetailsModal.style.display = "none";
    }
  });
});
