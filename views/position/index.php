<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Positions</h4>
                <p class="text-muted mb-0">Manage job titles and designations.</p>
            </div>
            <a href="index.php?action=positions-create" class="btn btn-warning"><i class="fa-solid fa-plus me-2"></i>Add Position</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>Name</th><th>Description</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($positions as $position): ?>
                        <tr>
                            <td><?= e($position['name'] ?? '') ?></td>
                            <td><?= e($position['description'] ?? '') ?></td>
                            <td>
                                <a href="index.php?action=positions-edit&id=<?= (int)$position['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="index.php?action=positions-delete&id=<?= (int)$position['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this position?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
