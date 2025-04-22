/**
 * Main Dashboard JavaScript
 * This file loads all dashboard components
 */

// Dashboard component dependencies
// Global variables accessible to all scripts
window.banksWithCards = {}; // Will be populated from the PHP script
window.fetchAndShowTransactions = null; // Will be defined in cardDetailsModal.js
window.showCardDetails = null; // Will be defined in cardDetailsModal.js

// Load all dashboard component scripts
document.addEventListener("DOMContentLoaded", function () {
  console.log("Dashboard scripts loaded");
});
