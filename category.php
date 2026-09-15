<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$slug = $_GET['slug'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
$stmt->execute([$slug]);
$category = $stmt->fetch();

if (!$category) {
    header('Location: index.php');
    exit;
}

$pageTitle = $category['name'];

$stmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name FROM products p
    JOIN categories c ON c.id = p.category_id
    WHERE p.category_id = ?
    ORDER BY p.created_at DESC
");
$stmt->execute([$category['id']]);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC")->fetchAll();

require_once 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1><?= e($category['name']) ?></h1>
        <p><?= count($products) ?> piece<?= count($products) === 1 ? '' : 's' ?> in this collection</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="cat-strip">
            <a href="index.php" class="cat-chip">All</a>
            <?php foreach ($categories as $cat): ?>
                <a href="category.php?slug=<?= e($cat['slug']) ?>" class="cat-chip <?= $cat['id'] === $category['id'] ? 'active' : '' ?>"><?= e($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>

        <?php if (count($products) === 0): ?>
            <div class="empty-state">Nothing in this collection yet — check back soon, or ask us on WhatsApp.</div>
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