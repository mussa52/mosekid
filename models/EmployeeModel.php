<?php
declare(strict_types=1);

namespace Model;

use App\BaseModel;
use App\CrudRepository;
use App\Encryption;
use Model\DepartmentModel;

class EmployeeModel extends BaseModel implements CrudRepository
{
    public function findAll(): array
{
    $stmt = $this->db->query("
        SELECT
            e.*,
            d.name AS department_name,
            p.name AS position_name
        FROM employees e
        LEFT JOIN departments d ON e.department_id = d.id
        LEFT JOIN positions p ON e.position_id = p.id
        ORDER BY e.last_name ASC, e.first_name ASC
    ");

    return $this->decryptRows($stmt->fetchAll());
}

    public function findActive(): array
    {
        $stmt = $this->db->query('SELECT * FROM employees WHERE LOWER(TRIM(status)) = \'active\' ORDER BY last_name ASC, first_name ASC');
        return $this->decryptRows($stmt->fetchAll());
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM employees WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->decryptRow($row) : null;
    }

    public function findByEmail(string $email): ?array
    {
        $normalizedEmail = strtolower(trim($email));
        $rows = $this->findAll();

        foreach ($rows as $row) {
            if (strtolower(trim((string) ($row['email'] ?? ''))) === $normalizedEmail) {
                return $row;
            }
        }

        return null;
    }

    public function findByName(string $firstName, string $lastName): ?array
    {
        $normalizedFirst = strtolower(trim($firstName));
        $normalizedLast = strtolower(trim($lastName));
        $rows = $this->findAll();

        foreach ($rows as $row) {
            if (strtolower(trim((string) ($row['first_name'] ?? ''))) === $normalizedFirst && strtolower(trim((string) ($row['last_name'] ?? ''))) === $normalizedLast) {
                return $row;
            }
        }

        return null;
    }


    public function create(array $data): bool
    {
        $encryption = new Encryption();
        $stmt = $this->db->prepare('INSERT INTO employees (
department_id,
position_id,
employee_code,
first_name,
last_name,
gender,
date_of_birth,
national_id,
phone_number,
email,
address,
salary,
employment_date,
status,
profile_image
)
VALUES (
:department_id,
:position_id,
:employee_code,
:first_name,
:last_name,
:gender,
:date_of_birth,
:national_id,
:phone_number,
:email,
:address,
:salary,
:employment_date,
:status,
:profile_image
)');
        $positionId = $data['position_id'] ?? $this->getDefaultPositionId();
        if ($positionId === null) {
            throw new \RuntimeException('Please create a position before adding an employee.');
        }

        return $stmt->execute([
            'department_id' => $data['department_id'],
            'position_id' => $positionId,
            'employee_code' => $data['employee_code'],
            'first_name' => $encryption->encrypt($data['first_name']),
            'last_name' => $encryption->encrypt($data['last_name']),
            'gender' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'],
            'national_id' => $encryption->encrypt($data['national_id']),
            'phone_number' => $encryption->encrypt($data['phone_number']),
            'email' => $encryption->encrypt($data['email']),
            'address' => $encryption->encrypt($data['address']),
            'salary' => $data['salary'],
            'employment_date' => $data['employment_date'] ?? date('Y-m-d'),
            'status' => $data['status'] ?? 'active',
'profile_image' => $data['profile_image'] ?? null,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $encryption = new Encryption();
        $positionId = $data['position_id'] ?? $this->getDefaultPositionId();
        if ($positionId === null) {
            throw new \RuntimeException('Please create a position before updating an employee.');
        }

        $stmt = $this->db->prepare('UPDATE employees SET department_id = :department_id, position_id = :position_id, employee_code = :employee_code, first_name = :first_name, last_name = :last_name, gender = :gender, date_of_birth = :date_of_birth, national_id = :national_id, phone_number = :phone_number, email = :email, address = :address, salary = :salary, employment_date = :employment_date, status = :status WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'department_id' => $data['department_id'],
            'position_id' => $positionId,
            'employee_code' => $data['employee_code'],
            'first_name' => $encryption->encrypt($data['first_name']),
            'last_name' => $encryption->encrypt($data['last_name']),
            'gender' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'],
            'national_id' => $encryption->encrypt($data['national_id']),
            'phone_number' => $encryption->encrypt($data['phone_number']),
            'email' => $encryption->encrypt($data['email']),
            'address' => $encryption->encrypt($data['address']),
            'salary' => $data['salary'],
            'employment_date' => $data['employment_date'] ?? date('Y-m-d'),
            'status' => $data['status'] ?? 'active',
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM employees WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function search(string $term): array
    {
        // Many fields are stored encrypted (first_name, last_name, email, phone_number, address).
        // Searching encrypted columns with SQL LIKE won't match plaintext terms. To support
        // searching by name/email/phone, fetch rows, decrypt them, and filter in PHP.

        $sql = "SELECT e.*, d.name AS department_name, p.name AS position_name
                FROM employees e
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN positions p ON e.position_id = p.id
                ORDER BY e.last_name ASC, e.first_name ASC";

        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll();
        $rows = $this->decryptRows($rows);

        $term = trim((string)$term);
        if ($term === '') {
            return $rows;
        }

        $termLower = mb_strtolower($term);
        $filtered = [];
        foreach ($rows as $row) {
            $haystack = (
                ($row['employee_code'] ?? '') . ' ' .
                ($row['first_name'] ?? '') . ' ' .
                ($row['last_name'] ?? '') . ' ' .
                ($row['email'] ?? '') . ' ' .
                ($row['phone_number'] ?? '') . ' ' .
                ($row['department_name'] ?? '') . ' ' .
                ($row['position_name'] ?? '')
            );
            if (mb_stripos($haystack, $term) !== false || mb_stripos($haystack, $termLower) !== false) {
                $filtered[] = $row;
            }
        }

        return $filtered;
    }

    public function findPresentToday(): array
    {
        $stmt = $this->db->prepare('SELECT e.* FROM employees e JOIN attendance a ON a.employee_id = e.id WHERE a.attendance_date = CURDATE() AND a.status = :status ORDER BY e.last_name ASC, e.first_name ASC');
        $stmt->execute(['status' => 'present']);
        return $this->decryptRows($stmt->fetchAll());
    }

    public function getDefaultPositionId(): ?int
    {
        $stmt = $this->db->query('SELECT id FROM positions ORDER BY id ASC LIMIT 1');
        $row = $stmt->fetch();

        return $row ? (int)$row['id'] : null;
    }

    private function decryptRows(array $rows): array
    {
        $encryption = new Encryption();
        foreach ($rows as &$row) {
            $row = $this->decryptRow($row, $encryption);
        }
        return $rows;
    }

    private function decryptRow(array $row, ?Encryption $encryption = null): array
    {
        $encryption ??= new Encryption();
        $fields = ['first_name', 'last_name', 'national_id', 'phone_number', 'email', 'address'];
        foreach ($fields as $field) {
            if (!empty($row[$field])) {
                $decrypted = $encryption->decrypt($row[$field]);
                if ($decrypted !== '') {
                    $row[$field] = $decrypted;
                }
            }
        }
        return $row;
    }
}
