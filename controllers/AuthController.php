<?php
declare(strict_types=1);

namespace Controller;

use Model\UserModel;

class AuthController
{
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                verify_csrf();
                $email = trim((string) ($_POST['email'] ?? ''));
                $password = (string) ($_POST['password'] ?? '');
                $remember = isset($_POST['remember']);

                $this->validateLoginInput($email, $password);

                $model = new UserModel();
                $model->ensureDefaultAdmin();
                $loginAttempt = new \Model\LoginAttemptModel();
                $loginAttempt->recordAttempt($email, getClientIp());

                if ($loginAttempt->isLockedOut($email)) {
                    $_SESSION['error'] = 'Account locked due to too many failed login attempts. Please try again later.';
                } else {
                    $user = $model->findByEmail($email);
                    if ($user && password_verify($password, $user['password_hash']) && $user['status'] === 'active') {
                        $loginAttempt->resetAttempts($email);
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['role_id'] = $user['role_id'];
                        $role = (new \Model\RoleModel())->findById((int) $user['role_id']);
                        $roleName = strtolower((string) ($role['name'] ?? 'employee'));
                        $_SESSION['role_name'] = $roleName;
                        $_SESSION['last_activity'] = time();
                        $_SESSION['success'] = 'Welcome back, ' . e($user['first_name']) . '!';

                        error_log(sprintf('[DEBUG] AuthController login: user_id=%d role_id=%d role_name=%s', $_SESSION['user_id'], $_SESSION['role_id'], $_SESSION['role_name']));

                        // Link the logged-in user to an employee record if one exists.
                        $employee = getCurrentEmployeeByUserEmail();
                        if ($employee) {
                            $_SESSION['employee_id'] = (int) $employee['id'];
                        }

                        if ($remember) {
                            $token = generateToken(64);
                            $rememberModel = new \Model\RememberTokenModel();
                            $rememberModel->createToken((int) $user['id'], $token, REMEMBER_ME_DURATION);
                            setSecureCookie('remember_me', $token, REMEMBER_ME_DURATION);
                        }

                        redirect('/index.php?action=' . $this->getRedirectAction($roleName));
                    }
                    $_SESSION['error'] = 'Invalid email or password.';
                }
            } catch (\Throwable $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }

        include APP_ROOT . '/views/auth/login.php';
    }

    private function validateLoginInput(string $email, string $password): void
    {
        if ($email === '') {
            throw new \RuntimeException('Email address is required.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('Please enter a valid email address.');
        }
        if ($password === '') {
            throw new \RuntimeException('Password is required.');
        }
        if (mb_strlen($password) < 8) {
            throw new \RuntimeException('Password must be at least 8 characters.');
        }
    }

    private function getRedirectAction(string $roleName): string
    {
        return match (true) {
            in_array($roleName, ['admin', 'administrator'], true) => 'dashboard',
            in_array($roleName, ['manager', 'hr_manager', 'hr manager', 'hr-manager'], true) => 'hr-dashboard',
            in_array($roleName, ['payroll', 'payroll_officer', 'payroll officer', 'accounting'], true) => 'payroll-dashboard',
            default => 'employee-dashboard',
        };
    }

    public function logout(): void
    {
        destroySession();
        redirect('/login.php');
    }
}
