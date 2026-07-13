<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">User Accounts</h4>
                <p class="text-muted mb-0">Manage application users and roles.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <?php if (hasPermission('users_edit') || hasPermission('users_delete')): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= e($user['id'] ?? '') ?></td>
                            <td><?= e(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></td>
                            <td><?= e($user['email'] ?? '') ?></td>
                            <td><?= e(ucwords(str_replace('_', ' ', $user['role_name'] ?? ($user['role_id'] ?? '')))) ?></td>
                            <td><span class="badge bg-<?= ($user['status'] === 'active' ? 'success' : 'secondary') ?>"><?= e(ucfirst($user['status'] ?? '')) ?></span></td>
                            <td><?= e(date('Y-m-d', strtotime($user['created_at'] ?? 'now'))) ?></td>
                            <?php if (hasPermission('users_edit') || hasPermission('users_delete')): ?>
                                <td>
                                    <?php if (hasPermission('users_edit')): ?>
                                        <a href="<?= e(APP_URL . '/index.php?action=users-edit&id=' . ($user['id'] ?? 0)) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <?php endif; ?>
                                    <?php if (hasPermission('users_delete')): ?>
                                        <form method="post" action="<?= e(APP_URL . '/index.php?action=users-delete&id=' . ($user['id'] ?? 0)) ?>" class="d-inline-block ms-1" onsubmit="return confirm('Delete this user?');">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
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
