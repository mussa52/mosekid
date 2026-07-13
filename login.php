<?php
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';

if (isset($_COOKIE['remember_me']) && !isLoggedIn()) {
    if (attemptRememberLogin((string) $_COOKIE['remember_me'])) {
        redirect('/index.php');
    }
}

if (isLoggedIn()) {
    redirect('/index.php');
}

$timeout = isset($_GET['timeout']);
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);

$pageTitle = 'Employee Management login';
ob_start();
?>
<div class="page-wrapper">
    <div class="login-card">
        <div class="logo-badge">
            <div class="icon"><i class="fas fa-users-cog"></i></div>
            <div>
                <div class="text-uppercase text-info fw-semibold small">Employee Management</div>
            </div>
        </div>

        <?php if ($timeout): ?>
            <div class="status-banner error">
                <strong>Session expired</strong>
                Your session has timed out. Please log in again.
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
    <div class="status-banner success">
        <div class="status-icon">
            <i class="fas fa-check"></i>
        </div>

        <div class="status-content">
            <h6>Success</h6>
            <p><?= e($success) ?></p>
        </div>
    </div>
<?php endif; ?>

        <?php if ($error): ?>
    <div class="status-banner error" id="login-error">
        <div class="status-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>

        <div class="status-content">
            <h6>Authentication error</h6>
            <p><?= e($error) ?></p>
        </div>
    </div>
        <?php else: ?>
            <div class="status-banner error d-none" id="login-error"></div>
        <?php endif; ?>

        <form id="login-form" method="post" action="authenticate.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <div class="form-group">
                <label for="email" class="form-label text-white mb-2">User Email</label>
                <input id="email" name="email" type="email" class="form-control" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label text-white mb-2">Password</label>
                <div class="input-with-icon">
                    <input id="password" name="password" type="password" class="form-control" placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" id="toggle-password" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-footer">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label text-white" for="remember">Remember Me</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mb-3">LOGIN</button>

            <?php if (SELF_REGISTRATION_ENABLED): ?>
                <a href="register.php" class="btn btn-secondary">REGISTER</a>
            <?php endif; ?>

            <p class="footer-note">© 2026 Employee Management System </p>
        </form>
    </div>
</div>
<?php
$pageContent = ob_get_clean();
include APP_ROOT . '/views/auth/layout.php';
