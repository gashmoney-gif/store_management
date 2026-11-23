<?php
/** Expects $pageTitle to be set before including this file. */
$u = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title><?= htmlspecialchars($pageTitle ?? 'Store Manager') ?></title>
<link rel="stylesheet" href="assets/css/style.css">
<meta name="theme-color" content="#1B2430">
</head>
<body>
<?php if ($u): ?>
<header class="topbar">
    <div class="topbar-brand">
        <span class="brand-mark">S</span>
        <span class="brand-name">Store Manager</span>
    </div>
    <div class="topbar-user">
        <span class="role-pill role-<?= htmlspecialchars($u['role']) ?>"><?= htmlspecialchars(ucfirst($u['role'])) ?></span>
        <span class="user-name"><?= htmlspecialchars($u['full_name']) ?></span>
        <a href="logout.php" class="btn-logout">Log out</a>
    </div>
</header>
<?php endif; ?>
<main class="app-main">
