<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Leave Requests</h5>
        </div>
        <?php if ($currentRole === 'employee'): ?>
            <?php if (!$currentEmployee): ?>
                <div class="alert alert-warning mb-4">
                    Unable to identify your employee profile. Leave requests are only available for users who are linked to an employee record. Please contact your administrator.
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="card card-body bg-light border-0 mb-4">
            <?php if (!empty($_SESSION['errors'])): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= e($_SESSION['success']) ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if ($currentRole === 'employee' && $currentEmployee): ?>
            <form method="post" action="<?= e(APP_URL . '/index.php?action=leave-create') ?>">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Leave Type</label>
                        <select name="leave_type" class="form-select" required>
                            <option value="">Select leave type</option>
                            <option value="annual">Annual</option>
                            <option value="sick">Sick</option>
                            <option value="unpaid">Unpaid</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Submit Leave Request</button>
                    </div>
                </div>
            </form>
            <?php endif; ?>
        </div>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                                <th>Employee</th>
                        <th>Leave Date</th>
                        <th>Month</th>
                        <th>Type</th>
                        <th>Status</th>
                        <?php if (in_array($currentRole, ['hr_manager', 'admin'], true)): ?>
                            <th>Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaves as $leave): ?>
                        <tr>
                            <td><?= e($leave['first_name'] . ' ' . $leave['last_name']) ?></td>
                            <td><?= e($leave['start_date']) ?></td>
                            <td><?= e(date('F', strtotime($leave['start_date']))) ?></td>
                            <td><?= e($leave['leave_type']) ?></td>
                            <td>
                                <?php
                                    $status = strtolower($leave['status']);
                                    $badgeClass = $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning text-dark');
                                ?>
                                <span class="badge bg-<?= e($badgeClass) ?>"><?= e(ucfirst($leave['status'])) ?></span>
                            </td>
                            <?php if (in_array($currentRole, ['hr_manager', 'admin'], true)): ?>
                                <td>
                                    <?php if (strtolower($leave['status']) === 'pending'): ?>
                                        <div class="d-flex gap-2">
                                            <form method="post" action="<?= e(APP_URL . '/index.php?action=leave-approve&id=' . (int)$leave['id']) ?>" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                            <form method="post" action="<?= e(APP_URL . '/index.php?action=leave-reject&id=' . (int)$leave['id']) ?>" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                            </form>
                                            <a href="<?= e(APP_URL . '/index.php?action=leave-edit&id=' . (int)$leave['id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <form method="post" action="<?= e(APP_URL . '/index.php?action=leave-delete&id=' . (int)$leave['id']) ?>" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">No action</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
