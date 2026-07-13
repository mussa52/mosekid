<div class="card shadow-sm border-0">
    <div class="card-body">
        <?php if (currentUserRoleName() !== 'admin'): ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Attendance Records</h5>
                <?php if (!empty($attendanceManager) && currentUserRoleName() !== 'hr_manager'): ?>
                    <form method="get" class="d-flex gap-2" action="<?= e(APP_URL . '/index.php') ?>">
                        <input type="hidden" name="action" value="attendance">
                        <div class="form-group mb-0">
                            <select name="employee_id" class="form-select form-select-sm">
                                <?php if (currentUserRoleName() !== 'admin'): ?>
                                    <option value="">All employees</option>
                                <?php endif; ?>
                                <?php foreach ($employees as $employee): ?>
                                    <?php $employeeName = trim((!empty($employee['first_name']) ? e($employee['first_name']) : $employee['first_name']) . ' ' . (!empty($employee['last_name']) ? e($employee['last_name']) : $employee['last_name'])); ?>
                                    <option value="<?= e($employee['id']) ?>" <?= !empty($selectedEmployeeId) && $selectedEmployeeId === (int) $employee['id'] ? 'selected' : '' ?>><?= e($employeeName) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                <?php endif; ?>
                <div class="d-flex gap-2">
                    <?php if (!empty($hasEmployee)): ?>
                        <form method="post" action="<?= e(APP_URL . '/index.php?action=attendance-checkin') ?>">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <?php if (!empty($attendanceManager) && !empty($selectedEmployeeId)): ?>
                                <input type="hidden" name="employee_id" value="<?= e($selectedEmployeeId) ?>">
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary btn-sm">Check In</button>
                        </form>
                        <form method="post" action="<?= e(APP_URL . '/index.php?action=attendance-checkout') ?>">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <?php if (!empty($attendanceManager) && !empty($selectedEmployeeId)): ?>
                                <input type="hidden" name="employee_id" value="<?= e($selectedEmployeeId) ?>">
                            <?php endif; ?>
                            <button type="submit" class="btn btn-secondary btn-sm">Check Out</button>
                        </form>
                    <?php else: ?>
                        <button type="button" class="btn btn-primary btn-sm disabled" disabled>Check In</button>
                        <button type="button" class="btn btn-secondary btn-sm disabled" disabled>Check Out</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success mb-3"><?= e($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="alert alert-danger mb-3">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <div><?= e($error) ?></div>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>
        <?php if (empty($attendanceManager) && empty($hasEmployee)): ?>
            <div class="alert alert-warning mb-3">
                Your account is not linked to an employee record. Attendance check-in / check-out requires a matching employee profile.
            </div>
        <?php endif; ?>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr><th>Employee</th><th>Date</th><th>Check In</th><th>Check Out</th><th>Status</th><?php if (currentUserRoleName() === 'admin'): ?><th>Actions</th><?php endif; ?></tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?= e($record['employee']) ?></td>
                            <td><?= e($record['date']) ?></td>
                            <td><?= e($record['check_in']) ?></td>
                            <td><?= e($record['check_out']) ?></td>
                            <td><span class="badge bg-success"><?= e($record['status']) ?></span></td>
                            <?php if (currentUserRoleName() === 'admin'): ?>
                                <td>
                                    <a href="<?= e(APP_URL . '/index.php?action=attendance-edit&id=' . (int)$record['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="post" action="<?= e(APP_URL . '/index.php?action=attendance-delete&id=' . (int)$record['id']) ?>" class="d-inline-block" onsubmit="return confirm('Delete this record?');">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
