<?php
require_once 'auth.php';

$error = '';

// Create a new admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || strlen($password) < 6) {
        $error = 'Enter a username and a password of at least 6 characters.';
    } else {
        $check = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            $error = 'That username is already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)");
            $stmt->execute([$username, $hash]);
            header('Location: manage_admins.php?msg=' . urlencode('Admin "' . $username . '" was created.'));
            exit;
        }
    }
}

// Delete an admin (never allow deleting yourself, and never allow deleting the last remaining admin)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $total = (int)$pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();

    if ($id === (int)$_SESSION['admin_id']) {
        $error = "You can't delete the account you're currently logged in as.";
    } elseif ($total <= 1) {
        $error = 'At least one admin account must remain.';
    } else {
        $del = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
        $del->execute([$id]);
        header('Location: manage_admins.php?msg=' . urlencode('Admin account removed.'));
        exit;
    }
}

$admins = $pdo->query("SELECT * FROM admin_users ORDER BY created_at ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Users — Admin</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include 'admin-nav.php'; ?>

<div class="admin-topbar">
    <h1>Admin users</h1>
</div>

<p style="color:#5B5468; font-size:14px; margin-top:-14px; margin-bottom:24px;">
    Everyone listed here has full access to the admin panel — products, categories and other admin accounts. There are no separate permission levels; only add people you'd trust with the whole store.
</p>

<?php if (!empty($_GET['msg'])): ?>
    <div class="alert success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="stat-row" style="grid-template-columns: 1fr;">
    <div class="stat-card"><div class="num"><?= count($admins) ?></div><div class="label">Admin accounts</div></div>
</div>

<div class="card-panel" style="margin-bottom:24px;">
    <h3 style="font-family:'Fraunces',serif; margin-top:0;">Add a new admin</h3>
    <form method="POST">
        <input type="hidden" name="action" value="create">
        <div class="form-row">
            <div>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-magenta">Create admin account</button>
        </div>
    </form>
</div>

<div class="card-panel">
    <table>
        <thead><tr><th>Username</th><th>Created</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($admins as $admin): ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($admin['username']) ?></strong>
                    <?php if ($admin['id'] == $_SESSION['admin_id']): ?>
                        <span class="pill active" style="margin-left:6px;">You</span>
                    <?php endif; ?>
                </td>
                <td><?= date('d M Y', strtotime($admin['created_at'])) ?></td>
                <td>
                    <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                        <a href="manage_admins.php?delete=<?= $admin['id'] ?>" class="btn btn-red btn-small" onclick="return confirm('Remove this admin account?');">Delete</a>
                    <?php endif; ?>
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