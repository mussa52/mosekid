<?php
declare(strict_types=1);

namespace Controller;

use App\Database;

class DashboardController
{
    public function index(): void
    {
        $roleName = strtolower((string) ($_SESSION['role_name'] ?? 'admin'));

        if ($roleName === 'payroll') {
            $stats = [
                ['label' => 'Salary Statistics', 'value' => '121', 'icon' => 'fa-chart-line', 'color' => 'success', 'subtext' => 'Average salary updated'],
                ['label' => 'Monthly Payments', 'value' => '98%', 'icon' => 'fa-calendar-check', 'color' => 'warning', 'subtext' => 'Payments processed on time'],
                ['label' => 'Payslip Generation', 'value' => '24', 'icon' => 'fa-file-invoice-dollar', 'color' => 'info', 'subtext' => 'Payslips ready'],
            ];

            $recentEmployees = $this->getRecentActivities();
            renderView('dashboard/index.php', compact('stats', 'recentEmployees'), 'Payroll Dashboard', 'payroll');
            return;
        }
if ($roleName === 'hr_manager') {

    $presentEmployees = (new \Model\EmployeeModel())->findPresentToday();
    $presentCount = count($presentEmployees);

    $stats = [
        [
            'label' => 'Employees',
            'value' => (string) $this->getCount('SELECT COUNT(*) FROM employees'),
            'subtext' => 'Total employees',
        ],
        [
            'label' => 'Departments',
            'value' => (string) $this->getCount('SELECT COUNT(*) FROM departments'),
            'subtext' => 'Total departments',
        ],
        [
            'label' => 'Positions',
            'value' => (string) $this->getCount('SELECT COUNT(*) FROM positions'),
            'subtext' => 'Available positions',
        ],
        [
            'label' => 'Attendance',
            'value' => $this->getAttendancePercent(),
            'subtext' => 'Today attendance',
        ],
        [
            'label' => 'Leave',
            'value' => (string) $this->getCount(
                'SELECT COUNT(*) FROM leaves WHERE status = :status',
                ['status' => 'pending']
            ),
            'subtext' => 'Pending requests',
        ],
    ];

    $recentEmployees = $this->getRecentActivities();

    renderView(
        'dashboard/index.php',
        compact('stats', 'recentEmployees', 'presentEmployees', 'presentCount'),
        'HR Manager Dashboard',
        'hr_manager'
    );

    return;
}
        $presentEmployees = (new \Model\EmployeeModel())->findPresentToday();
        $presentCount = count($presentEmployees);

        $stats = [
            ['label' => 'Total Employees', 'value' => (string) $this->getCount('SELECT COUNT(*) FROM employees'), 'icon' => 'fa-users', 'color' => 'primary', 'subtext' => 'Current headcount'],
            ['label' => 'Total Users', 'value' => (string) $this->getCount('SELECT COUNT(*) FROM users'), 'icon' => 'fa-user-check', 'color' => 'success', 'subtext' => 'Registered accounts'],
            ['label' => 'Departments', 'value' => (string) $this->getCount('SELECT COUNT(*) FROM departments'), 'icon' => 'fa-building', 'color' => 'warning', 'subtext' => 'Active departments'],
            ['label' => 'Leave Requests', 'value' => (string) $this->getCount('SELECT COUNT(*) FROM leaves WHERE status = :status', ['status' => 'pending']), 'icon' => 'fa-clipboard-list', 'color' => 'danger', 'subtext' => 'Pending approvals'],
            // Payroll Summary removed
            ['label' => 'Attendance Summary', 'value' => $this->getAttendancePercent(), 'icon' => 'fa-calendar-check', 'color' => 'secondary', 'subtext' => 'Present today'],
        ];

        $recentEmployees = $this->getRecentActivities();
        renderView('dashboard/index.php', compact('stats', 'recentEmployees', 'presentEmployees', 'presentCount'), 'Dashboard', 'dashboard');
    }

    private function getCount(string $sql, array $params = []): int
    {
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    private function getPayrollTotal(): float
    {
        // Removed payroll total calculation as payroll summary is no longer shown on dashboard.
        return 0.0;
    }

    private function getAttendancePercent(): string
    {
        $db = Database::getConnection();
        $totalStmt = $db->prepare('SELECT COUNT(*) FROM attendance WHERE attendance_date = CURDATE()');
        $totalStmt->execute();
        $total = (int) $totalStmt->fetchColumn();
        if ($total === 0) {
            return '0%';
        }

        $presentStmt = $db->prepare("SELECT COUNT(*) FROM attendance WHERE attendance_date = CURDATE() AND status = 'present'");
        $presentStmt->execute();
        $present = (int) $presentStmt->fetchColumn();

        return round(($present / $total) * 100) . '%';
    }

    private function formatCurrency(float $amount): string
    {
        return '$' . number_format($amount, 2);
    }

    private function getRecentActivities(): array
    {
        $stmt = Database::getConnection()->query('SELECT action, created_at, details FROM activity_logs ORDER BY created_at DESC LIMIT 6');
        $rows = [];

        while ($row = $stmt->fetch()) {
            $rows[] = [
                'time' => date('h:i A', strtotime($row['created_at'])),
                'activity' => $row['action'] . ($row['details'] ? ' - ' . $row['details'] : ''),
                'status' => 'Completed',
            ];
        }

        if (empty($rows)) {
            return [
                ['time' => date('h:i A'), 'activity' => 'No recent activity available', 'status' => 'Info'],
            ];
        }

        return $rows;
    }
}
