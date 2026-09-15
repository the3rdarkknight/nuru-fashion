<?php
require_once 'auth.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: dashboard.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $basePrice = (float)($_POST['base_price'] ?? 0);
    $discountPercent = (float)($_POST['discount_percent'] ?? 0);
    $discountStart = $_POST['discount_start'] ?: null;
    $discountEnd = $_POST['discount_end'] ?: null;
    $sizes = trim($_POST['sizes'] ?? '');
    $colors = trim($_POST['colors'] ?? '');
    $stockStatus = $_POST['stock_status'] ?? 'in_stock';
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

    if ($name === '' || $categoryId === 0 || $basePrice <= 0) {
        $error = 'Please fill in the product name, category and a valid price.';
    } else {
        $imageName = $product['image'];

        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $newImage = $product['slug'] . '-' . time() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/products/' . $newImage);
                if ($imageName && file_exists(__DIR__ . '/../uploads/products/' . $imageName)) {
                    unlink(__DIR__ . '/../uploads/products/' . $imageName);
                }
                $imageName = $newImage;
            } else {
                $error = 'Image must be a jpg, png or webp file.';
            }
        }

        if ($error === '') {
            $stmt = $pdo->prepare("
                UPDATE products SET
                    category_id = ?, name = ?, description = ?, base_price = ?,
                    discount_percent = ?, discount_start = ?, discount_end = ?,
                    sizes = ?, colors = ?, image = ?, stock_status = ?, is_featured = ?
                WHERE id = ?
            ");
            $stmt->execute([$categoryId, $name, $description, $basePrice, $discountPercent, $discountStart, $discountEnd, $sizes, $colors, $imageName, $stockStatus, $isFeatured, $id]);

            header('Location: dashboard.php?msg=' . urlencode('"' . $name . '" was updated.'));
            exit;
        }
    }
    // Keep edits visible on error
    $product = array_merge($product, [
        'name' => $name, 'category_id' => $categoryId, 'description' => $description,
        'base_price' => $basePrice, 'discount_percent' => $discountPercent,
        'discount_start' => $discountStart, 'discount_end' => $discountEnd,
        'sizes' => $sizes, 'colors' => $colors, 'stock_status' => $stockStatus, 'is_featured' => $isFeatured,
    ]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Product — Admin</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include 'admin-nav.php'; ?>

<div class="admin-topbar">
    <h1>Edit product</h1>
    <a href="dashboard.php" class="btn btn-outline">← Back to products</a>
</div>

<?php if ($error): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card-panel">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $product['id'] ?>">

        <?php if ($product['image']): ?>
            <img class="thumb" style="width:90px; height:112px; margin-bottom:14px;" src="../uploads/products/<?= htmlspecialchars($product['image']) ?>" alt="">
        <?php endif; ?>

        <label for="name">Product name</label>
        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($product['name']) ?>">

        <div class="form-row">
            <div>
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="stock_status">Stock status</label>
                <select id="stock_status" name="stock_status">
                    <?php foreach (['in_stock' => 'In stock', 'low_stock' => 'Low stock', 'out_of_stock' => 'Out of stock'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $product['stock_status'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <label for="description">Description</label>
        <textarea id="description" name="description"><?= htmlspecialchars($product['description']) ?></textarea>

        <label for="image">Replace photo (optional)</label>
        <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">

        <div class="form-row">
            <div>
                <label for="sizes">Sizes (comma-separated)</label>
                <input type="text" id="sizes" name="sizes" value="<?= htmlspecialchars($product['sizes'] ?? '') ?>">
            </div>
            <div>
                <label for="colors">Colours (comma-separated)</label>
                <input type="text" id="colors" name="colors" value="<?= htmlspecialchars($product['colors'] ?? '') ?>">
            </div>
        </div>

        <label for="base_price">Price (KES)</label>
        <input type="number" id="base_price" name="base_price" min="0" step="1" required value="<?= htmlspecialchars($product['base_price']) ?>">

        <h3 style="margin-top:28px; font-family:'Fraunces',serif;">Today's deal</h3>
        <p style="color:#5B5468; font-size:13px; margin-top:4px;">Update the discount daily — set new dates, or drop the percentage to 0 to switch it off.</p>

        <div class="form-row">
            <div>
                <label for="discount_percent">Discount %</label>
                <input type="number" id="discount_percent" name="discount_percent" min="0" max="90" step="1" value="<?= htmlspecialchars($product['discount_percent']) ?>">
            </div>
            <div></div>
        </div>
        <div class="form-row">
            <div>
                <label for="discount_start">Discount starts</label>
                <input type="date" id="discount_start" name="discount_start" value="<?= htmlspecialchars($product['discount_start'] ?? '') ?>">
            </div>
            <div>
                <label for="discount_end">Discount ends</label>
                <input type="date" id="discount_end" name="discount_end" value="<?= htmlspecialchars($product['discount_end'] ?? '') ?>">
            </div>
        </div>

        <label style="display:flex; align-items:center; gap:8px; margin-top:20px;">
            <input type="checkbox" name="is_featured" style="width:auto;" <?= $product['is_featured'] ? 'checked' : '' ?>> Feature on homepage
        </label>

        <div class="form-actions">
            <button type="submit" class="btn btn-magenta">Save changes</button>
            <a href="dashboard.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

    </main>
</div>
</body>
</html>