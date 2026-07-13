<?php
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';

if (!SELF_REGISTRATION_ENABLED) {
    redirect('/login.php');
}

$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verify_csrf();
        $firstName = trim((string) ($_POST['first_name'] ?? ''));
        $lastName = trim((string) ($_POST['last_name'] ?? ''));
        $email = normalizeEmail((string) ($_POST['email'] ?? ''));
        $password = trim((string) ($_POST['password'] ?? ''));
        $confirmPassword = trim((string) ($_POST['confirm_password'] ?? ''));

        if ($firstName === '' || $lastName === '' || $email === '' || $password === '' || $confirmPassword === '') {
            throw new RuntimeException('All fields are required.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Please enter a valid email address.');
        }
        if ($password !== $confirmPassword) {
            throw new RuntimeException('Passwords do not match.');
        }
        if (mb_strlen($password) < 8) {
            throw new RuntimeException('Password must be at least 8 characters.');
        }

        $userModel = new Model\UserModel();
        if ($userModel->findByEmail($email) !== null) {
            throw new RuntimeException('An account with that email already exists.');
        }

        $userModel->create([
            'role_id' => 3,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password,
            'status' => 'active',
        ]);

        $_SESSION['success'] = 'Your account has been created successfully. Please login.';
        redirect('/login.php');
    } catch (Throwable $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: register.php');
        exit;
    }
}

$pageTitle = 'Register | Employee Management';
ob_start();
?>
<div class="page-wrapper">
    <div class="login-card">
        <div class="logo-badge">
            <div class="icon"><i class="fas fa-user-plus"></i></div>
            <div>
                <h1 class="login-title">Create Account</h1>
            
            </div>
        </div>

        <?php if ($success): ?>
            <div class="status-banner success"><strong>Success</strong> <?= e($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="status-banner error"><strong>Error</strong> <?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="register.php">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-group">
                <label for="first_name" class="form-label text-white mb-2">First name</label>
                <input id="first_name" name="first_name" type="text" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="last_name" class="form-label text-white mb-2">Last name</label>
                <input id="last_name" name="last_name" type="text" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email" class="form-label text-white mb-2">Email address</label>
                <input id="email" name="email" type="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label text-white mb-2">Password</label>
                <input id="password" name="password" type="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="confirm_password" class="form-label text-white mb-2">Confirm password</label>
                <input id="confirm_password" name="confirm_password" type="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary mb-3">Register</button>
            <a href="login.php" class="forgot-link">Back to login</a>
            <p class="footer-note">© 2026 Employee Management System </p>
        </form>
    </div>
</div>
<?php
$pageContent = ob_get_clean();
include APP_ROOT . '/views/auth/layout.php';
