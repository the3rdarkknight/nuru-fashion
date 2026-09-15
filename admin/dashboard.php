<?php
require_once 'auth.php';

$stats = [
    'total' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'active_deals' => $pdo->query("
        SELECT COUNT(*) FROM products
        WHERE discount_percent > 0
          AND (discount_start IS NULL OR discount_start <= CURDATE())
          AND (discount_end IS NULL OR discount_end >= CURDATE())
    ")->fetchColumn(),
    'low_stock' => $pdo->query("SELECT COUNT(*) FROM products WHERE stock_status = 'low_stock'")->fetchColumn(),
    'out_of_stock' => $pdo->query("SELECT COUNT(*) FROM products WHERE stock_status = 'out_of_stock'")->fetchColumn(),
];

$products = $pdo->query("
    SELECT p.*, c.name AS category_name FROM products p
    JOIN categories c ON c.id = p.category_id
    ORDER BY p.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Products — Admin</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include 'admin-nav.php'; ?>

<div class="admin-topbar">
    <h1>Products</h1>
    <a href="add_product.php" class="btn btn-magenta">+ Add product</a>
</div>

<?php if (!empty($_GET['msg'])): ?>
    <div class="alert success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<div class="stat-row">
    <div class="stat-card"><div class="num"><?= $stats['total'] ?></div><div class="label">Total products</div></div>
    <div class="stat-card"><div class="num"><?= $stats['active_deals'] ?></div><div class="label">Deals live today</div></div>
    <div class="stat-card"><div class="num"><?= $stats['low_stock'] ?></div><div class="label">Low stock</div></div>
    <div class="stat-card"><div class="num"><?= $stats['out_of_stock'] ?></div><div class="label">Out of stock</div></div>
</div>

<div class="card-panel">
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Today's discount</th>
                <th>Stock</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): $pricing = getPricing($p); ?>
            <tr>
                <td>
                    <img class="thumb" src="<?= $p['image'] ? '../uploads/products/' . htmlspecialchars($p['image']) : 'https://placehold.co/60x76/F1E9FE/7B2FF7?text=%20' ?>" alt="">
                </td>
                <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                <td><?= htmlspecialchars($p['category_name']) ?></td>
                <td>
                    <?= formatPrice($pricing['final']) ?>
                    <?php if ($pricing['active']): ?>
                        <div style="color:#5B5468; font-size:12px; text-decoration:line-through;"><?= formatPrice($pricing['original']) ?></div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($p['discount_percent'] > 0): ?>
                        <span class="pill <?= $pricing['active'] ? 'active' : 'inactive' ?>">
                            -<?= (int)$p['discount_percent'] ?>% <?= $pricing['active'] ? '(live)' : '(scheduled/expired)' ?>
                        </span>
                    <?php else: ?>
                        <span style="color:#5B5468; font-size:13px;">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php $s = ['in_stock' => ['in','In stock'], 'low_stock' => ['low','Low'], 'out_of_stock' => ['out','Out']][$p['stock_status']]; ?>
                    <span class="pill <?= $s[0] ?>"><?= $s[1] ?></span>
                </td>
                <td style="white-space:nowrap;">
                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-small">Edit</a>
                    <form action="delete_product.php" method="POST" style="display:inline;" onsubmit="return confirm('Delete this product? This cannot be undone.');">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-red btn-small">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (count($products) === 0): ?>
            <tr><td colspan="7" style="text-align:center; color:#5B5468; padding:30px;">No products yet. Click "Add product" to create your first one.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

    </main>
</div>
</body>
</html>