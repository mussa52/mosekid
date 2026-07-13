<div class="card shadow-sm border-0">
    <div class="card-body">
        <h4 class="mb-3"><?= isset($position) ? 'Edit Position' : 'Add Position' ?></h4>
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
        <form method="post" action="index.php?action=<?= isset($position) ? 'positions-edit' : 'positions-create' ?><?= isset($position) ? '&id=' . (int)$position['id'] : '' ?>" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="col-md-6">
                <input class="form-control" type="text" name="name" placeholder="Position Name" value="<?= e($position['name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <textarea class="form-control" name="description" placeholder="Description" rows="1"><?= e($position['description'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
