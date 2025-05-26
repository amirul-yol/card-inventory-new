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
                  'htmlAttributes' => [
                      'data-bs-toggle' => 'modal',
                      'data-bs-target' => '#bankReportsModal'
                  ]
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
                  'htmlAttributes' => [
                      'data-bs-toggle' => 'modal',
                      'data-bs-target' => '#bankReportsModal'
                  ]
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
        $onclick = $c['onclick'] ?? null; // Ensure $onclick is defined, default to null
        $htmlAttributes = $c['htmlAttributes'] ?? []; // Pass htmlAttributes if they exist
        include __DIR__ . '/../components/InfoCard.php';
      }
    ?>
  </div>

  <?php if ($isBank && isset($bankId)): ?>
    <?php
      // Prepare data for DashboardFilterControls component
      $filterGroups = [];
      if (isset($data['cardTypeFilterOptions']) && !empty($data['cardTypeFilterOptions'])) {
          $filterGroups[] = [
              'label'         => 'Filter by Card Type:',
              'name'          => 'card_type',
              'options'       => $data['cardTypeFilterOptions'],
              'selectedValue' => $data['selectedCardType'] ?? null,
              'allLabel'      => 'All Card Types'
          ];
      }

      if (isset($data['chipTypeFilterOptions']) && !empty($data['chipTypeFilterOptions'])) {
          $filterGroups[] = [
              'label'         => 'Filter by Chip Type:',
              'name'          => 'chip_type',
              'options'       => $data['chipTypeFilterOptions'],
              'selectedValue' => $data['selectedChipType'] ?? null,
              'allLabel'      => 'All Chip Types'
          ];
      }

      if (isset($data['associationFilterOptions']) && !empty($data['associationFilterOptions'])) {
          $filterGroups[] = [
              'label'         => 'Filter by Payment Scheme:',
              'name'          => 'association',
              'options'       => $data['associationFilterOptions'],
              'selectedValue' => $data['selectedAssociation'] ?? null,
              'allLabel'      => 'All Schemes'
          ];
      }

      // Add a group for sorting by Expiry Date
      // Note: 'options' for sort is an associative array [value => display_text]
      // The component needs to be slightly adjusted if it doesn't handle associative options for display vs value.
      // For now, assuming simple string options or that the component handles it.
      // Let's define explicit options for sorting.
      $expirySortOptions = [
          '' => 'Sort by Expiry (Default)', // Default/No sort specific to expiry
          'ASC' => 'Oldest First',
          'DESC' => 'Newest First'
      ];
      $filterGroups[] = [
          'label'         => 'Sort by Expiry Date:',
          'name'          => 'expiry_sort',
          // Pass associative array for options if component supports it, otherwise map to simple array if needed.
          // For simplicity, let's assume the component can take an array of values and display them, 
          // or we can adjust the component to handle this by checking if options are associative.
          'options'       => $expirySortOptions, // This will be an associative array
          'selectedValue' => $data['selectedExpirySort'] ?? null,
          'allLabel'      => 'Default Sort' // This might be confusing for a sort, let's make it specific
      ];

      $formAction = 'index.php';
      $formMethod = 'GET';
      $hiddenPathValue = 'dashboardNew'; // Assuming 'dashboardNew' is the correct path for this view
      $clearFilterLink = 'index.php?path=' . $hiddenPathValue;
      
      // Construct active filter message (optional, can be enhanced)
      $activeFilterMessage = null;
      if (!empty($data['selectedCardType'])) {
        $activeFilterMessage = 'Filtered by Card Type: ' . htmlspecialchars($data['selectedCardType']);
      }
      if (!empty($data['selectedChipType'])) {
        $activeFilterMessage .= ($activeFilterMessage ? '; ' : '') . 'Chip: ' . htmlspecialchars($data['selectedChipType']);
      }
      if (!empty($data['selectedAssociation'])) {
        $activeFilterMessage .= ($activeFilterMessage ? '; ' : '') . 'Scheme: ' . htmlspecialchars($data['selectedAssociation']);
      }
      if (!empty($data['selectedExpirySort'])) {
        $sortLabel = ($data['selectedExpirySort'] === 'ASC') ? 'Oldest First' : 'Newest First';
        $activeFilterMessage .= ($activeFilterMessage ? '; ' : '') . 'Sort by Expiry: ' . $sortLabel;
      }

      // Include the filter controls component if there are any filter groups defined
      if (!empty($filterGroups)) {
          include __DIR__ . '/../components/DashboardFilterControls.php';
      }
    ?>
    <?php
      // Use actual card data passed from DashboardController
      // Default to an empty array if not set or empty to prevent errors in the component
      $cardsData = isset($data['bankCardsDashboard']) && !empty($data['bankCardsDashboard']) ? $data['bankCardsDashboard'] : [];
      include __DIR__ . '/../components/CardQuickInfoTable.php';
    ?>
  <?php endif; ?>

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

</div> <!-- End of .container -->

<?php 
  // Include the Bank Reports Modal component here, so it's available on the page
  // It's used by both bank users and admin/other non-bank roles via their respective 'Total Reports' InfoCard.
  
  // Set up variables for the modal
  $modalBankId = $isBank ? ($data['currentBankId'] ?? null) : null;
  $modalBankName = $isBank ? ($data['currentBankName'] ?? 'Selected Bank') : null;
  
  // Include the modal component with bank context
  include __DIR__ . '/../components/BankReportsModal.php';
?>

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
