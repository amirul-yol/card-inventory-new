<?php
// views/components/BankCarousel.php
// Expects: $banks (array of bank data)

if (empty($banks)) {
    echo '<p class="text-center text-muted">No banks to display.</p>';
    return;
}

// Group banks into slides, 5 banks per slide for medium devices and up
$banksPerSlide = 6; // Show 5 narrower cards per slide
$bankChunks = array_chunk($banks, $banksPerSlide);
$carouselId = 'bankCarousel' . uniqid(); // Ensure unique ID for multiple carousels if ever needed

?>
<div class="bank-carousel-wrapper d-flex align-items-center">
    <?php if (count($bankChunks) > 1): ?>
        <button class="carousel-control-prev-custom btn btn-link" type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
    <?php endif; ?>

    <div id="<?= $carouselId ?>" class="carousel slide bank-carousel-component flex-grow-1 mx-2 position-relative" data-bs-interval="false"> 
        <?php if (count($bankChunks) > 1): ?>
            <div class="carousel-indicators" style="bottom: -30px;"> 
                <?php foreach ($bankChunks as $index => $chunk): ?>
                    <button type="button" 
                            data-bs-target="#<?= $carouselId ?>" 
                            data-bs-slide-to="<?= $index ?>" 
                            class="<?= $index === 0 ? 'active' : '' ?>" 
                            aria-current="<?= $index === 0 ? 'true' : 'false' ?>" 
                            aria-label="Slide <?= $index + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="carousel-inner">
            <?php foreach ($bankChunks as $index => $bankChunk): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <div class="row gx-2"> 
                        <?php foreach ($bankChunk as $bank): ?>
                            <div class="col-md-2 col-sm-4 col-6"> 
                                <?php 
                                    $bankLogoUrl = $bank['logoUrl'];
                                    $bankName = $bank['name'];
                                    $cardsValue = $bank['cardsValue'];
                                    include __DIR__ . '/BankCard.php'; // Include the BankCard component
                                ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (count($bankChunks) > 1): ?>
        <button class="carousel-control-next-custom btn btn-link" type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    <?php endif; ?>
</div>
