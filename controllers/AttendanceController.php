<?php
declare(strict_types=1);

namespace Controller;
use App\Database;
use App\Encryption;

class AttendanceController
{
    public function index(): void
    {
        $db = Database::getConnection();
        $encryption = new Encryption();

        $employeeModel = new \Model\EmployeeModel();
        $employees = [];
        $attendanceManager = hasPermission('attendance_manage');
        $employeeId = $attendanceManager ? null : getCurrentEmployeeId();
        $selectedEmployeeId = null;

        if ($attendanceManager) {
            $employees = $employeeModel->findActive();
            if (!empty($_GET['employee_id']) && ctype_digit((string) $_GET['employee_id'])) {
                $selectedEmployeeId = (int) $_GET['employee_id'];
                $selectedEmployee = $employeeModel->findById($selectedEmployeeId);
                if ($selectedEmployee) {
                    $employeeId = $selectedEmployeeId;
                } else {
                    $selectedEmployeeId = null;
                }
            }

            if ($selectedEmployeeId !== null) {
                $stmt = $db->prepare('SELECT a.*, e.first_name, e.last_name FROM attendance a JOIN employees e ON a.employee_id = e.id WHERE a.employee_id = :eid ORDER BY a.attendance_date DESC, a.check_in DESC');
                $stmt->execute(['eid' => $selectedEmployeeId]);
                $rows = $stmt->fetchAll();
            } else {
                $stmt = $db->query('SELECT a.*, e.first_name, e.last_name FROM attendance a JOIN employees e ON a.employee_id = e.id ORDER BY a.attendance_date DESC, a.check_in DESC');
                $rows = $stmt->fetchAll();
            }
        } else {
            if (empty($employeeId)) {
                $rows = [];
            } else {
                $stmt = $db->prepare('SELECT a.*, e.first_name, e.last_name FROM attendance a JOIN employees e ON a.employee_id = e.id WHERE a.employee_id = :eid ORDER BY a.attendance_date DESC, a.check_in DESC');
                $stmt->execute(['eid' => $employeeId]);
                $rows = $stmt->fetchAll();
            }
        }

        $records = [];
        foreach ($rows as $r) {
            $first = !empty($r['first_name']) ? $encryption->decrypt($r['first_name']) : '';
            $last = !empty($r['last_name']) ? $encryption->decrypt($r['last_name']) : '';
            $employeeName = trim(($first !== '' ? $first : $r['first_name']) . ' ' . ($last !== '' ? $last : $r['last_name']));

            $records[] = [
                'id' => $r['id'],
                'employee' => $employeeName,
                'date' => $r['attendance_date'] ?? ($r['date'] ?? ''),
                'check_in' => $r['check_in'] ? date('H:i', strtotime($r['check_in'])) : '-',
                'check_out' => $r['check_out'] ? date('H:i', strtotime($r['check_out'])) : '-',
                'status' => ucfirst((string) ($r['status'] ?? '')),
            ];
        }

        if (!$attendanceManager && empty($employeeId) && !empty($_SESSION['user_id'])) {
            $employee = getCurrentEmployeeByUserEmail();
            if ($employee) {
                $employeeId = (int) $employee['id'];
                $_SESSION['employee_id'] = $employeeId;
            }
        }

        $hasEmployee = !empty($employeeId);

        $canCheckout = false;
        if ($hasEmployee) {
            $todayStmt = $db->prepare('SELECT * FROM attendance WHERE employee_id = :eid AND attendance_date = CURDATE() LIMIT 1');
            $todayStmt->execute(['eid' => $employeeId]);
            $todayRow = $todayStmt->fetch();
            $canCheckout = $todayRow && !empty($todayRow['check_in']) && empty($todayRow['check_out']);
        }

        renderView('attendance/index.php', compact('records', 'canCheckout', 'hasEmployee', 'attendanceManager', 'employees', 'selectedEmployeeId'), 'Attendance Records', 'attendance');
    }

    public function checkOut(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        verify_csrf();

        $employeeId = getCurrentEmployeeId();
        if (hasPermission('attendance_manage') && !empty($_POST['employee_id']) && ctype_digit((string) $_POST['employee_id'])) {
            $employeeId = (int) $_POST['employee_id'];
            $selectedEmployee = (new \Model\EmployeeModel())->findById($employeeId);
            if (!$selectedEmployee) {
                $employeeId = null;
            }
        }

        if (empty($employeeId)) {
            $employee = getCurrentEmployeeByUserEmail();
            if ($employee) {
                $employeeId = (int)$employee['id'];
                $_SESSION['employee_id'] = $employeeId;
            }
        }

        if (empty($employeeId)) {
            $_SESSION['errors'] = ['Unable to identify your employee record.'];
            redirect('/index.php?action=attendance');
        }

        $db = \App\Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM attendance WHERE employee_id = :eid AND attendance_date = CURDATE() LIMIT 1');
        $stmt->execute(['eid' => $employeeId]);
        $row = $stmt->fetch();

        if (!$row || empty($row['check_in'])) {
            $_SESSION['errors'] = ['You must check in before checking out.'];
            redirect('/index.php?action=attendance');
        }

        if (!empty($row['check_out'])) {
            $_SESSION['errors'] = ['You have already checked out for today.'];
            redirect('/index.php?action=attendance');
        }

        $up = $db->prepare('UPDATE attendance SET check_out = NOW(), updated_at = NOW() WHERE id = :id');
        $up->execute(['id' => $row['id']]);

        $_SESSION['success'] = 'Checked out successfully.';
        redirect('/index.php?action=attendance');
    }

    public function checkIn(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        verify_csrf();

        $employeeId = getCurrentEmployeeId();
        if (hasPermission('attendance_manage') && !empty($_POST['employee_id']) && ctype_digit((string) $_POST['employee_id'])) {
            $employeeId = (int) $_POST['employee_id'];
            $selectedEmployee = (new \Model\EmployeeModel())->findById($employeeId);
            if (!$selectedEmployee) {
                $employeeId = null;
            }
        }

        if (empty($employeeId)) {
            $employee = getCurrentEmployeeByUserEmail();
            if ($employee) {
                $employeeId = (int) $employee['id'];
                $_SESSION['employee_id'] = $employeeId;
            }
        }

        if (empty($employeeId)) {
            $_SESSION['errors'] = ['Unable to identify your employee record.'];
            redirect('/index.php?action=attendance');
        }

        $db = \App\Database::getConnection();

        // Check if there is an attendance row for today
        $stmt = $db->prepare('SELECT * FROM attendance WHERE employee_id = :eid AND attendance_date = CURDATE() LIMIT 1');
        $stmt->execute(['eid' => $employeeId]);
        $row = $stmt->fetch();

        if ($row) {
            if (!empty($row['check_in'])) {
                $_SESSION['errors'] = ['You have already checked in for today.'];
                redirect('/index.php?action=attendance');
            }

            $up = $db->prepare('UPDATE attendance SET check_in = NOW(), status = :status, updated_at = NOW() WHERE id = :id');
            $up->execute(['status' => 'present', 'id' => $row['id']]);
            $_SESSION['success'] = 'Checked in successfully.';
            redirect('/index.php?action=attendance');
        }

        $ins = $db->prepare('INSERT INTO attendance (employee_id, check_in, attendance_date, status, created_at) VALUES (:eid, NOW(), CURDATE(), :status, NOW())');
        $ins->execute(['eid' => $employeeId, 'status' => 'present']);
        $_SESSION['success'] = 'Checked in successfully.';
        redirect('/index.php?action=attendance');
    }

    public function edit(int $id = 0): void
    {
        if (!hasPermission('attendance_manage') || currentUserRoleName() !== 'admin') {
            $_SESSION['error'] = 'You do not have permission to edit attendance records.';
            redirect('/index.php?action=attendance');
        }

        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT a.*, e.first_name, e.last_name FROM attendance a JOIN employees e ON a.employee_id = e.id WHERE a.id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            $_SESSION['error'] = 'Attendance record not found.';
            redirect('/index.php?action=attendance');
        }

        $encryption = new Encryption();
        $first = !empty($row['first_name']) ? $encryption->decrypt($row['first_name']) : '';
        $last = !empty($row['last_name']) ? $encryption->decrypt($row['last_name']) : '';
        $employeeName = trim(($first !== '' ? $first : $row['first_name']) . ' ' . ($last !== '' ? $last : $row['last_name']));

        renderView('attendance/edit.php', compact('row', 'employeeName'), 'Edit Attendance', 'attendance');
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        if (!hasPermission('attendance_manage') || currentUserRoleName() !== 'admin') {
            $_SESSION['error'] = 'You do not have permission to update attendance records.';
            redirect('/index.php?action=attendance');
        }

        verify_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $checkIn = $_POST['check_in'] ?? null;
        $checkOut = $_POST['check_out'] ?? null;

        $db = Database::getConnection();
        $up = $db->prepare('UPDATE attendance SET check_in = :ci, check_out = :co, updated_at = NOW() WHERE id = :id');
        $up->execute(['ci' => $checkIn, 'co' => $checkOut, 'id' => $id]);

        $_SESSION['success'] = 'Attendance record updated.';
        redirect('/index.php?action=attendance');
    }

    public function delete(int $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        if (!hasPermission('attendance_manage') || currentUserRoleName() !== 'admin') {
            $_SESSION['error'] = 'You do not have permission to delete attendance records.';
            redirect('/index.php?action=attendance');
        }

        verify_csrf();

        $db = Database::getConnection();
        $del = $db->prepare('DELETE FROM attendance WHERE id = :id');
        $del->execute(['id' => $id]);

        $_SESSION['success'] = 'Attendance record deleted.';
        redirect('/index.php?action=attendance');
    }
}
