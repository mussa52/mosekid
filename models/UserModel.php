<?php
declare(strict_types=1);

namespace Model;

use App\BaseModel;
use App\Encryption;

class UserModel extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        $normalizedEmail = strtolower(trim($email));
        $encryption = new Encryption();

        $allUsers = $this->db->query('SELECT * FROM users ORDER BY id');
        while ($row = $allUsers->fetch()) {
            $candidateEmail = $encryption->decrypt($row['email']);
            $candidateEmail = strtolower(trim((string) $candidateEmail));
            $rawEmail = strtolower(trim((string) $row['email']));

            if ($candidateEmail === $normalizedEmail || $rawEmail === $normalizedEmail) {
                $row['first_name'] = $encryption->decrypt($row['first_name']);
                $row['last_name'] = $encryption->decrypt($row['last_name']);
                $row['email'] = $candidateEmail !== '' ? $candidateEmail : $rawEmail;
                return $row;
            }
        }

        return null;
    }

    public function ensureDefaultAdmin(): void
    {
        $this->ensureRoleUser(1, 'System', 'Admin', 'admin@example.com', 'password123');
        $this->ensureRoleUser(2, 'HR', 'Manager', 'hr@example.com', 'password123');
        // Accountant default user removed per project configuration.
        $this->ensureRoleUser(3, 'Employee', 'User', 'employee@example.com', 'password123');
    }

    public function ensureRoleUser(int $roleId, string $firstName, string $lastName, string $email, string $password): void
    {
        $existingUser = $this->findByEmail($email);
        $encryption = new Encryption();
        $normalizedEmail = strtolower(trim($email));

        if ($existingUser !== null) {
            $update = $this->db->prepare('UPDATE users SET role_id = :role_id, first_name = :first_name, last_name = :last_name, email = :email, password_hash = :password_hash, status = :status WHERE id = :id');
            $update->execute([
                'role_id' => $roleId,
                'first_name' => $encryption->encrypt($firstName),
                'last_name' => $encryption->encrypt($lastName),
                'email' => $encryption->encrypt($normalizedEmail),
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'status' => 'active',
                'id' => $existingUser['id'],
            ]);
            return;
        }

        $insert = $this->db->prepare('INSERT INTO users (role_id, first_name, last_name, email, password_hash, status) VALUES (:role_id, :first_name, :last_name, :email, :password_hash, :status)');
        $insert->execute([
            'role_id' => $roleId,
            'first_name' => $encryption->encrypt($firstName),
            'last_name' => $encryption->encrypt($lastName),
            'email' => $encryption->encrypt($normalizedEmail),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'status' => 'active',
        ]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        if ($user === false) {
            return null;
        }

        $encryption = new Encryption();
        $user['first_name'] = $encryption->decrypt($user['first_name']);
        $user['last_name'] = $encryption->decrypt($user['last_name']);
        $user['email'] = $encryption->decrypt($user['email']);
        return $user;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT u.*, r.name AS role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC');
        $rows = $stmt->fetchAll();
        $encryption = new Encryption();

        foreach ($rows as &$row) {
            $row['first_name'] = $encryption->decrypt($row['first_name']);
            $row['last_name'] = $encryption->decrypt($row['last_name']);
            $row['email'] = $encryption->decrypt($row['email']);
            $row['role_name'] = $row['role_name'] ?? 'Unknown';
        }

        return $rows;
    }

    public function updateLastLogin(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function updatePassword(int $id, string $password): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = :password_hash, updated_at = NOW() WHERE id = :id');
        return $stmt->execute([ 'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'id' => $id ]);
    }

    public function update(int $id, array $data): bool
    {
        $encryption = new Encryption();
        $sql = 'UPDATE users SET role_id = :role_id, first_name = :first_name, last_name = :last_name, email = :email, status = :status, updated_at = NOW() WHERE id = :id';
        $params = [
            'role_id' => $data['role_id'],
            'first_name' => $encryption->encrypt($data['first_name']),
            'last_name' => $encryption->encrypt($data['last_name']),
            'email' => $encryption->encrypt(strtolower(trim($data['email']))),
            'status' => $data['status'],
            'id' => $id,
        ];

        $result = $this->db->prepare($sql)->execute($params);

        if ($result && !empty($data['password'])) {
            return $this->updatePassword($id, $data['password']);
        }

        return (bool) $result;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function create(array $data): bool
    {
        $encryption = new Encryption();
        $stmt = $this->db->prepare('INSERT INTO users (role_id, first_name, last_name, email, password_hash, status) VALUES (:role_id, :first_name, :last_name, :email, :password_hash, :status)');
        return $stmt->execute([
            'role_id' => $data['role_id'],
            'first_name' => $encryption->encrypt($data['first_name']),
            'last_name' => $encryption->encrypt($data['last_name']),
            'email' => $encryption->encrypt(strtolower(trim($data['email']))),
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'status' => $data['status'] ?? 'active',
        ]);
    }
}
