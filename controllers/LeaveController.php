<?php
declare(strict_types=1);

namespace Controller;

use Model\LeaveModel;

class LeaveController
{
    public function index(): void
    {
        $model = new LeaveModel();
        $currentRole = currentUserRoleName();
        $currentEmployee = null;

        // Admins and HR managers should see all leave requests for approval.
        if (in_array($currentRole, ['hr_manager', 'admin'], true)) {
            $leaves = $model->findAll();
        } else {
            $currentEmployee = getCurrentEmployeeByUserEmail();
            $leaves = $currentEmployee ? $model->findByEmployeeId((int) $currentEmployee['id']) : [];
        }

        renderView('leave/index.php', compact('leaves', 'currentEmployee', 'currentRole'), 'Leave Management', 'leave');
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        verify_csrf();
        $employee = getCurrentEmployeeByUserEmail();
        if (!$employee) {
            $_SESSION['errors'] = ['Unable to identify employee for leave request.'];
            redirect('/index.php?action=leave');
        }

        $data = [
            'employee_id' => (int) $employee['id'],
            'leave_type' => trim((string) ($_POST['leave_type'] ?? '')),
            'start_date' => trim((string) ($_POST['start_date'] ?? '')),
            'end_date' => trim((string) ($_POST['end_date'] ?? '')),
            'reason' => trim((string) ($_POST['reason'] ?? '')),
            'status' => 'pending',
        ];

        $errors = [];
        if ($data['leave_type'] === '') {
            $errors[] = 'Leave type is required.';
        }
        if ($data['start_date'] === '') {
            $errors[] = 'Start date is required.';
        }
        if ($data['end_date'] === '') {
            $errors[] = 'End date is required.';
        }
        if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
            $errors[] = 'End date cannot be earlier than start date.';
        }
        if ($data['reason'] === '') {
            $errors[] = 'A reason is required.';
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            redirect('/index.php?action=leave');
        }

        $model = new LeaveModel();
        $model->create($data);
        $_SESSION['success'] = 'Leave request submitted successfully.';
        redirect('/index.php?action=leave');
    }

    public function approve(int $id): void
    {
        requirePermission('leave_manage');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        verify_csrf();
        $model = new LeaveModel();
        $model->updateStatus($id, 'approved', (int)($_SESSION['user_id'] ?? 0));
        redirect('/index.php?action=leave');
    }

    public function reject(int $id): void
    {
        requirePermission('leave_manage');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        verify_csrf();
        $model = new LeaveModel();
        $model->updateStatus($id, 'rejected', (int)($_SESSION['user_id'] ?? 0));
        redirect('/index.php?action=leave');
    }

    public function edit(int $id): void
    {
        requirePermission('leave_manage');

        $model = new LeaveModel();
        $leave = $model->findById($id);
        if (!$leave) {
            $_SESSION['errors'] = ['Leave request not found.'];
            redirect('/index.php?action=leave');
        }

        $currentRole = currentUserRoleName();
        renderView('leave/edit.php', compact('leave', 'currentRole'), 'Edit Leave Request', 'leave');
    }

    public function update(int $id): void
    {
        requirePermission('leave_manage');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        verify_csrf();

        $data = [
            'leave_type' => trim((string) ($_POST['leave_type'] ?? '')),
            'start_date' => trim((string) ($_POST['start_date'] ?? '')),
            'end_date' => trim((string) ($_POST['end_date'] ?? '')),
            'reason' => trim((string) ($_POST['reason'] ?? '')),
        ];

        $errors = [];
        if ($data['leave_type'] === '') {
            $errors[] = 'Leave type is required.';
        }
        if ($data['start_date'] === '') {
            $errors[] = 'Start date is required.';
        }
        if ($data['end_date'] === '') {
            $errors[] = 'End date is required.';
        }
        if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
            $errors[] = 'End date cannot be earlier than start date.';
        }
        if ($data['reason'] === '') {
            $errors[] = 'A reason is required.';
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            redirect('/index.php?action=leave-edit&id=' . $id);
        }

        $model = new LeaveModel();
        $model->update($id, $data);
        $_SESSION['success'] = 'Leave request updated.';
        redirect('/index.php?action=leave');
    }

    public function delete(int $id): void
    {
        requirePermission('leave_manage');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        verify_csrf();

        $model = new LeaveModel();
        $model->delete($id);
        $_SESSION['success'] = 'Leave request deleted.';
        redirect('/index.php?action=leave');
    }
}
