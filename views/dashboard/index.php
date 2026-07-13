<?php $presentEmployees = $presentEmployees ?? []; $presentCount = $presentCount ?? 0; ?>
<?php if (currentUserRoleName() === 'payroll'): ?>
    <div class="list-group mb-4">
        <?php foreach ($stats as $stat): ?>
            <?php if (stripos((string)$stat['label'], 'payroll') !== false) continue; ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold"><?= e($stat['label']) ?></div>
                    <small class="text-muted"><?= e($stat['subtext']) ?></small>
                </div>
                <div class="fs-4 fw-bold"><?= e($stat['value']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
<?php elseif (in_array(currentUserRoleName(), ['admin', 'hr_manager'])): ?>
    <div class="row g-3">
        <?php foreach ($stats as $stat): ?>
            <?php if (stripos((string)$stat['label'], 'payroll') !== false) continue; ?>
            <?php 
                $action = 'dashboard';
                if (stripos((string)$stat['label'], 'employee') !== false) {
                    $action = 'employees';
                } elseif (stripos((string)$stat['label'], 'user') !== false) {
                    $action = 'users';
                } elseif (stripos((string)$stat['label'], 'department') !== false) {
                    $action = 'departments';
                } elseif (stripos((string)$stat['label'], 'leave') !== false) {
                    $action = 'leave';
                } elseif (stripos((string)$stat['label'], 'attendance') !== false) {
                    $action = 'attendance';
                }
                $href = APP_URL . '/index.php?action=' . $action;
            ?>
            <div class="col-lg-4 col-md-6">
                <a href="<?= e($href) ?>" class="text-decoration-none">
                    <div class="card border-0 rounded-3 stat-card" style="background: #f8f9fa; min-height: 140px;">
                        <div class="card-body p-4">
                            <p class="text-muted mb-2" style="font-size: 0.95rem;">
                                <?= e($stat['label']) ?>
                            </p>
                            <h2 class="fw-bold mb-2" style="font-size: 2rem; color: #333;">
                                <?= e($stat['value']) ?>
                            </h2>
                            <small class="text-muted" style="font-size: 0.85rem;">
                                <?= e($stat['subtext']) ?>
                            </small>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <style>
        .stat-card {
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12) !important;
        }
    </style>
<?php else: ?>
    <div class="row g-3">
        <div class="col-lg-4 col-md-6">
            <a href="<?= e(APP_URL . '/index.php?action=employees-profile') ?>" class="text-decoration-none">
                <div class="card border-0 rounded-3 stat-card" style="background: #f8f9fa; min-height: 140px;">
                    <div class="card-body p-4">
                        <p class="text-muted mb-2" style="font-size: 0.95rem;">My Profile</p>
                        <small class="text-muted" style="font-size: 0.85rem;">View your profile</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="<?= e(APP_URL . '/index.php?action=attendance') ?>" class="text-decoration-none">
                <div class="card border-0 rounded-3 stat-card" style="background: #f8f9fa; min-height: 140px;">
                    <div class="card-body p-4">
                        <p class="text-muted mb-2" style="font-size: 0.95rem;">Attendance</p>
                        <small class="text-muted" style="font-size: 0.85rem;">View attendance records</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="<?= e(APP_URL . '/index.php?action=leave') ?>" class="text-decoration-none">
                <div class="card border-0 rounded-3 stat-card" style="background: #f8f9fa; min-height: 140px;">
                    <div class="card-body p-4">
                        <p class="text-muted mb-2" style="font-size: 0.95rem;">Leave</p>
                        <small class="text-muted" style="font-size: 0.85rem;">Manage leave requests</small>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <style>
        .stat-card {
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12) !important;
        }
    </style>
<?php endif; ?>
