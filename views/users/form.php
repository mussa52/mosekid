<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Edit User</h4>
                <p class="text-muted mb-0">Update the user account details below.</p>
            </div>
        </div>
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

        <form method="post" action="<?= e(APP_URL . '/index.php?action=users-edit&id=' . ($user['id'] ?? 0)) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?= e($user['first_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?= e($user['last_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <select name="role_id" class="form-select" required>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= e($role['id']) ?>" <?= isset($user['role_id']) && $user['role_id'] == $role['id'] ? 'selected' : '' ?>>
                                <?= e(ucwords(str_replace('_', ' ', $role['name']))) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="active" <?= ($user['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="<?= e(APP_URL . '/index.php?action=users') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
