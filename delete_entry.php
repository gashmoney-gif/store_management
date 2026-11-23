<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_check()) {
    header('Location: index.php?error=' . urlencode('Invalid request.'));
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if ($id > 0) {
    $stmt = get_db()->prepare('DELETE FROM inventory WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: index.php?ok=' . urlencode('Item deleted.'));
    exit;
}

header('Location: index.php?error=' . urlencode('Item not found.'));
exit;
