/**
 * Bank Carousel Component
 * Handles carousel functionality for the bank cards section
 */

document.addEventListener("DOMContentLoaded", function () {
  // Bank carousel functionality
  const carousel = document.querySelector(".bank-carousel");
  const prevArrow = document.querySelector(".prev-arrow");
  const nextArrow = document.querySelector(".next-arrow");

  if (carousel && prevArrow && nextArrow) {
    const bankCards = carousel.querySelectorAll(".bank-card-item");
    const cardWidth = 180; // width of each card plus gap
    const gap = 16; // gap between cards (1rem)
    const totalWidth = cardWidth + gap;
    const visibleCards = Math.floor(carousel.offsetWidth / totalWidth);

    let currentPosition = 0;

    // Update arrow state
    function updateArrows() {
      prevArrow.style.opacity = currentPosition <= 0 ? "0.5" : "1";
      prevArrow.style.cursor = currentPosition <= 0 ? "default" : "pointer";

      const maxPosition = bankCards.length - visibleCards;
      nextArrow.style.opacity = currentPosition >= maxPosition ? "0.5" : "1";
      nextArrow.style.cursor =
        currentPosition >= maxPosition ? "default" : "pointer";
    }

    // Initialize arrows
    updateArrows();

    // Scroll to previous set of cards
    prevArrow.addEventListener("click", () => {
      if (currentPosition > 0) {
        currentPosition--;
        carousel.style.transform = `translateX(-${
          currentPosition * totalWidth
        }px)`;
        updateArrows();
      }
    });

    // Scroll to next set of cards
    nextArrow.addEventListener("click", () => {
      if (currentPosition < bankCards.length - visibleCards) {
        currentPosition++;
        carousel.style.transform = `translateX(-${
          currentPosition * totalWidth
        }px)`;
        updateArrows();
      }
    });

    // Handle window resize
    window.addEventListener("resize", () => {
      const newVisibleCards = Math.floor(carousel.offsetWidth / totalWidth);
      if (
        newVisibleCards !== visibleCards &&
        currentPosition > bankCards.length - newVisibleCards
      ) {
        currentPosition = Math.max(0, bankCards.length - newVisibleCards);
        carousel.style.transform = `translateX(-${
          currentPosition * totalWidth
        }px)`;
      }
      updateArrows();
    });
  }
});
