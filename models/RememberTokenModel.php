<?php
declare(strict_types=1);

namespace Model;

use App\BaseModel;

class RememberTokenModel extends BaseModel
{
    public function createToken(int $userId, string $token, int $duration): bool
    {
        $this->deleteTokensForUser($userId);

        $stmt = $this->db->prepare('INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (:user_id, :token_hash, :expires_at)');
        return $stmt->execute([
            'user_id' => $userId,
            'token_hash' => hash('sha256', $token),
            'expires_at' => date('Y-m-d H:i:s', time() + $duration),
        ]);
    }

    public function findByToken(string $token): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM remember_tokens WHERE token_hash = :token_hash AND expires_at > NOW() LIMIT 1');
        $stmt->execute(['token_hash' => hash('sha256', $token)]);
        $record = $stmt->fetch();
        return $record === false ? null : $record;
    }

    public function invalidateToken(string $token): bool
    {
        $stmt = $this->db->prepare('DELETE FROM remember_tokens WHERE token_hash = :token_hash');
        return $stmt->execute(['token_hash' => hash('sha256', $token)]);
    }

    private function deleteTokensForUser(int $userId): void
    {
        $stmt = $this->db->prepare('DELETE FROM remember_tokens WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
    }
}
