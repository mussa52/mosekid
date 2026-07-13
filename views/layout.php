<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Employee Management System') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
        body { background: #ffffff; color: #333333; }
        .sidebar { min-height: 100vh; background: linear-gradient(180deg, rgba(15,23,42,0.97) 0%, rgba(15,118,110,0.95) 100%); color: #f8fafc; }
        .sidebar .input-group .form-control { background: rgba(255,255,255,0.04); color: #fff; border: 1px solid rgba(255,255,255,0.04); }
        .sidebar .btn-outline-light { border-color: rgba(255,255,255,0.12); color: #fff; }
        .sidebar .brand { padding-bottom: 1rem; border-bottom: 1px solid rgba(148,163,184,0.1); }
        .nav-link { color: #cbd5e1; border-radius: 10px; transition: background 0.25s ease, color 0.25s ease; }
        .nav-link:hover, .nav-link.active { background: rgba(56,189,248,0.16); color: #eff6ff; }
        .topbar { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(148,163,184,0.12); }
        .main-content { min-height: 100vh; }
        .card { border: none; border-radius: 20px; }
        .card:hover { transform: translateY(-4px); transition: transform 0.2s ease; }
        .btn-outline-secondary { border-color: rgba(148,163,184,0.2); color: #333; }
        .btn-outline-secondary:hover { background: rgba(56,189,248,0.06); }
    </style>
</head>
<body>
    <div class="d-flex">
        <aside class="sidebar p-3">
            <div class="brand mb-4 d-flex align-items-center">
                <div>
                    <div class="fw-bold">
                        <?php if (currentUserRoleName() === 'admin'): ?>
                            Admin Dashboard
                        <?php elseif (currentUserRoleName() === 'payroll'): ?>
                            Payroll Dashboard
                        <?php elseif (currentUserRoleName() === 'hr_manager'): ?>
                            HR Dashboard
                        <?php else: ?>
                            Employee Dashboard
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <nav class="nav flex-column gap-1">
                <?php $sidebarMenu = getSidebarMenu(currentUserRoleName()); ?>
                <?php $sidebarCounts = getSidebarCounts(); ?>
                <?php $currentEmployeeId = getCurrentEmployeeId(); ?>
                <?php if (empty($currentEmployeeId)): ?>
                    <?php $currentEmployee = getCurrentEmployeeByUserEmail(); ?>
                    <?php $currentEmployeeId = $currentEmployee['id'] ?? null; ?>
                <?php endif; ?>
                <?php foreach ($sidebarMenu as $item): ?>
                    <?php $isActive = isset($active) && $active === $item['action']; ?>
                    <?php $menuLabel = $item['label']; ?>
                    <?php $itemUrl = APP_URL . '/index.php?action=' . $item['action']; ?>
                    <?php if ($item['action'] === 'employees-profile' && !empty($currentEmployeeId)): ?>
                        <?php $itemUrl .= '&id=' . (int)$currentEmployeeId; ?>
                    <?php endif; ?>
                    <a class="nav-link<?= $isActive ? ' active' : '' ?>" href="<?= e($itemUrl) ?>"><?= $menuLabel ?></a>
                <?php endforeach; ?>
            </nav>
        </aside>

        <div class="flex-grow-1 main-content">
            <nav class="topbar navbar navbar-expand-lg bg-white shadow-sm px-4 py-3">
                <div class="d-flex align-items-center gap-3 w-100">
                    <div class="fw-bold fs-4 text-primary">Employee Management System</div>
                    <div class="ms-auto d-flex align-items-center">
                        <?php if (currentUserRoleName() === 'admin'): ?>
                            <form class="d-flex" method="get" action="<?= e(APP_URL . '/index.php') ?>" role="search">
                                <input type="hidden" name="action" value="employees-search">
                                <div class="input-group input-group-sm">
                                    <input type="search" name="q" class="form-control" placeholder="Search employees..." aria-label="Search employees">
                                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>

            <main class="p-4">
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
