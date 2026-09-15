<?php $adminPage = basename($_SERVER['PHP_SELF']); ?>
<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="admin-logo">Nuru<span>Fashion</span></div>
        <nav class="admin-nav">
            <a href="dashboard.php" class="<?= $adminPage === 'dashboard.php' ? 'active' : '' ?>">Products</a>
            <a href="manage_categories.php" class="<?= $adminPage === 'manage_categories.php' ? 'active' : '' ?>">Categories</a>
            <a href="manage_admins.php" class="<?= $adminPage === 'manage_admins.php' ? 'active' : '' ?>">Admin users</a>
            <a href="../index.php" target="_blank">View storefront ↗</a>
            <a href="logout.php" style="margin-top:20px; color:#FF8FAE;">Log out</a>
        </nav>
    </aside>
    <main class="admin-main">