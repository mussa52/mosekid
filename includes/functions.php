<?php
declare(strict_types=1);

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function currentUserRoleName(): string {
    if (!empty($_SESSION['role_name'])) {
        $roleName = strtolower(trim((string) $_SESSION['role_name']));
        $roleName = match ($roleName) {
            'manager', 'hr manager', 'hr-manager', 'hr_manager' => 'hr_manager',
            'payroll officer', 'payroll', 'payroll_officer', 'payroll-officer' => 'payroll',
            default => $roleName,
        };
        $_SESSION['role_name'] = $roleName;
        return $roleName;
    }

    if (!empty($_SESSION['role_id'])) {
        $roleModel = new \Model\RoleModel();
        $role = $roleModel->findById((int) $_SESSION['role_id']);
        if ($role) {
            $roleName = strtolower(trim((string) $role['name']));
            $roleName = match ($roleName) {
                'manager', 'hr manager', 'hr-manager', 'hr_manager' => 'hr_manager',
                'payroll officer', 'payroll', 'payroll_officer', 'payroll-officer' => 'payroll',
                default => $roleName,
            };
            $_SESSION['role_name'] = $roleName;
            return $roleName;
        }
    }

    return 'employee';
}

function hasPermission(string $permission): bool {
    if (empty($_SESSION['user_id'])) {
        return false;
    }

    $roleName = currentUserRoleName();
    if (in_array($roleName, ['admin', 'administrator'], true)) {
        return true;
    }

    if (in_array($roleName, ['payroll', 'payroll officer', 'payroll_officer', 'payroll-officer'], true)
        && in_array($permission, ['payroll_manage', 'payroll_generate', 'payroll_action', 'reports_view', 'dashboard', 'profile_manage'], true)
    ) {
        return true;
    }

    $roleId = (int) ($_SESSION['role_id'] ?? 0);

    $roleModel = new \Model\RoleModel();
    return $roleModel->hasPermission($roleId, $permission);
}

function requirePermission(string $permission): void {
    if (!hasPermission($permission)) {
        $_SESSION['error'] = 'You do not have permission to access that resource.';
        redirect('/index.php');
    }
}

function getSidebarCounts(): array {
    $counts = [
        'Total Employees' => 0,
        'Total Users' => 0,
        'Departments' => 0,
        'Leave Requests' => 0,
        'Attendance Summary' => '0%',
    ];

    try {
        $db = \App\Database::getConnection();

        $counts['Total Employees'] = (int) $db->query('SELECT COUNT(*) FROM employees')->fetchColumn();
        $counts['Total Users'] = (int) $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $counts['Departments'] = (int) $db->query('SELECT COUNT(*) FROM departments')->fetchColumn();
        $counts['Leave Requests'] = (int) $db->query("SELECT COUNT(*) FROM leaves WHERE status = 'pending'")->fetchColumn();

        // Payroll summary removed from sidebar counts.

        $attendanceTotal = (int) $db->query('SELECT COUNT(*) FROM attendance WHERE attendance_date = CURDATE()')->fetchColumn();
        if ($attendanceTotal > 0) {
            $present = (int) $db->query("SELECT COUNT(*) FROM attendance WHERE attendance_date = CURDATE() AND status = 'present'")->fetchColumn();
            $counts['Attendance Summary'] = round(($present / $attendanceTotal) * 100) . '%';
        }
    } catch (\Throwable) {
        // Keep defaults if the dashboard counts cannot be loaded.
    }

    return $counts;
}

function getSidebarMenu(string $roleName = ''): array {
    $roleName = strtolower($roleName ?: currentUserRoleName());
    $roleName = $roleName === 'manager' ? 'hr_manager' : $roleName;

    $roleName = match ($roleName) {
        'manager', 'hr-manager', 'hr_manager', 'hr manager' => 'hr_manager',
        'payroll officer', 'payroll', 'payroll_officer', 'payroll-officer' => 'payroll',
        default => $roleName,
    };

    return match ($roleName) {
        'admin' => [
            ['label' => 'Employees', 'action' => 'employees', 'permission' => 'employees_manage'],
            ['label' => 'Users', 'action' => 'users', 'permission' => 'users_manage'],
            ['label' => 'Departments', 'action' => 'departments', 'permission' => 'departments_manage'],
            ['label' => 'Positions', 'action' => 'positions', 'permission' => 'positions_manage'],
            ['label' => 'Leave Requests', 'action' => 'leave', 'permission' => 'leave_manage'],
            // Payroll Summary removed from sidebar/menu
            ['label' => 'Attendance Summary', 'action' => 'attendance', 'permission' => 'attendance_manage'],
            ['label' => 'Logout', 'action' => 'logout', 'permission' => 'dashboard'],
        ],
        'hr_manager' => [
            ['label' => 'Employees', 'action' => 'employees', 'permission' => 'employees_manage'],
            ['label' => 'Departments', 'action' => 'departments', 'permission' => 'departments_manage'],
            ['label' => 'Positions', 'action' => 'positions', 'permission' => 'positions_manage'],
            ['label' => 'Attendance', 'action' => 'attendance', 'permission' => 'attendance_manage'],
            ['label' => 'Leave', 'action' => 'leave', 'permission' => 'leave_manage'],
            ['label' => 'Logout', 'action' => 'logout', 'permission' => 'dashboard'],
        ],
        'payroll' => [
            ['label' => 'Payroll', 'action' => 'payroll', 'permission' => 'payroll_manage'],
            ['label' => 'Salary Statistics', 'action' => 'payroll-stats', 'permission' => 'payroll_manage'],
            ['label' => 'Monthly Payments', 'action' => 'payroll-monthly', 'permission' => 'payroll_manage'],
            ['label' => 'Payslip Generation', 'action' => 'payroll-payslips', 'permission' => 'payroll_manage'],
            ['label' => 'Logout', 'action' => 'logout', 'permission' => 'dashboard'],
        ],
        default => [
            ['label' => 'My Profile', 'action' => 'employees-profile', 'permission' => 'profile_manage'],
            ['label' => 'Attendance', 'action' => 'attendance', 'permission' => 'attendance_manage'],
            ['label' => 'Leave', 'action' => 'leave', 'permission' => 'leave_manage'],
            ['label' => 'Logout', 'action' => 'logout', 'permission' => 'dashboard'],
        ],
    };
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function destroySession(): void {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    clearSecureCookie('remember_me');
    session_unset();
    session_destroy();
}

function enforceSessionTimeout(): void {
    if (!empty($_SESSION['last_activity']) && (time() - (int) $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        destroySession();
        redirect('/login.php?timeout=1');
    }
    $_SESSION['last_activity'] = time();
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
    enforceSessionTimeout();
}

function redirect(string $path): void {
    header('Location: ' . APP_URL . $path);
    exit;
}

function generateToken(int $length = 64): string {
    return bin2hex(random_bytes((int) ceil($length / 2)));
}

function setSecureCookie(string $name, string $value, int $duration): void {
    setcookie($name, $value, time() + $duration, '/', '', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'), true);
}

function clearSecureCookie(string $name): void {
    setcookie($name, '', time() - 3600, '/', '', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'), true);
}

function getClientIp(): string {
    foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = explode(',', $_SERVER[$key])[0];
            return trim($ip);
        }
    }
    return '0.0.0.0';
}

function normalizeEmail(string $email): string {
    return strtolower(trim($email));
}

function attemptRememberLogin(string $token): bool {
    $model = new \Model\RememberTokenModel();
    $record = $model->findByToken($token);
    if (!$record) {
        return false;
    }

    $userModel = new \Model\UserModel();
    $user = $userModel->findById((int) $record['user_id']);
    if (!$user || $user['status'] !== 'active') {
        $model->invalidateToken($token);
        return false;
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role_id'] = $user['role_id'];
    $role = (new \Model\RoleModel())->findById((int) $user['role_id']);
    $_SESSION['role_name'] = strtolower((string) ($role['name'] ?? 'employee'));
    $_SESSION['last_activity'] = time();

    $employee = getCurrentEmployeeByUserEmail();
    if ($employee) {
        $_SESSION['employee_id'] = (int) $employee['id'];
    }

    return true;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function getCurrentEmployeeId(): ?int {
    return !empty($_SESSION['employee_id']) ? (int) $_SESSION['employee_id'] : null;
}

function getCurrentEmployeeByUserEmail(): ?array {
    if (!empty($_SESSION['employee_id'])) {
        $employeeModel = new \Model\EmployeeModel();
        $employee = $employeeModel->findById((int) $_SESSION['employee_id']);
        if ($employee) {
            return $employee;
        }
    }

    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $userModel = new \Model\UserModel();
    $user = $userModel->findById((int) $_SESSION['user_id']);
    if (!$user) {
        return null;
    }

    $employeeModel = new \Model\EmployeeModel();
    $employee = null;

    if (!empty($user['email'])) {
        $employee = $employeeModel->findByEmail((string) $user['email']);
    }

    if (!$employee && !empty($user['first_name']) && !empty($user['last_name'])) {
        $employee = $employeeModel->findByName((string) $user['first_name'], (string) $user['last_name']);
    }

    if ($employee) {
    $_SESSION['employee_id'] = (int) $employee['id'];
}

return $employee;
}

function verify_csrf(): void {
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        throw new RuntimeException('Invalid CSRF token.');
    }
}

function parseDate(?string $date): ?string {
    return $date ? date('Y-m-d', strtotime($date)) : null;
}

function renderView(string $viewPath, array $data = [], string $title = 'Employee Management System', string $active = 'dashboard'): void
{
    $viewFile = APP_ROOT . '/views/' . $viewPath;
    if (!file_exists($viewFile)) {
        throw new RuntimeException('View not found: ' . $viewPath);
    }

    extract($data, EXTR_OVERWRITE);
    ob_start();
    include $viewFile;
    $content = ob_get_clean();

    include APP_ROOT . '/views/layout.php';
}
