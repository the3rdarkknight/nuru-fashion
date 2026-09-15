<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$slug = $_GET['slug'] ?? '';

$stmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p
    JOIN categories c ON c.id = p.category_id
    WHERE p.slug = ?
");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit;
}

$pageTitle = $product['name'];
$pricing = getPricing($product);
$imgSrc = !empty($product['image']) ? 'uploads/products/' . $product['image'] : 'https://placehold.co/700x875/F3EDFB/7B2FF7?text=' . urlencode($product['name']);
$sizes = $product['sizes'] ? array_map('trim', explode(',', $product['sizes'])) : [];
$colors = $product['colors'] ? array_map('trim', explode(',', $product['colors'])) : [];

// A few more items from the same category
$stmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name FROM products p
    JOIN categories c ON c.id = p.category_id
    WHERE p.category_id = ? AND p.id != ?
    ORDER BY p.created_at DESC LIMIT 4
");
$stmt->execute([$product['category_id'], $product['id']]);
$related = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">Home</a> /
        <a href="category.php?slug=<?= e($product['category_slug']) ?>"><?= e($product['category_name']) ?></a> /
        <?= e($product['name']) ?>
    </div>

    <div class="product-detail">
        <div class="product-media">
            <img src="<?= e($imgSrc) ?>" alt="<?= e($product['name']) ?>">
        </div>

        <div class="product-info">
            <span class="card-cat"><?= e($product['category_name']) ?></span>
            <h1><?= e($product['name']) ?></h1>

            <div class="product-price-block">
                <span class="price-now"><?= formatPrice($pricing['final']) ?></span>
                <?php if ($pricing['active']): ?>
                    <span class="price-was"><?= formatPrice($pricing['original']) ?></span>
                    <span class="discount-pill">-<?= (int)$pricing['percent'] ?>% today only</span>
                <?php endif; ?>
            </div>

            <p class="product-desc"><?= nl2br(e($product['description'])) ?></p>

            <?php if (count($sizes) > 0): ?>
            <div class="option-block">
                <div class="option-label">Available sizes</div>
                <div class="option-pills">
                    <?php foreach ($sizes as $size): ?>
                        <span class="option-pill"><?= e($size) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (count($colors) > 0): ?>
            <div class="option-block">
                <div class="option-label">Available colours</div>
                <div class="option-pills">
                    <?php foreach ($colors as $color): ?>
                        <span class="option-pill"><?= e($color) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php
            $stockLabels = [
                'in_stock' => ['in', 'In stock — ready to ship'],
                'low_stock' => ['low', 'Low stock — order soon'],
                'out_of_stock' => ['out', 'Out of stock — message us for restock date'],
            ];
            [$stockClass, $stockText] = $stockLabels[$product['stock_status']];
            ?>
            <div class="stock-note <?= $stockClass ?>"><?= e($stockText) ?></div>

            <div class="product-order">
                <a href="<?= e(whatsAppProductLink($product)) ?>" target="_blank" rel="noopener" class="btn-whatsapp">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.2h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2Z"/></svg>
                    Order this on WhatsApp
                </a>
            </div>
            <p class="order-note">Tapping "Order" opens WhatsApp with this item and price filled in — just confirm your size and delivery address.</p>
        </div>
    </div>

    <?php if (count($related) > 0): ?>
    <section class="section" style="padding-top:0;">
        <div class="section-header">
            <h2>You might also like</h2>
        </div>
        <div class="product-grid">
            <?php foreach ($related as $product): $categoryName = $product['category_name']; ?>
                <?php include 'includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>