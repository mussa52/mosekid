<?php
declare(strict_types=1);

namespace Model;

use App\BaseModel;
use App\Encryption;

class LeaveModel extends BaseModel
{
    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT l.*, e.first_name, e.last_name, u.first_name AS reviewer_first, u.last_name AS reviewer_last FROM leaves l JOIN employees e ON l.employee_id = e.id LEFT JOIN users u ON l.reviewed_by = u.id ORDER BY l.applied_at DESC');
        return $this->decryptLeaveRows($stmt->fetchAll());
    }

    public function findByEmployeeId(int $employeeId): array
    {
        $stmt = $this->db->prepare('SELECT l.*, e.first_name, e.last_name, u.first_name AS reviewer_first, u.last_name AS reviewer_last FROM leaves l JOIN employees e ON l.employee_id = e.id LEFT JOIN users u ON l.reviewed_by = u.id WHERE l.employee_id = :employee_id ORDER BY l.applied_at DESC');
        $stmt->execute(['employee_id' => $employeeId]);
        return $this->decryptLeaveRows($stmt->fetchAll());
    }

    private function decryptLeaveRows(array $rows): array
    {
        $encryption = new Encryption();
        foreach ($rows as &$row) {
            if (!empty($row['first_name'])) {
                $dec = $encryption->decrypt($row['first_name']);
                if ($dec !== '') {
                    $row['first_name'] = $dec;
                }
            }
            if (!empty($row['last_name'])) {
                $dec = $encryption->decrypt($row['last_name']);
                if ($dec !== '') {
                    $row['last_name'] = $dec;
                }
            }
            if (!empty($row['reviewer_first'])) {
                $dec = $encryption->decrypt($row['reviewer_first']);
                if ($dec !== '') {
                    $row['reviewer_first'] = $dec;
                }
            }
            if (!empty($row['reviewer_last'])) {
                $dec = $encryption->decrypt($row['reviewer_last']);
                if ($dec !== '') {
                    $row['reviewer_last'] = $dec;
                }
            }
        }
        return $rows;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT l.*, e.first_name, e.last_name, u.first_name AS reviewer_first, u.last_name AS reviewer_last FROM leaves l JOIN employees e ON l.employee_id = e.id LEFT JOIN users u ON l.reviewed_by = u.id WHERE l.id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $leave = $stmt->fetch();
        if ($leave === false) {
            return null;
        }

        $encryption = new Encryption();
        if (!empty($leave['first_name'])) {
            $dec = $encryption->decrypt($leave['first_name']);
            if ($dec !== '') {
                $leave['first_name'] = $dec;
            }
        }
        if (!empty($leave['last_name'])) {
            $dec = $encryption->decrypt($leave['last_name']);
            if ($dec !== '') {
                $leave['last_name'] = $dec;
            }
        }
        if (!empty($leave['reviewer_first'])) {
            $dec = $encryption->decrypt($leave['reviewer_first']);
            if ($dec !== '') {
                $leave['reviewer_first'] = $dec;
            }
        }
        if (!empty($leave['reviewer_last'])) {
            $dec = $encryption->decrypt($leave['reviewer_last']);
            if ($dec !== '') {
                $leave['reviewer_last'] = $dec;
            }
        }

        return $leave;
    }

    public function updateStatus(int $id, string $status, int $reviewedBy): bool
    {
        $stmt = $this->db->prepare('UPDATE leaves SET status = :status, reviewed_at = NOW(), reviewed_by = :reviewed_by WHERE id = :id');
        return $stmt->execute(['status' => $status, 'reviewed_by' => $reviewedBy, 'id' => $id]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare('UPDATE leaves SET leave_type = :leave_type, start_date = :start_date, end_date = :end_date, reason = :reason WHERE id = :id');
        return $stmt->execute([
            'leave_type' => $data['leave_type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'reason' => $data['reason'],
            'id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM leaves WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare('INSERT INTO leaves (employee_id, leave_type, start_date, end_date, reason, status, applied_at) VALUES (:employee_id, :leave_type, :start_date, :end_date, :reason, :status, NOW())');
        return $stmt->execute([
            'employee_id' => $data['employee_id'],
            'leave_type' => $data['leave_type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'reason' => $data['reason'],
            'status' => $data['status'] ?? 'pending',
        ]);
    }
}
