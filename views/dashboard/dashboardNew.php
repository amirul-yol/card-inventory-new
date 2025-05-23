<?php
// dashboardNew.php

// Variables like $data, $isBank, $isAdmin, $bankId are expected to be set by the calling controller method.

include __DIR__ . '/../includes/headerNew.php';
?>

<div class="container">
  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mt-md">
    <?php
      $infoCards = []; // Renamed from $cardsData to avoid confusion with $data array
      // The controller now sets $isBank, $isAdmin, and $bankId. We can use them directly.
      // The $data array is also set by the controller.
      if ($isBank && isset($bankId)) { // Ensure $bankId is set for bank users
          $infoCards = [
              [
                  'icon' => 'fas fa-file-alt',
                  'label' => 'Total Reports',
                  'value' => $data['totalReports'] ?? 0, // Use fetched data, default to 0 if not set
                  'onclick' => "window.location.href='index.php?path=report/bankReports&bank_id=$bankId'"
              ],
              [
                  'icon' => 'fas fa-credit-card',
                  'label' => 'Total Cards',
                  'value' => $data['totalCards'] ?? 0,
                  'onclick' => "window.location.href='index.php?path=card'"
              ],
              [
                  'icon' => 'fas fa-credit-card', // Consider a more specific icon for debit cards
                  'label' => 'Debit Cards',
                  'value' => $data['totalDebitCards'] ?? 0,
                  'onclick' => "window.location.href='index.php?path=card&type=DEBIT%20CARD'"
              ],
              [
                  'icon' => 'fas fa-credit-card', // Consider a more specific icon for credit cards
                  'label' => 'Credit Cards',
                  'value' => $data['totalCreditCards'] ?? 0,
                  'onclick' => "window.location.href='index.php?path=card&type=CREDIT%20CARD'"
              ],
          ];
      } else { // Non-bank users (e.g., Admin)
          $infoCards = [
              [
                  'icon' => 'fas fa-file-alt',
                  'label' => 'Total Reports',
                  'value' => $data['totalReports'] ?? 0,
                  'onclick' => "window.location.href='index.php?path=report'" // Link to general reports page for admin
              ],
              [
                  'icon' => 'fas fa-credit-card',
                  'label' => 'Total Cards',
                  'value' => $data['totalCards'] ?? 0,
                  'onclick' => "window.location.href='index.php?path=card'" // Link to general cards page for admin
              ],
              [
                  'icon' => 'fas fa-university',
                  'label' => 'Total Banks',
                  'value' => $data['totalBanks'] ?? 0,
                  'onclick' => "window.location.href='index.php?path=bank'" // Link to banks management for admin
              ],
              [
                  'icon' => 'fas fa-users',
                  'label' => 'Total Users',
                  'value' => $data['totalUsers'] ?? 0,
                  'onclick' => "window.location.href='index.php?path=user'" // Link to user management for admin
              ],
          ];
      }

      foreach ($infoCards as $c) {
        $iconClass = $c['icon'];
        $label = $c['label'];
        $value = $c['value'];
        $onclick = $c['onclick'];
        include __DIR__ . '/../components/InfoCard.php';
      }
    ?>
  </div>

  <?php 
    // Show bank carousel for non-bank users if there's bank data from the controller
    // $banksForCarousel is passed from DashboardController
    if (!$isBank && !empty($banksForCarousel)): 
  ?>
    <div class="mt-xl">
      <h2 class="mb-md">Banks</h2>
      <?php
        // Use the $banksForCarousel data passed from the controller
        $banks = $banksForCarousel; 
        include __DIR__ . '/../components/BankCarousel.php';
      ?>
    </div>
  <?php endif; ?>

</div> <!-- End of .container-fluid from headerNew.php or a main wrapper -->

<?php 
  // Include the new footer
  include __DIR__ . '/../includes/footerNew.php';
?>

<!-- Bootstrap JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script for updating current time in header -->
<script>
  function updateTime() {
    const currentTimeSpan = document.getElementById('currentTime');
    if (currentTimeSpan) { // Check if element exists before trying to set textContent
      const now = new Date();
      const dateStr = now.toLocaleDateString('en-GB'); // DD/MM/YYYY
      const timeStr = now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' }); // HH:MM:SS
      currentTimeSpan.textContent = `${dateStr} ${timeStr}`;
    }
  }
  // Initial call to display time immediately
  updateTime();
  // Update time every second
  setInterval(updateTime, 1000);
</script>

</body>
</html>
