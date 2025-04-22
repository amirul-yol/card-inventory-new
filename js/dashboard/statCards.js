/**
 * Stat Cards Component
 * Handles hover effects and interactions for the stat cards
 */

document.addEventListener("DOMContentLoaded", function () {
  // Add smooth hover effect for stat cards
  const statCards = document.querySelectorAll(".stat-card");
  statCards.forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-5px)";
      this.style.boxShadow = "0 8px 15px rgba(0, 0, 0, 0.1)";
    });

    card.addEventListener("mouseleave", function () {
      if (!this.classList.contains("clickable")) {
        this.style.transform = "";
        this.style.boxShadow = "";
      }
    });
  });
});
