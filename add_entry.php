<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();

$errors = [];
$form = ['item_code' => '', 'item_name' => '', 'location' => '', 'quantity' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $errors[] = 'Session expired. Please try again.';
    } else {
        $form['item_code'] = trim($_POST['item_code'] ?? '');
        $form['item_name'] = trim($_POST['item_name'] ?? '');
        $form['location']  = trim($_POST['location'] ?? '');
        $form['quantity']  = trim($_POST['quantity'] ?? '');

        if ($form['item_code'] === '') $errors[] = 'Item code is required.';
        if ($form['item_name'] === '') $errors[] = 'Item name is required.';
        if ($form['location'] === '') $errors[] = 'Store location is required.';
        if ($form['quantity'] === '' || !is_numeric($form['quantity']) || (int)$form['quantity'] < 0) {
            $errors[] = 'Quantity must be a number of 0 or more.';
        }

        if (empty($errors)) {
            $db = get_db();
            $check = $db->prepare('SELECT id FROM inventory WHERE item_code = ?');
            $check->execute([$form['item_code']]);
            if ($check->fetch()) {
                $errors[] = 'That item code already exists. Use a unique code or edit the existing entry.';
            } else {
                $stmt = $db->prepare(
                    'INSERT INTO inventory (item_code, item_name, location, quantity, updated_by)
                     VALUES (?, ?, ?, ?, ?)'
                );
                $stmt->execute([
                    $form['item_code'],
                    $form['item_name'],
                    $form['location'],
                    (int)$form['quantity'],
                    current_user()['username'],
                ]);
                header('Location: index.php?ok=' . urlencode('Item added.'));
                exit;
            }
        }
    }
}

$pageTitle = 'Add item — Store Manager';
include __DIR__ . '/includes/header.php';
?>
<div class="form-wrap container-pad">
    <a href="index.php" class="back-link">&larr; Back to inventory</a>
    <h1 class="form-title">Add item</h1>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= htmlspecialchars($err) ?></div>
    <?php endforeach; ?>

    <form method="post" class="entry-form">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <label for="item_code">Item code</label>
        <input type="text" id="item_code" name="item_code" required placeholder="e.g. SKU-1004"
               value="<?= htmlspecialchars($form['item_code']) ?>">

        <label for="item_name">Item name</label>
        <input type="text" id="item_name" name="item_name" required placeholder="e.g. Roofing Nails (4 inch)"
               value="<?= htmlspecialchars($form['item_name']) ?>">

        <label for="location">Store location</label>
        <input type="text" id="location" name="location" required placeholder="e.g. Warehouse A - Rack 2"
               value="<?= htmlspecialchars($form['location']) ?>">

        <label for="quantity">Quantity</label>
        <input type="number" id="quantity" name="quantity" required min="0" step="1" placeholder="e.g. 50"
               value="<?= htmlspecialchars($form['quantity']) ?>">

        <button type="submit" class="btn btn-primary btn-block">Save item</button>
    </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
