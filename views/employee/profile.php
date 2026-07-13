<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h4 class="mb-1">Employee Profile</h4>
                <p class="text-muted mb-0">Details for <?= e(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? '')) ?></p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card bg-light border-0">
                    <div class="card-body text-center">
                        <?php if (!empty($employee['profile_image'])): ?>

    <img
        src="<?= APP_URL ?>/uploads/profiles/<?= e($employee['profile_image']) ?>"
        alt="Profile Image"
        class="rounded-circle border shadow-sm"
        style="width:100px;height:100px;object-fit:cover;">

<?php else: ?>

    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center"
         style="width:100px;height:100px;font-size:2rem;">
        <?= e(strtoupper(substr($employee['first_name'] ?? 'E',0,1) . substr($employee['last_name'] ?? 'M',0,1))) ?>
    </div>

<?php endif; ?>
                        <h5 class="mt-3 mb-1"><?= e(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? '')) ?></h5>
                        <p class="text-muted mb-0"><?= e($employee['status'] ?? 'Active') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-sm-6">
                                <strong>Employee Code</strong>
                                <p class="mb-0"><?= e($employee['employee_code'] ?? '') ?></p>
                            </div>
                            <div class="col-sm-6">
                                <strong>Department</strong>
                                <p class="mb-0"><?= e($departmentName ?: $employee['department_id'] ?? '') ?></p>
                            </div>
                            <div class="col-sm-6">
                                <strong>Position</strong>
                                <p class="mb-0"><?= e($positionName ?: $employee['position_id'] ?? '') ?></p>
                            </div>
                            <div class="col-sm-6">
                                <strong>Gender</strong>
                                <p class="mb-0"><?= e($employee['gender'] ?? '') ?></p>
                            </div>
                            <div class="col-sm-6">
                                <strong>Date of Birth</strong>
                                <p class="mb-0"><?= e($employee['date_of_birth'] ?? '') ?></p>
                            </div>
                            <div class="col-sm-6">
                                <strong>National ID</strong>
                                <p class="mb-0"><?= e($employee['national_id'] ?? '') ?></p>
                            </div>
                            <div class="col-sm-6">
                                <strong>Phone</strong>
                                <p class="mb-0"><?= e($employee['phone_number'] ?? '') ?></p>
                            </div>
                            <div class="col-sm-6">
                                <strong>Email</strong>
                                <p class="mb-0"><?= e($employee['email'] ?? '') ?></p>
                            </div>
                            <div class="col-sm-6">
                                <strong>Salary</strong>
                                <p class="mb-0">Tsh <?= e(number_format((float)($employee['salary'] ?? 0), 2)) ?></p>
                            </div>
                            <div class="col-sm-6">
                                <strong>Employment Date</strong>
                                <p class="mb-0"><?= e($employee['employment_date'] ?? '') ?></p>
                            </div>
                            <div class="col-12">
                                <strong>Address</strong>
                                <p class="mb-0"><?= nl2br(e($employee['address'] ?? '')) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
