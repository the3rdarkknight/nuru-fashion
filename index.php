<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Home';

// Today's active deals
$deals = $pdo->query("
    SELECT p.*, c.name AS category_name FROM products p
    JOIN categories c ON c.id = p.category_id
    WHERE p.discount_percent > 0
      AND (p.discount_start IS NULL OR p.discount_start <= CURDATE())
      AND (p.discount_end IS NULL OR p.discount_end >= CURDATE())
    ORDER BY p.updated_at DESC
")->fetchAll();

// Categories for the chip strip
$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC")->fetchAll();

// Newest / featured products for the main grid
$products = $pdo->query("
    SELECT p.*, c.name AS category_name FROM products p
    JOIN categories c ON c.id = p.category_id
    ORDER BY p.is_featured DESC, p.created_at DESC
    LIMIT 12
")->fetchAll();

require_once 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-copy">
            <span class="hero-eyebrow">New arrivals every week</span>
            <h1>Dress like today<br>is <em>the</em> occasion.</h1>
            <p>Curated women's fashion for Nairobi — dresses, sets and accessories that don't wait for a special day. Prices move daily, so today's discount might be gone tomorrow.</p>
            <div class="hero-actions">
                <a href="#deals" class="btn-primary">Shop today's deals</a>
                <a href="<?= e(whatsAppGeneralLink()) ?>" target="_blank" rel="noopener" class="btn-secondary">Chat with us</a>
            </div>
        </div>
        <div class="hero-art">
            <span class="hero-art-tag">Today's best cut: up to 25% off select pieces</span>
        </div>
    </div>
</section>

<?php if (count($deals) > 0): ?>
<section class="deals-section" id="deals">
    <div class="container">
        <div class="deals-header">
            <h2>Today's <span>deals</span></h2>
            <span class="deals-sub">Refreshed daily — grab it before midnight</span>
        </div>
        <div class="deals-row">
            <?php foreach ($deals as $product): $categoryName = $product['category_name']; ?>
                <?php include 'includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="container">
        <div class="cat-strip">
            <a href="index.php" class="cat-chip active">All</a>
            <?php foreach ($categories as $cat): ?>
                <a href="category.php?slug=<?= e($cat['slug']) ?>" class="cat-chip"><?= e($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="section-header">
            <h2>Just landed</h2>
        </div>

        <?php if (count($products) === 0): ?>
            <div class="empty-state">No products yet — add some from the admin panel.</div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): $categoryName = $product['category_name']; ?>
                    <?php include 'includes/product-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>