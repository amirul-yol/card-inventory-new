<?php
// InfoCard component
// Expects: $iconClass, $label, $value, optional $onclick
?>
<div class="col">
  <div class="card bg-accent text-white mb-4" <?php if (!empty($onclick)): ?>onclick="<?=$onclick?>" style="cursor:pointer;"<?php endif; ?>>
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
