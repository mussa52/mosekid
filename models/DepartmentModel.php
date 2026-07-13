<?php
declare(strict_types=1);

namespace Model;

use App\BaseModel;
use App\CrudRepository;

class DepartmentModel extends BaseModel implements CrudRepository
{
    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM departments ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM departments WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByName(string $name): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM departments WHERE name = :name');
        $stmt->execute(['name' => $name]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool
    {
        $existing = $this->findByName($data['name']);
        if ($existing !== null) {
            throw new \RuntimeException('A department with that name already exists.');
        }

        $stmt = $this->db->prepare('INSERT INTO departments (name, description) VALUES (:name, :description)');
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $existing = $this->findByName($data['name']);
        if ($existing !== null && (int)$existing['id'] !== $id) {
            throw new \RuntimeException('A department with that name already exists.');
        }

        $stmt = $this->db->prepare('UPDATE departments SET name = :name, description = :description WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function delete(int $id): bool
{
    // Check if the department has employees
    $check = $this->db->prepare(
        'SELECT COUNT(*) FROM employees WHERE department_id = :id'
    );
    $check->execute(['id' => $id]);

    if ((int)$check->fetchColumn() > 0) {
        throw new \RuntimeException(
            'This department cannot be deleted because employees are assigned to it.'
        );
    }

    $stmt = $this->db->prepare(
        'DELETE FROM departments WHERE id = :id'
    );

    return $stmt->execute(['id' => $id]);
}

    public function search(string $term): array
    {
        $stmt = $this->db->prepare('SELECT * FROM departments WHERE name LIKE :term OR description LIKE :term ORDER BY name ASC');
        $stmt->execute(['term' => '%' . $term . '%']);
        return $stmt->fetchAll();
    }

    public function getDefaultDepartmentId(): int
{
    $stmt = $this->db->query('SELECT id FROM departments ORDER BY id ASC LIMIT 1');
    $row = $stmt->fetch();

    if (!$row) {
        throw new \RuntimeException(
            'No department found. Please create a department first.'
        );
    }

    return (int) $row['id'];
}
}
