<?php include 'views/includes/headerNew.php'; ?>

<div class="container">
  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mt-md">
    <?php
      // Mock data for info cards
      $cards = [
        ['icon' => 'fas fa-file-alt', 'label' => 'Total Reports', 'value' => '77', 'onclick' => "console.log('Total Reports clicked');"],
        ['icon' => 'fas fa-credit-card', 'label' => 'Total Cards', 'value' => '52', 'onclick' => "console.log('Total Cards clicked');"],
        ['icon' => 'fas fa-university', 'label' => 'Total Banks', 'value' => '8', 'onclick' => "console.log('Total Banks clicked');"],
        ['icon' => 'fas fa-users', 'label' => 'Total Users', 'value' => '10', 'onclick' => "console.log('Total Users clicked');"],
      ];
      foreach ($cards as $c) {
        $iconClass = $c['icon'];
        $label = $c['label'];
        $value = $c['value'];
        $onclick = $c['onclick'];
        include 'views/components/InfoCard.php';
      }
    ?>
  </div>
</div>
