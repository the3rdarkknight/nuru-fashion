<?php
// Expects $pdo to already be available (config/database.php included by the calling page).
$navCategories = $pdo->query("SELECT id, name, slug FROM categories ORDER BY sort_order ASC")->fetchAll();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? e($pageTitle) . ' — ' . SITE_NAME : SITE_NAME . ' | Women\'s Fashion, Nairobi' ?></title>
<meta name="description" content="Everyday and statement women's fashion in Nairobi. New arrivals, daily deals, and order straight to WhatsApp for delivery.">
<link rel="stylesheet" href="<?= (strpos($currentPage, 'admin') !== false ? '' : '') ?>css/style.css">
</head>
<body>

<div class="topbar">
    <div class="container">
        <span>Free delivery within Nairobi CBD on orders over <?= formatPrice(5000) ?></span>
        <a href="<?= e(whatsAppGeneralLink()) ?>" target="_blank" rel="noopener">Chat with us on WhatsApp →</a>
    </div>
</div>

<header class="site-header">
    <div class="container">
        <a href="index.php" class="logo">Nuru<span>Fashion</span></a>

        <nav class="main-nav">
            <a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Home</a>
            <?php foreach ($navCategories as $cat): ?>
                <a href="category.php?slug=<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="header-cta">
            <a href="<?= e(whatsAppGeneralLink()) ?>" target="_blank" rel="noopener" class="btn-whatsapp">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.2h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2Z" opacity=".18"/><path d="M12.04 20.13a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.18 8.18 0 0 1-1.26-4.36c0-4.53 3.69-8.22 8.24-8.22 2.2 0 4.27.86 5.82 2.42a8.17 8.17 0 0 1 2.41 5.81c0 4.54-3.69 8.21-8.23 8.21Zm4.51-6.15c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.13-.17.24-.64.8-.78.97-.14.16-.29.18-.54.06-.25-.12-1.04-.38-1.98-1.22-.73-.65-1.22-1.45-1.37-1.7-.14-.24-.02-.37.11-.5.11-.11.25-.29.37-.43.12-.15.16-.25.24-.41.08-.16.04-.31-.02-.43-.06-.12-.56-1.35-.77-1.85-.2-.48-.41-.42-.56-.42-.14-.01-.31-.01-.48-.01-.16 0-.43.06-.66.31-.23.24-.86.85-.86 2.06s.89 2.39 1.01 2.56c.12.16 1.75 2.67 4.24 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.08.14-1.18-.06-.11-.22-.17-.47-.29Z"/></svg>
                <span class="full">Order on WhatsApp</span>
            </a>
            <button class="mobile-toggle" aria-label="Menu">☰</button>
        </div>
    </div>
</header>