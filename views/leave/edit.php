<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Edit Leave Request</h5>
        </div>

        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <form method="post" action="<?= e(APP_URL . '/index.php?action=leave-update&id=' . (int)$leave['id']) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Leave Type</label>
                    <select name="leave_type" class="form-select" required>
                        <option value="" <?= $leave['leave_type'] === '' ? 'selected' : '' ?>>Select leave type</option>
                        <option value="annual" <?= $leave['leave_type'] === 'annual' ? 'selected' : '' ?>>Annual</option>
                        <option value="sick" <?= $leave['leave_type'] === 'sick' ? 'selected' : '' ?>>Sick</option>
                        <option value="unpaid" <?= $leave['leave_type'] === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" required value="<?= e($leave['start_date']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" required value="<?= e($leave['end_date']) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Reason</label>
                    <textarea name="reason" class="form-control" rows="3" required><?= e($leave['reason']) ?></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="<?= e(APP_URL . '/index.php?action=leave') ?>" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
