<?php
declare(strict_types=1);

namespace Controller;

use App\Validator;
use Model\DepartmentModel;
use Model\EmployeeModel;
use Model\PositionModel;

class EmployeeController
{
    public function index(): void
    {
        $model = new EmployeeModel();
        $employees = $model->findAll();
        renderView('employee/index.php', compact('employees'), 'Employees', 'employee');
    }

    public function create(): void
    {
        $departmentModel = new DepartmentModel();
        $positionModel = new PositionModel();
        $employeeModel = new EmployeeModel();

        $departments = $departmentModel->findAll();
        $positions = $positionModel->findAll();
        $employee = null;

        $missingRequirements = [];
        if (empty($departments)) {
            $missingRequirements[] = 'Please create a department before adding an employee.';
        }
        if (empty($positions)) {
            $missingRequirements[] = 'Please create a position before adding an employee.';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                verify_csrf();
                $data = $_POST;
                // Upload profile image
if (
    isset($_FILES['profile_image']) &&
    $_FILES['profile_image']['error'] === UPLOAD_ERR_OK
) {
    $uploadDir = __DIR__ . '/../uploads/profiles/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (in_array($extension, $allowed)) {

        $fileName = uniqid('emp_') . '.' . $extension;

        move_uploaded_file(
            $_FILES['profile_image']['tmp_name'],
            $uploadDir . $fileName
        );

        $data['profile_image'] = $fileName;
    }
}

                if (!empty($missingRequirements)) {
                    $errors = $missingRequirements;
                } else {
                    $errors = Validator::required($data, ['employee_code', 'first_name', 'last_name', 'gender', 'date_of_birth', 'employment_date', 'national_id', 'phone_number', 'email', 'address', 'department_id', 'position_id', 'salary']);
                    if (!Validator::email($data['email'] ?? '')) {
                        $errors[] = 'Email is invalid.';
                    }
                    if (!Validator::phone($data['phone_number'] ?? '')) {
                        $errors[] = 'Phone number is invalid.';
                    }
                    if (empty($data['department_id']) || !ctype_digit((string) $data['department_id']) || $departmentModel->findById((int) $data['department_id']) === null) {
                        $errors[] = 'Please select a valid department.';
                    }
                    if (empty($data['position_id']) || !ctype_digit((string) $data['position_id']) || $positionModel->findById((int) $data['position_id']) === null) {
                        $errors[] = 'Please select a valid position.';
                    }
                }

                if (!empty($errors)) {
                    $_SESSION['errors'] = $errors;
                    $employee = $data;
                } else {
                    $employeeModel->create($data);
                    redirect('/index.php?action=employees');
                }
            } catch (\Throwable $e) {
                $_SESSION['errors'] = [$e->getMessage()];
                $employee = $_POST;
            }
        }

        renderView('employee/form.php', compact('employee', 'departments', 'positions', 'missingRequirements'), 'Employee Form', 'employee');
    }

    public function edit(int $id): void
    {
        $departmentModel = new DepartmentModel();
        $positionModel = new PositionModel();
        $departments = $departmentModel->findAll();
        $positions = $positionModel->findAll();
        $model = new EmployeeModel();
        $employee = $model->findById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                verify_csrf();
                $data = $_POST;
                $errors = Validator::required($data, ['employee_code', 'first_name', 'last_name', 'gender', 'date_of_birth', 'employment_date', 'national_id', 'phone_number', 'email', 'address', 'department_id', 'salary']);
                if (!Validator::email($data['email'] ?? '')) {
                    $errors[] = 'Email is invalid.';
                }
                if (!Validator::phone($data['phone_number'] ?? '')) {
                    $errors[] = 'Phone number is invalid.';
                }
                if (empty($data['department_id']) || !ctype_digit((string) $data['department_id']) || $departmentModel->findById((int) $data['department_id']) === null) {
                    $errors[] = 'Please select a valid department.';
                }
                if (empty($data['position_id']) || !ctype_digit((string) $data['position_id']) || $positionModel->findById((int) $data['position_id']) === null) {
                    $errors[] = 'Please select a valid position.';
                }
                if ($errors) {
                    $_SESSION['errors'] = $errors;
                    $employee = array_merge($employee ?? [], $data);
                } else {
                    $model->update($id, $data);
                    redirect('/index.php?action=employees');
                }
            } catch (\Throwable $e) {
                $_SESSION['errors'] = [$e->getMessage()];
                $employee = array_merge($employee ?? [], $_POST);
            }
        }

        renderView('employee/form.php', compact('employee', 'departments', 'positions'), 'Employee Form', 'employee');
    }

    public function delete(int $id): void
    {
        $model = new EmployeeModel();
        $model->delete($id);
        redirect('/index.php?action=employees');
    }

    public function search(): void
    {
        $term = $_GET['q'] ?? '';
        $model = new EmployeeModel();
        $employees = $model->search($term);
        renderView('employee/index.php', compact('employees'), 'Search Results', 'employee');
    }

    public function profile(?int $id = null): void
    {
        $model = new EmployeeModel();
        if (empty($id) || $id <= 0) {
            $employee = getCurrentEmployeeByUserEmail();
        } else {
            $employee = $model->findById($id);
        }

        if (!$employee) {
            $_SESSION['error'] = 'Employee profile not found.';
            redirect('/index.php?action=dashboard');
        }

        $departmentName = '';
        $positionName = '';

        if (!empty($employee['department_id'])) {
            $department = (new DepartmentModel())->findById((int) $employee['department_id']);
            $departmentName = $department['name'] ?? '';
        }

        if (!empty($employee['position_id'])) {
            $position = (new PositionModel())->findById((int) $employee['position_id']);
            $positionName = $position['name'] ?? '';
        }

        renderView('employee/profile.php', compact('employee', 'departmentName', 'positionName'), 'Employee Profile', 'employee');
    }
}
