/**
 * Notification System Component
 * Handles showing notification messages to the user
 */

document.addEventListener("DOMContentLoaded", function () {
  // Notification system
  window.showNotification = function (message, type = "success") {
    const notification = document.getElementById("notification");
    const notificationMessage = document.getElementById("notification-message");
    const notificationProgress = document.getElementById(
      "notification-progress"
    );

    if (!notification || !notificationMessage || !notificationProgress) {
      console.error("Notification elements not found");
      return;
    }

    // Set message
    notificationMessage.textContent = message;

    // Set notification type
    notification.className = "notification show " + type;

    // Setup progress bar animation
    let width = 100;
    const duration = 10000; // 10 seconds
    const interval = 50; // update interval (ms)
    const step = (interval / duration) * 100;

    // Clear any existing timer
    if (window.notificationTimer) {
      clearInterval(window.notificationTimer);
    }

    // Set progress animation
    notificationProgress.style.width = "100%";
    window.notificationTimer = setInterval(() => {
      width -= step;
      notificationProgress.style.width = width + "%";

      if (width <= 0) {
        clearInterval(window.notificationTimer);
        hideNotification();
      }
    }, interval);
  };

  window.hideNotification = function () {
    const notification = document.getElementById("notification");
    if (notification) {
      notification.classList.remove("show");

      if (window.notificationTimer) {
        clearInterval(window.notificationTimer);
      }
    }
  };

  // Setup notification close button
  const notificationCloseBtn = document.getElementById("notification-close");
  if (notificationCloseBtn) {
    notificationCloseBtn.addEventListener("click", hideNotification);
  }
});
