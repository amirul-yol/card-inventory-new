<?php
/**
 * Dashboard JavaScript Files
 * This file includes all the JavaScript files needed for the dashboard page
 */
?>

<!-- Pass PHP data to JavaScript -->
<script>
    // Initialize global objects
    window.banksWithCards = <?php echo json_encode($banksWithCards); ?>;
</script>

<!-- Dashboard Components -->
<script src="js/dashboard/dashboard.js"></script>
<script src="js/dashboard/notifications.js"></script>
<script src="js/dashboard/statCards.js"></script>
<script src="js/dashboard/bankCards.js"></script>
<script src="js/dashboard/bankCarousel.js"></script>
<script src="js/dashboard/reportsCarousel.js"></script>
<script src="js/dashboard/cardDetailsModal.js"></script>
<script src="js/dashboard/transactionHandlers.js"></script>
<script src="js/dashboard/cardForm.js"></script>
<script src="js/dashboard/bankManagement.js"></script>
<script src="js/dashboard/reportDetails.js"></script> 