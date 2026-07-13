<?php
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/login.php');
}

try {
    verify_csrf();

    $email = normalizeEmail((string) ($_POST['email'] ?? ''));
    $password = trim((string) ($_POST['password'] ?? ''));
    $remember = isset($_POST['remember']);

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('Please enter a valid email address.');
    }
    if ($password === '' || mb_strlen($password) < 8) {
        throw new RuntimeException('Password must be at least 8 characters.');
    }

    $loginAttempt = new Model\LoginAttemptModel();
    $loginAttempt->recordAttempt($email, getClientIp());

    if ($loginAttempt->isLockedOut($email)) {
        throw new RuntimeException('Too many failed login attempts. Please try again in a few minutes.');
    }

    $userModel = new Model\UserModel();
    $userModel->ensureDefaultAdmin();
    $user = $userModel->findByEmail($email);
    if (!$user || !password_verify($password, $user['password_hash']) || $user['status'] !== 'active') {
        throw new RuntimeException('Invalid email or password.');
    }

    $loginAttempt->resetAttempts($email);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role_id'] = $user['role_id'];
    $role = (new Model\RoleModel())->findById((int) $user['role_id']);
    $_SESSION['role_name'] = strtolower((string) ($role['name'] ?? 'employee'));
    $_SESSION['last_activity'] = time();
    $_SESSION['success'] = 'Welcome back, ' . e($user['first_name']) . '!';

    error_log(sprintf('[DEBUG] authenticate.php login: user_id=%d role_id=%d role_name=%s', $_SESSION['user_id'], $_SESSION['role_id'], $_SESSION['role_name']));

    $employee = getCurrentEmployeeByUserEmail();
    if ($employee) {
        $_SESSION['employee_id'] = (int) $employee['id'];
    }

    if ($remember) {
        $token = generateToken(64);
        $rememberModel = new Model\RememberTokenModel();
        $rememberModel->createToken((int) $user['id'], $token, REMEMBER_ME_DURATION);
        setSecureCookie('remember_me', $token, REMEMBER_ME_DURATION);
    }

    redirect('/index.php');
} catch (Throwable $e) {
    $_SESSION['error'] = $e->getMessage();
    redirect('/login.php');
}
