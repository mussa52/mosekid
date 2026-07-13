<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Departments</h4>
                <p class="text-muted mb-0">Manage departmental records.</p>
            </div>
            <a href="index.php?action=departments-create" class="btn btn-success"><i class="fa-solid fa-plus me-2"></i>Add Department</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>Name</th><th>Description</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($departments as $department): ?>
                        <tr>
                            <td><?= e($department['name'] ?? '') ?></td>
                            <td><?= e($department['description'] ?? '') ?></td>
                            <td>
                                <a href="index.php?action=departments-edit&id=<?= (int)$department['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="index.php?action=departments-delete&id=<?= (int)$department['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this department?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
