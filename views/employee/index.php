<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Employee Records</h4>
                <p class="text-muted mb-0">Responsive employee management table.</p>
            </div>
            <a href="index.php?action=employees-create" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Add Employee</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Employee Code</th>
                        <th>Full Name</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
<?php foreach ($employees as $employee): ?>
                        <tr>
                            <td><?= e($employee['employee_code'] ?? '') ?></td>
<td><?= e(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? '')) ?></td>
<td><?= e($employee['department_name'] ?? 'N/A') ?></td>
<td><?= e($employee['position_name'] ?? 'N/A') ?></td>
<td><?= e($employee['phone_number'] ?? '') ?></td>
<td><?= e($employee['email'] ?? '') ?></td>
<td>
    <span class="status-badge bg-success bg-opacity-10 text-success">
        <?= e($employee['status'] ?? 'Active') ?>
    </span>
</td>
                            <td>
                                <a href="index.php?action=employees-profile&id=<?= (int)($employee['id'] ?? 0) ?>" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="index.php?action=employees-edit&id=<?= (int)($employee['id'] ?? 0) ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="index.php?action=employees-delete&id=<?= (int)($employee['id'] ?? 0) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this employee?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
