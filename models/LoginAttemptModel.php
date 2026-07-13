<?php
declare(strict_types=1);

namespace Model;

use App\BaseModel;

class LoginAttemptModel extends BaseModel
{
    public function recordAttempt(string $email, string $ipAddress): void
    {
        $stmt = $this->db->prepare('INSERT INTO login_attempts (email, ip_address, attempted_at) VALUES (:email, :ip_address, NOW())');
        $stmt->execute([
            'email' => strtolower(trim($email)),
            'ip_address' => $ipAddress,
        ]);
    }

    public function isLockedOut(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM login_attempts WHERE email = :email AND attempted_at > DATE_SUB(NOW(), INTERVAL :window SECOND)');
        $stmt->execute(['email' => strtolower(trim($email)), 'window' => LOGIN_ATTEMPT_WINDOW]);
        return (int)$stmt->fetchColumn() >= MAX_LOGIN_ATTEMPTS;
    }

    public function resetAttempts(string $email): void
    {
        $stmt = $this->db->prepare('DELETE FROM login_attempts WHERE email = :email');
        $stmt->execute(['email' => strtolower(trim($email))]);
    }
}
