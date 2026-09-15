<?php
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        $del = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $del->execute([$id]);

        if ($product['image'] && file_exists(__DIR__ . '/../uploads/products/' . $product['image'])) {
            unlink(__DIR__ . '/../uploads/products/' . $product['image']);
        }
    }
}

header('Location: dashboard.php?msg=' . urlencode('Product deleted.'));
exit;