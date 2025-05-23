<?php
// InfoCard component
// Expects: $iconClass, $label, $value, optional $onclick, optional $htmlAttributes (array)
?>
<div class="col">
  <div class="card bg-accent text-white mb-4"
    <?php 
      if (!empty($onclick)) {
        echo ' onclick="' . htmlspecialchars($onclick) . '" style="cursor:pointer;"';
      }
      if (!empty($htmlAttributes) && is_array($htmlAttributes)) {
        foreach ($htmlAttributes as $attr => $val) {
          echo ' ' . htmlspecialchars($attr) . '="' . htmlspecialchars($val) . '"';
        }
        // Add cursor pointer if modal attributes are present and no onclick
        if (empty($onclick) && (isset($htmlAttributes['data-bs-toggle']) && $htmlAttributes['data-bs-toggle'] === 'modal')) {
            echo ' style="cursor:pointer;"';
        }
      }
    ?>>
    <div class="card-body">
      <div class="d-flex align-items-center">
        <i class="<?=$iconClass?> fa-2x me-3"></i>
        <div>
          <h5 class="card-title mb-1"><?=$label?></h5>
          <p class="card-text mb-0"><?=$value?></p>
        </div>
      </div>
    </div>
  </div>
</div>
