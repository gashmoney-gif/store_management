<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $error = 'Session expired. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Enter both username and password.';
        } else {
            $stmt = get_db()->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id'        => $user['id'],
                    'username'  => $user['username'],
                    'full_name' => $user['full_name'],
                    'role'      => $user['role'],
                ];
                header('Location: index.php');
                exit;
            }
            $error = 'Incorrect username or password.';
        }
    }
}

$pageTitle = 'Log in — Store Manager';
include __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-mark">S</div>
        <h1 class="auth-title">Store Manager</h1>
        <p class="auth-sub">Log in to check stock levels and locations.</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn btn-primary btn-block">Log in</button>
        </form>

        <p class="auth-hint">
            Default accounts — admin: <code>admin / Admin123!</code>,
            viewer: <code>viewer / Viewer123!</code>. Change these after setup.
        </p>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
