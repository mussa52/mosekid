<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="mb-3">Edit Attendance</h5>
        <form method="post" action="<?= e(APP_URL . '/index.php?action=attendance-update') ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="id" value="<?= e($row['id']) ?>">

            <div class="mb-3">
                <label class="form-label">Employee</label>
                <div class="form-control-plaintext"><?= e($employeeName) ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Check In</label>
                <input type="time" name="check_in" class="form-control" value="<?= e(date('H:i', strtotime($row['check_in'] ?? '00:00'))) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Check Out</label>
                <input type="time" name="check_out" class="form-control" value="<?= e($row['check_out'] ? date('H:i', strtotime($row['check_out'])) : '') ?>">
            </div>

            <button class="btn btn-primary">Save</button>
            <a href="<?= e(APP_URL . '/index.php?action=attendance') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
