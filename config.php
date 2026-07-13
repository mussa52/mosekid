<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? '',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

$envFile = APP_ROOT . '/config/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with($line, '#')) {
            continue;
        }
        [$name, $value] = array_pad(explode('=', $line, 2), 2, '');
        $name = trim($name);
        $value = trim($value);
        if ($name !== '') {
            putenv("{$name}={$value}");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'employee_management');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('APP_ENCRYPTION_KEY', getenv('APP_ENCRYPTION_KEY') ?: 'replace-this-with-a-strong-random-key');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost/employee');
define('SESSION_TIMEOUT', 1800);
define('REMEMBER_ME_DURATION', 604800);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_ATTEMPT_WINDOW', 900);
define('PASSWORD_RESET_EXPIRY', 3600);
define('SELF_REGISTRATION_ENABLED', true);

define('EMAIL_SENDER_NAME', getenv('EMAIL_SENDER_NAME') ?: 'Employee Management System');

define('EMAIL_SUPPORT_ADDRESS', getenv('EMAIL_SUPPORT_ADDRESS') ?: 'support@example.com');

spl_autoload_register(function (string $class): void {
    $prefixes = ['App\\' => APP_ROOT . '/classes/', 'Controller\\' => APP_ROOT . '/controllers/', 'Model\\' => APP_ROOT . '/models/'];
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }

    $defaultFiles = [
        APP_ROOT . '/classes/' . $class . '.php',
        APP_ROOT . '/controllers/' . $class . '.php',
        APP_ROOT . '/models/' . $class . '.php',
    ];
    foreach ($defaultFiles as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require_once APP_ROOT . '/includes/functions.php';

$roleModel = new \Model\RoleModel();
$roleModel->ensureDefaults();
