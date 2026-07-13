<div class="card shadow-sm border-0">
    <div class="card-body">
        <h4 class="mb-3"><?= isset($department) ? 'Edit Department' : 'Add Department' ?></h4>
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
        <form method="post" action="index.php?action=<?= isset($department) ? 'departments-edit' : 'departments-create' ?><?= isset($department) ? '&id=' . (int)$department['id'] : '' ?>" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="col-md-6"><input class="form-control" type="text" name="name" placeholder="Department Name" value="<?= e($department['name'] ?? '') ?>" required></div>
            <div class="col-12"><textarea class="form-control" name="description" placeholder="Description" rows="3"><?= e($department['description'] ?? '') ?></textarea></div>
            <div class="col-12"><button type="submit" class="btn btn-success">Save</button></div>
        </form>
    </div>
</div>
