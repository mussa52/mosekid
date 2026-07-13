<?php
declare(strict_types=1);

namespace App;

interface CrudRepository
{
    public function findAll(): array;
    public function findById(int $id): ?array;
    public function create(array $data): bool;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
