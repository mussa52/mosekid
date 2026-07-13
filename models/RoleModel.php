<?php
declare(strict_types=1);

namespace Model;

use App\BaseModel;

class RoleModel extends BaseModel
{
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM roles WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $role = $stmt->fetch();
        return $role === false ? null : $role;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM roles ORDER BY id');
        return $stmt->fetchAll();
    }

    public function hasPermission(int $roleId, string $permission): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM role_permissions rp JOIN permissions p ON p.id = rp.permission_id WHERE rp.role_id = :role_id AND p.permission_name = :permission LIMIT 1');
        $stmt->execute(['role_id' => $roleId, 'permission' => $permission]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function ensureDefaults(): void
    {
        // Do not auto-create an `accountant` role by default.
        $this->db->exec("INSERT IGNORE INTO roles (id, name, description) VALUES (1, 'admin', 'Administrator'), (2, 'manager', 'HR Manager'), (4, 'employee', 'Employee')");
        $this->db->exec("CREATE TABLE IF NOT EXISTS permissions (id INT AUTO_INCREMENT PRIMARY KEY, permission_name VARCHAR(100) NOT NULL UNIQUE, module VARCHAR(100) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
        $this->db->exec("CREATE TABLE IF NOT EXISTS role_permissions (id INT AUTO_INCREMENT PRIMARY KEY, role_id INT NOT NULL, permission_id INT NOT NULL, FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE, FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE)");

        $permissions = [
            ['permission_name' => 'dashboard', 'module' => 'dashboard'],
            ['permission_name' => 'users_manage', 'module' => 'users'],
            ['permission_name' => 'users_edit', 'module' => 'users'],
            ['permission_name' => 'users_delete', 'module' => 'users'],
            ['permission_name' => 'users_approve', 'module' => 'users'],
            ['permission_name' => 'roles_manage', 'module' => 'roles'],
            ['permission_name' => 'permissions_manage', 'module' => 'permissions'],
            ['permission_name' => 'employees_manage', 'module' => 'employees'],
            ['permission_name' => 'departments_manage', 'module' => 'departments'],
            ['permission_name' => 'positions_manage', 'module' => 'positions'],
            ['permission_name' => 'attendance_manage', 'module' => 'attendance'],
            ['permission_name' => 'leave_manage', 'module' => 'leave'],
            ['permission_name' => 'payroll_manage', 'module' => 'payroll'],
            ['permission_name' => 'payroll_generate', 'module' => 'payroll'],
            ['permission_name' => 'payroll_action', 'module' => 'payroll'],
            ['permission_name' => 'reports_view', 'module' => 'reports'],
            ['permission_name' => 'profile_manage', 'module' => 'profile'],
        ];

        foreach ($permissions as $permission) {
            $stmt = $this->db->prepare('INSERT IGNORE INTO permissions (permission_name, module) VALUES (:permission_name, :module)');
            $stmt->execute($permission);
        }

        $this->db->exec("CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_login_attempts_email (email),
            INDEX idx_login_attempts_time (attempted_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->exec("CREATE TABLE IF NOT EXISTS remember_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token_hash VARCHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_remember_tokens_hash (token_hash),
            INDEX idx_remember_tokens_user (user_id),
            CONSTRAINT fk_remember_tokens_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->assignDefaultPermissions();
    }

    public function assignDefaultPermissions(): void
    {
        $rolePermissions = [
            1 => ['dashboard', 'users_manage', 'users_edit', 'users_delete', 'users_approve', 'roles_manage', 'permissions_manage', 'employees_manage', 'departments_manage', 'positions_manage', 'attendance_manage', 'leave_manage', 'payroll_manage', 'payroll_generate', 'payroll_action', 'reports_view', 'profile_manage'],
            2 => ['dashboard', 'users_manage', 'users_edit', 'users_approve', 'employees_manage', 'departments_manage', 'positions_manage', 'attendance_manage', 'leave_manage', 'reports_view', 'profile_manage'],
            // Do not assign a dedicated accountant role by default. Employees retain limited payroll access through permissions.
            4 => ['dashboard', 'profile_manage', 'attendance_manage', 'leave_manage', 'payroll_manage'],
        ];

        foreach ($rolePermissions as $roleId => $permissions) {
            foreach ($permissions as $permissionName) {
                $permissionStmt = $this->db->prepare('SELECT id FROM permissions WHERE permission_name = :permission_name LIMIT 1');
                $permissionStmt->execute(['permission_name' => $permissionName]);
                $permissionId = $permissionStmt->fetchColumn();
                if ($permissionId) {
                    $assignStmt = $this->db->prepare('INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)');
                    $assignStmt->execute(['role_id' => $roleId, 'permission_id' => $permissionId]);
                }
            }
        }
    }
}
