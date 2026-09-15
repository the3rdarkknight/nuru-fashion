<?php
/**
 * Renders one product card.
 * Expects $product (array) and $categoryName (string) to be set before including.
 */

$pricing = getPricing($product);
$imgSrc = !empty($product['image']) ? 'uploads/products/' . $product['image'] : 'https://placehold.co/500x650/F3EDFB/7B2FF7?text=' . urlencode($product['name']);
?>
<div class="card">
    <div class="card-media">
        <?php if ($pricing['active']): ?>
            <span class="card-badge">-<?= (int)$pricing['percent'] ?>% today</span>
        <?php elseif ($product['stock_status'] === 'low_stock'): ?>
            <span class="card-badge low">Low stock</span>
        <?php endif; ?>
        <img src="<?= e($imgSrc) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
    </div>
    <div class="card-body">
        <span class="card-cat"><?= e($categoryName ?? '') ?></span>
        <h3 class="card-name"><?= e($product['name']) ?></h3>
        <div class="card-price-row">
            <span class="price-now"><?= formatPrice($pricing['final']) ?></span>
            <?php if ($pricing['active']): ?>
                <span class="price-was"><?= formatPrice($pricing['original']) ?></span>
            <?php endif; ?>
        </div>
        <div class="card-actions">
            <a href="<?= e(whatsAppProductLink($product)) ?>" target="_blank" rel="noopener" class="btn-whatsapp">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.2h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2Z"/></svg>
                Order
            </a>
            <a href="product.php?slug=<?= e($product['slug']) ?>" class="btn-view" aria-label="View <?= e($product['name']) ?>">→</a>
        </div>
    </div>
</div>