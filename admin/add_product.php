<?php
require_once 'auth.php';

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
        $slug = slugify($name);
        // Ensure slug is unique
        $base = $slug; $i = 1;
        while (true) {
            $check = $pdo->prepare("SELECT id FROM products WHERE slug = ?");
            $check->execute([$slug]);
            if (!$check->fetch()) break;
            $slug = $base . '-' . (++$i);
        }

        // Handle image upload
        $imageName = null;
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageName = $slug . '-' . time() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/products/' . $imageName);
            } else {
                $error = 'Image must be a jpg, png or webp file.';
            }
        }

        if ($error === '') {
            $stmt = $pdo->prepare("
                INSERT INTO products (category_id, name, slug, description, base_price, discount_percent, discount_start, discount_end, sizes, colors, image, stock_status, is_featured)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$categoryId, $name, $slug, $description, $basePrice, $discountPercent, $discountStart, $discountEnd, $sizes, $colors, $imageName, $stockStatus, $isFeatured]);

            header('Location: dashboard.php?msg=' . urlencode('"' . $name . '" was added.'));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Product — Admin</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include 'admin-nav.php'; ?>

<div class="admin-topbar">
    <h1>Add product</h1>
    <a href="dashboard.php" class="btn btn-outline">← Back to products</a>
</div>

<?php if ($error): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card-panel">
    <form method="POST" enctype="multipart/form-data">
        <label for="name">Product name</label>
        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

        <div class="form-row">
            <div>
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="stock_status">Stock status</label>
                <select id="stock_status" name="stock_status">
                    <option value="in_stock">In stock</option>
                    <option value="low_stock">Low stock</option>
                    <option value="out_of_stock">Out of stock</option>
                </select>
            </div>
        </div>

        <label for="description">Description</label>
        <textarea id="description" name="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

        <label for="image">Product photo</label>
        <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">

        <div class="form-row">
            <div>
                <label for="sizes">Sizes (comma-separated)</label>
                <input type="text" id="sizes" name="sizes" placeholder="S, M, L, XL">
            </div>
            <div>
                <label for="colors">Colours (comma-separated)</label>
                <input type="text" id="colors" name="colors" placeholder="Black, Red, Navy">
            </div>
        </div>

        <label for="base_price">Price (KES)</label>
        <input type="number" id="base_price" name="base_price" min="0" step="1" required>

        <h3 style="margin-top:28px; font-family:'Fraunces',serif;">Today's deal (optional)</h3>
        <p style="color:#5B5468; font-size:13px; margin-top:4px;">Set a discount and the dates it should run. Leave dates blank for an open-ended discount, or set both to today for a one-day flash deal.</p>

        <div class="form-row">
            <div>
                <label for="discount_percent">Discount %</label>
                <input type="number" id="discount_percent" name="discount_percent" min="0" max="90" step="1" value="0">
            </div>
            <div></div>
        </div>
        <div class="form-row">
            <div>
                <label for="discount_start">Discount starts</label>
                <input type="date" id="discount_start" name="discount_start">
            </div>
            <div>
                <label for="discount_end">Discount ends</label>
                <input type="date" id="discount_end" name="discount_end">
            </div>
        </div>

        <label style="display:flex; align-items:center; gap:8px; margin-top:20px;">
            <input type="checkbox" name="is_featured" style="width:auto;"> Feature on homepage
        </label>

        <div class="form-actions">
            <button type="submit" class="btn btn-magenta">Save product</button>
            <a href="dashboard.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

    </main>
</div>
</body>
</html>