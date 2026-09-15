<?php
require_once 'auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $error = 'Category name is required.';
    } else {
        $slug = slugify($name);
        $base = $slug; $i = 1;
        while (true) {
            $check = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
            $check->execute([$slug]);
            if (!$check->fetch()) break;
            $slug = $base . '-' . (++$i);
        }
        $maxOrder = (int)$pdo->query("SELECT COALESCE(MAX(sort_order),0) FROM categories")->fetchColumn();
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, sort_order) VALUES (?, ?, ?)");
        $stmt->execute([$name, $slug, $maxOrder + 1]);
        header('Location: manage_categories.php?msg=' . urlencode('"' . $name . '" was added.'));
        exit;
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $count = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $count->execute([$id]);
    if ($count->fetchColumn() > 0) {
        $error = "Can't delete a category that still has products in it. Move or delete those products first.";
    } else {
        $del = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $del->execute([$id]);
        header('Location: manage_categories.php?msg=' . urlencode('Category deleted.'));
        exit;
    }
}

$categories = $pdo->query("
    SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count
    FROM categories c ORDER BY c.sort_order ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Categories — Admin</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include 'admin-nav.php'; ?>

<div class="admin-topbar">
    <h1>Categories</h1>
</div>

<?php if (!empty($_GET['msg'])): ?>
    <div class="alert success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card-panel" style="margin-bottom:24px;">
    <form method="POST" style="display:flex; gap:10px; align-items:flex-end;">
        <input type="hidden" name="action" value="add">
        <div style="flex:1;">
            <label for="name" style="margin-top:0;">New category name</label>
            <input type="text" id="name" name="name" placeholder="e.g. Jackets &amp; Outerwear" required>
        </div>
        <button type="submit" class="btn btn-magenta">Add category</button>
    </form>
</div>

<div class="card-panel">
    <table>
        <thead><tr><th>Name</th><th>Products</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                <td><?= $cat['product_count'] ?></td>
                <td>
                    <a href="manage_categories.php?delete=<?= $cat['id'] ?>" class="btn btn-red btn-small" onclick="return confirm('Delete this category?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

    </main>
</div>
</body>
</html>