<?php
declare(strict_types=1);

namespace Model;

use App\BaseModel;
use App\CrudRepository;

class PositionModel extends BaseModel implements CrudRepository
{
    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM positions ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM positions WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findFirstId(): ?int
    {
        $stmt = $this->db->query('SELECT id FROM positions ORDER BY id ASC LIMIT 1');
        $row = $stmt->fetch();
        return $row ? (int)$row['id'] : null;
    }

    public function findByName(string $name): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM positions WHERE name = :name');
        $stmt->execute(['name' => $name]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare('INSERT INTO positions (name, description) VALUES (:name, :description)');
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare('UPDATE positions SET name = :name, description = :description WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM positions WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function search(string $term): array
    {
        $stmt = $this->db->prepare('SELECT * FROM positions WHERE name LIKE :term OR description LIKE :term ORDER BY name ASC');
        $stmt->execute(['term' => '%' . $term . '%']);
        return $stmt->fetchAll();
    }
}
