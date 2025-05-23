<?php
// dashboardNew.php
require_once 'controllers/AuthController.php'; // Assuming execution via root index.php

$authController = new AuthController();
$isBank = $authController->isBank();
$isAdmin = $authController->isAdmin(); // For clarity, though old dashboard implies !isBank is admin view
$bankId = $isBank && isset($_SESSION['bank_id']) ? $_SESSION['bank_id'] : null;

// Mock data - this would eventually come from a controller method
$data = [
    'totalReports' => 77,
    'totalCards' => 52,
    'totalDebitCards' => 30,
    'totalCreditCards' => 22,
    'totalBanks' => 8,
    'totalUsers' => 10
];

include __DIR__ . '/../includes/headerNew.php';
?>

<div class="container">
  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mt-md">
    <?php
      $cardsData = [];
      if ($isBank) {
          $cardsData = [
              [
                  'icon' => 'fas fa-file-alt',
                  'label' => 'Total Reports',
                  'value' => $data['totalReports'],
                  'onclick' => "window.location.href='index.php?path=report/bankReports&bank_id=$bankId'"
              ],
              [
                  'icon' => 'fas fa-credit-card',
                  'label' => 'Total Cards',
                  'value' => $data['totalCards'],
                  'onclick' => "window.location.href='index.php?path=card'"
              ],
              [
                  'icon' => 'fas fa-credit-card', // Consider a more specific icon for debit cards
                  'label' => 'Debit Cards',
                  'value' => $data['totalDebitCards'],
                  'onclick' => "window.location.href='index.php?path=card&type=DEBIT%20CARD'"
              ],
              [
                  'icon' => 'fas fa-credit-card', // Consider a more specific icon for credit cards
                  'label' => 'Credit Cards',
                  'value' => $data['totalCreditCards'],
                  'onclick' => "window.location.href='index.php?path=card&type=CREDIT%20CARD'"
              ],
          ];
      } else { // Non-bank users (e.g., Admin)
          $cardsData = [
              [
                  'icon' => 'fas fa-file-alt',
                  'label' => 'Total Reports',
                  'value' => $data['totalReports'],
                  'onclick' => '' // Not clickable for admin, as per old dashboard
              ],
              [
                  'icon' => 'fas fa-credit-card',
                  'label' => 'Total Cards',
                  'value' => $data['totalCards'],
                  'onclick' => '' // Not clickable
              ],
              [
                  'icon' => 'fas fa-university',
                  'label' => 'Total Banks',
                  'value' => $data['totalBanks'],
                  'onclick' => '' // Not clickable
              ],
              [
                  'icon' => 'fas fa-users',
                  'label' => 'Total Users',
                  'value' => $data['totalUsers'],
                  'onclick' => '' // Not clickable
              ],
          ];
      }

      foreach ($cardsData as $c) {
        $iconClass = $c['icon'];
        $label = $c['label'];
        $value = $c['value'];
        $onclick = $c['onclick'];
        include __DIR__ . '/../components/InfoCard.php';
      }
    ?>
  </div>
</div>
