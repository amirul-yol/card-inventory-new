/**
 * Reports Carousel Component
 * Handles carousel functionality for the reports section
 */

document.addEventListener("DOMContentLoaded", function () {
  // Reports carousel functionality
  const reportsCarousel = document.querySelector(".reports-carousel");
  const reportsPrevArrow = document.querySelector(".reports-prev-arrow");
  const reportsNextArrow = document.querySelector(".reports-next-arrow");

  if (reportsCarousel && reportsPrevArrow && reportsNextArrow) {
    const reportCards = reportsCarousel.querySelectorAll(
      ".report-carousel-item"
    );
    const cardWidth = 280; // width of each report card
    const gap = 16; // gap between cards (1rem)
    const totalWidth = cardWidth + gap;
    const visibleCards = Math.floor(reportsCarousel.offsetWidth / totalWidth);

    let currentReportPosition = 0;

    // Update arrow state
    function updateReportArrows() {
      reportsPrevArrow.style.opacity = currentReportPosition <= 0 ? "0.5" : "1";
      reportsPrevArrow.style.cursor =
        currentReportPosition <= 0 ? "default" : "pointer";

      const maxPosition = reportCards.length - visibleCards;
      reportsNextArrow.style.opacity =
        currentReportPosition >= maxPosition ? "0.5" : "1";
      reportsNextArrow.style.cursor =
        currentReportPosition >= maxPosition ? "default" : "pointer";
    }

    // Initialize arrows
    updateReportArrows();

    // Scroll to previous set of cards
    reportsPrevArrow.addEventListener("click", () => {
      if (currentReportPosition > 0) {
        currentReportPosition--;
        reportsCarousel.style.transform = `translateX(-${
          currentReportPosition * totalWidth
        }px)`;
        updateReportArrows();
      }
    });

    // Scroll to next set of cards
    reportsNextArrow.addEventListener("click", () => {
      if (currentReportPosition < reportCards.length - visibleCards) {
        currentReportPosition++;
        reportsCarousel.style.transform = `translateX(-${
          currentReportPosition * totalWidth
        }px)`;
        updateReportArrows();
      }
    });

    // Handle window resize
    window.addEventListener("resize", () => {
      const newVisibleCards = Math.floor(
        reportsCarousel.offsetWidth / totalWidth
      );
      if (
        newVisibleCards !== visibleCards &&
        currentReportPosition > reportCards.length - newVisibleCards
      ) {
        currentReportPosition = Math.max(
          0,
          reportCards.length - newVisibleCards
        );
        reportsCarousel.style.transform = `translateX(-${
          currentReportPosition * totalWidth
        }px)`;
      }
      updateReportArrows();
    });
  }
});
