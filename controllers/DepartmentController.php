<?php
declare(strict_types=1);

namespace Controller;

use Model\DepartmentModel;

class DepartmentController
{
    public function index(): void
    {
        $model = new DepartmentModel();
        $departments = $model->findAll();
        renderView('department/index.php', compact('departments'), 'Departments', 'department');
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                verify_csrf();
                $model = new DepartmentModel();
                $model->create($_POST);
                redirect('/index.php?action=departments');
            } catch (\Throwable $e) {
                $_SESSION['errors'] = [$e->getMessage()];
                $department = $_POST;
            }
        }

        renderView('department/form.php', ['department' => $department ?? null], 'Department Form', 'department');
    }

    public function edit(int $id): void
    {
        $model = new DepartmentModel();
        $department = $model->findById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                verify_csrf();
                $model->update($id, $_POST);
                redirect('/index.php?action=departments');
            } catch (\Throwable $e) {
                $_SESSION['errors'] = [$e->getMessage()];
                $department = array_merge($department ?? [], $_POST);
            }
        }
        renderView('department/form.php', compact('department'), 'Department Form', 'department');
    }

    public function delete(int $id): void
    {
        try {
            $model = new DepartmentModel();
            $model->delete($id);

            $_SESSION['success'] = 'Department deleted successfully.';
        } catch (\Throwable $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        redirect('/index.php?action=departments');
    }

    public function search(): void
    {
        $term = $_GET['q'] ?? '';
        $model = new DepartmentModel();
        $departments = $model->search($term);
        renderView('department/index.php', compact('departments'), 'Search Results', 'department');
    }

    public function createApi(): void
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        try {
            verify_csrf();
            $model = new DepartmentModel();
            
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (empty($name)) {
                echo json_encode(['success' => false, 'message' => 'Department name is required']);
                exit;
            }
            
            // Check if department already exists
            $existing = $model->findByName($name);
            if ($existing) {
                echo json_encode(['success' => false, 'message' => 'Department already exists']);
                exit;
            }
            
            $model->create(['name' => $name, 'description' => $description]);
            
            // Fetch the newly created department to get its ID
            $newDepartment = $model->findByName($name);
            
            echo json_encode([
                'success' => true,
                'department_id' => $newDepartment['id'],
                'department_name' => $name
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
