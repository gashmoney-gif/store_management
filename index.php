<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$q = trim($_GET['q'] ?? '');
$db = get_db();

if ($q !== '') {
    $stmt = $db->prepare(
        'SELECT * FROM inventory
         WHERE item_code LIKE :q OR item_name LIKE :q OR location LIKE :q
         ORDER BY item_name ASC'
    );
    $stmt->execute(['q' => '%' . $q . '%']);
} else {
    $stmt = $db->query('SELECT * FROM inventory ORDER BY item_name ASC');
}
$rows = $stmt->fetchAll();

$pageTitle = 'Inventory — Store Manager';
include __DIR__ . '/includes/header.php';
?>

<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-error container-pad"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>
<?php if (!empty($_GET['ok'])): ?>
    <div class="alert alert-ok container-pad"><?= htmlspecialchars($_GET['ok']) ?></div>
<?php endif; ?>

<div class="search-bar container-pad">
    <form method="get" class="search-form" role="search">
        <input
            type="search"
            name="q"
            placeholder="Search by code, item, or location"
            value="<?= htmlspecialchars($q) ?>"
            aria-label="Search inventory"
        >
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($q !== ''): ?>
            <a href="index.php" class="btn btn-ghost">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="list-meta container-pad">
    <span><?= count($rows) ?> item<?= count($rows) === 1 ? '' : 's' ?><?= $q !== '' ? ' found for "' . htmlspecialchars($q) . '"' : ' in stock' ?></span>
</div>

<div class="item-list container-pad">
    <?php if (empty($rows)): ?>
        <div class="empty-state">
            <p>No matching items.</p>
            <p class="muted">Try a different code, item name, or location.</p>
        </div>
    <?php else: ?>
        <?php foreach ($rows as $row): ?>
            <div class="item-card">
                <div class="item-card-top">
                    <span class="item-code"><?= htmlspecialchars($row['item_code']) ?></span>
                    <span class="item-qty <?= $row['quantity'] <= 0 ? 'qty-zero' : ($row['quantity'] < 10 ? 'qty-low' : '') ?>">
                        <?= (int)$row['quantity'] ?> pcs
                    </span>
                </div>
                <div class="item-name"><?= htmlspecialchars($row['item_name']) ?></div>
                <div class="item-location">📍 <?= htmlspecialchars($row['location']) ?></div>
                <div class="item-meta">Updated <?= htmlspecialchars(date('d M Y, H:i', strtotime($row['updated_at']))) ?><?= $row['updated_by'] ? ' by ' . htmlspecialchars($row['updated_by']) : '' ?></div>

                <?php if (is_admin()): ?>
                    <div class="item-actions">
                        <a href="edit_entry.php?id=<?= (int)$row['id'] ?>" class="btn btn-small btn-outline">Edit</a>
                        <form method="post" action="delete_entry.php" onsubmit="return confirm('Delete this item?');">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                            <button type="submit" class="btn btn-small btn-danger">Delete</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (is_admin()): ?>
    <a href="add_entry.php" class="fab" aria-label="Add new item">+</a>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
