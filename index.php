<?php
require_once __DIR__ . '/config/config.php';

$action = $_GET['action'] ?? 'dashboard';

if ($action === 'login') {
    header('Location: ' . APP_URL . '/login.php');
    exit;
}

if ($action === 'logout') {
    header('Location: ' . APP_URL . '/logout.php');
    exit;
}

if (!isLoggedIn()) {
    header('Location: ' . APP_URL . '/login.php');
    exit;
}

$router = [
    'home' => [new Controller\DashboardController(), 'index'],
    'dashboard' => [new Controller\DashboardController(), 'index'],
    'hr-dashboard' => [new Controller\DashboardController(), 'index'],
    'payroll-dashboard' => [new Controller\DashboardController(), 'index'],
    'users' => [new Controller\UserController(), 'index'],
    'users-edit' => [new Controller\UserController(), 'edit'],
    'users-delete' => [new Controller\UserController(), 'delete'],
    'employees' => [new Controller\EmployeeController(), 'index'],
    'employees-search' => [new Controller\EmployeeController(), 'search'],
    'employees-create' => [new Controller\EmployeeController(), 'create'],
    'employees-edit' => [new Controller\EmployeeController(), 'edit'],
    'employees-delete' => [new Controller\EmployeeController(), 'delete'],
    'employees-profile' => [new Controller\EmployeeController(), 'profile'],
    'departments' => [new Controller\DepartmentController(), 'index'],
    'departments-create' => [new Controller\DepartmentController(), 'create'],
    'departments-create-api' => [new Controller\DepartmentController(), 'createApi'],
    'departments-edit' => [new Controller\DepartmentController(), 'edit'],
    'departments-delete' => [new Controller\DepartmentController(), 'delete'],
    'departments-search' => [new Controller\DepartmentController(), 'search'],
    'positions' => [new Controller\PositionController(), 'index'],
    'positions-create' => [new Controller\PositionController(), 'create'],
    'positions-create-api' => [new Controller\PositionController(), 'createApi'],
    'positions-edit' => [new Controller\PositionController(), 'edit'],
    'positions-delete' => [new Controller\PositionController(), 'delete'],
    'positions-search' => [new Controller\PositionController(), 'search'],
    'leave' => [new Controller\LeaveController(), 'index'],
    'leave-create' => [new Controller\LeaveController(), 'create'],
    'leave-approve' => [new Controller\LeaveController(), 'approve'],
    'leave-reject' => [new Controller\LeaveController(), 'reject'],
    'leave-edit' => [new Controller\LeaveController(), 'edit'],
    'leave-update' => [new Controller\LeaveController(), 'update'],
    'leave-delete' => [new Controller\LeaveController(), 'delete'],
    'attendance' => [new Controller\AttendanceController(), 'index'],
    'attendance-checkin' => [new Controller\AttendanceController(), 'checkIn'],
    'attendance-checkout' => [new Controller\AttendanceController(), 'checkOut'],
    'attendance-edit' => [new Controller\AttendanceController(), 'edit'],
    'attendance-update' => [new Controller\AttendanceController(), 'update'],
    'attendance-delete' => [new Controller\AttendanceController(), 'delete'],
];

if (array_key_exists($action, $router)) {
    $controller = $router[$action][0];
    $method = $router[$action][1];
    $args = [];
    if (in_array($action, ['users-edit', 'users-delete', 'leave-approve', 'leave-reject', 'leave-edit', 'leave-update', 'leave-delete', 'payroll-process', 'employees-edit', 'employees-delete', 'employees-profile', 'departments-edit', 'departments-delete', 'positions-edit', 'positions-delete', 'attendance-edit', 'attendance-delete'], true)) {
        $args[] = (int)($_GET['id'] ?? 0);
    }
    $controller->$method(...$args);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fb; }
        .sidebar { min-height: 100vh; background: #0f172a; color: #fff; }
        .nav-link { color: #cbd5e1; border-radius: 8px; }
        .nav-link:hover, .nav-link.active { background: #1e293b; color: #fff; }
        .card:hover { transform: translateY(-4px); transition: 0.2s ease; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <aside class="col-lg-2 sidebar p-3">
                <h4 class="fw-bold mb-4"><i class="fa-solid fa-users-gear"></i> EMS</h4>
                <ul class="nav flex-column gap-1">
                    <li><a class="nav-link active" href="index.php?action=dashboard"><i class="fa-solid fa-users me-2"></i>Total Employees</a></li>
                    <li><a class="nav-link" href="#"><i class="fa-solid fa-user-check me-2"></i>Total Users</a></li>
                    <li><a class="nav-link" href="#"><i class="fa-solid fa-building me-2"></i>Departments</a></li>
                    <li><a class="nav-link" href="#"><i class="fa-solid fa-clipboard-list me-2"></i>Leave Requests</a></li>
                    <!-- Payroll Summary removed per admin request -->
                    <li><a class="nav-link" href="#"><i class="fa-solid fa-calendar-check me-2"></i>Attendance Summary</a></li>
                    <li><a class="nav-link" href="index.php?action=logout"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
                </ul>
            </aside>
            <main class="col-lg-10">
                <div class="topbar p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Admin Dashboard</h4>
                        <small class="text-muted">HR & Payroll Control Center</small>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-outline-secondary"><i class="fa-solid fa-bell"></i></button>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://via.placeholder.com/40" class="rounded-circle" alt="avatar">
                            <span class="fw-semibold">Admin</span>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="row g-4">
                        <div class="col-lg-4 col-sm-6">
                            <div class="card shadow-sm border-0 h-100 p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted mb-1">Total Employees</p>
                                        <h3 class="fw-bold mb-0">128</h3>
                                        <small class="text-muted">Current headcount</small>
                                    </div>
                                    <div class="rounded-circle p-3 bg-primary bg-opacity-10 text-primary">
                                        <i class="fa-solid fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="card shadow-sm border-0 h-100 p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted mb-1">Total Users</p>
                                        <h3 class="fw-bold mb-0">165</h3>
                                        <small class="text-muted">Active accounts</small>
                                    </div>
                                    <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success">
                                        <i class="fa-solid fa-user-check fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="card shadow-sm border-0 h-100 p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted mb-1">Departments</p>
                                        <h3 class="fw-bold mb-0">8</h3>
                                        <small class="text-muted">Active departments</small>
                                    </div>
                                    <div class="rounded-circle p-3 bg-warning bg-opacity-10 text-warning">
                                        <i class="fa-solid fa-building fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="card shadow-sm border-0 h-100 p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted mb-1">Leave Requests</p>
                                        <h3 class="fw-bold mb-0">14</h3>
                                        <small class="text-muted">Pending approval</small>
                                    </div>
                                    <div class="rounded-circle p-3 bg-danger bg-opacity-10 text-danger">
                                        <i class="fa-solid fa-clipboard-list fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Payroll Summary card removed -->
                        <div class="col-lg-4 col-sm-6">
                            <div class="card shadow-sm border-0 h-100 p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted mb-1">Attendance Summary</p>
                                        <h3 class="fw-bold mb-0">96%</h3>
                                        <small class="text-muted">Present today</small>
                                    </div>
                                    <div class="rounded-circle p-3 bg-secondary bg-opacity-10 text-secondary">
                                        <i class="fa-solid fa-calendar-check fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4 mt-4">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h5 class="card-title">Recent Activities</h5>
                                    <div class="table-responsive">
                                        <table class="table align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Time</th>
                                                    <th>Activity</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>09:25 AM</td>
                                                    <td>New employee added</td>
                                                    <td><span class="badge bg-success">Completed</span></td>
                                                </tr>
                                                <tr>
                                                    <td>10:10 AM</td>
                                                    <td>Payroll report generated</td>
                                                    <td><span class="badge bg-primary">Done</span></td>
                                                </tr>
                                                <tr>
                                                    <td>11:05 AM</td>
                                                    <td>Leave request approved</td>
                                                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
