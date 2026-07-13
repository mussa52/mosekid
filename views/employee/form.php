<div class="card shadow-sm border-0">
    <div class="card-body">
        <h4 class="mb-3"><?= isset($employee) ? 'Edit Employee' : 'Add Employee' ?></h4>
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
        <form method="post"
      enctype="multipart/form-data"
      action="index.php?action=<?= isset($employee) ? 'employees-edit' : 'employees-create' ?><?= isset($employee) ? '&id=' . (int)$employee['id'] : '' ?>"
      class="row g-3">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="col-md-6"><input class="form-control" type="text" name="employee_code" placeholder="Employee Code" value="<?= e($employee['employee_code'] ?? '') ?>" required></div>
            <div class="col-md-6"><input class="form-control" type="text" name="first_name" placeholder="First Name" value="<?= e($employee['first_name'] ?? '') ?>" required></div>
            <div class="col-md-6"><input class="form-control" type="text" name="last_name" placeholder="Last Name" value="<?= e($employee['last_name'] ?? '') ?>" required></div>
            <div class="col-md-6"><select class="form-select" name="gender"><option value="male" <?= (($employee['gender'] ?? '') === 'male') ? 'selected' : '' ?>>Male</option><option value="female" <?= (($employee['gender'] ?? '') === 'female') ? 'selected' : '' ?>>Female</option></select></div>
            <div class="col-md-6">
                <label class="form-label" for="date_of_birth">Date of Birth</label>
                <input class="form-control" type="date" id="date_of_birth" name="date_of_birth" value="<?= e($employee['date_of_birth'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="employment_date">Employment Date</label>
                <input class="form-control" type="date" id="employment_date" name="employment_date" value="<?= e($employee['employment_date'] ?? '') ?>" required>
            </div>
            <div class="col-md-6"><input class="form-control" type="text" name="national_id" placeholder="National ID" value="<?= e($employee['national_id'] ?? '') ?>" required></div>
            <div class="col-md-6"><input class="form-control" type="text" name="phone_number" placeholder="Phone Number" value="<?= e($employee['phone_number'] ?? '') ?>" required></div>
            <div class="col-md-6"><input class="form-control" type="email" name="email" placeholder="Email" value="<?= e($employee['email'] ?? '') ?>" required></div>
            <div class="col-12"><textarea class="form-control" name="address" placeholder="Address" required><?= e($employee['address'] ?? '') ?></textarea></div>
            <div class="col-md-6"><input class="form-control" type="number" step="0.01" name="salary" placeholder="Salary (Tsh)" value="<?= e($employee['salary'] ?? '') ?>" required></div>
            <div class="col-md-6">
                <label class="form-label" for="department_id">Department</label>
                <select class="form-select" id="department_id" name="department_id" required>
                    <option value="">Select department</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?= e($department['id']) ?>" <?= ((string)($employee['department_id'] ?? '') === (string)$department['id']) ? 'selected' : '' ?>><?= e($department['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="position_id">Position</label>
                <select class="form-select" id="position_id" name="position_id" required>
                    <option value="">Select position</option>
                    <?php foreach ($positions as $position): ?>
                        <option value="<?= e($position['id']) ?>" <?= ((string)($employee['position_id'] ?? '') === (string)$position['id']) ? 'selected' : '' ?>><?= e($position['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
    <label class="form-label">Profile Image</label>

    <input
        type="file"
        name="profile_image"
        class="form-control"
        accept="image/*">
</div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Save</button></div>
        </form>
    </div>
</div>

