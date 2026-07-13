<?php
declare(strict_types=1);

namespace Controller;

use Model\RoleModel;
use Model\UserModel;

class UserController
{
    public function index(): void
    {
        $model = new UserModel();
        $users = $model->findAll();
        renderView('users/index.php', compact('users'), 'Users', 'users');
    }

    public function edit(int $id): void
    {
        requirePermission('users_edit');

        $model = new UserModel();
        $user = $model->findById($id);
        if (!$user) {
            $_SESSION['errors'] = ['User not found.'];
            redirect('/index.php?action=users');
        }

        $roleModel = new RoleModel();
        $roles = $roleModel->findAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                verify_csrf();
                $updateData = [
                    'role_id' => (int)($_POST['role_id'] ?? $user['role_id']),
                    'first_name' => trim($_POST['first_name'] ?? ''),
                    'last_name' => trim($_POST['last_name'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'status' => trim($_POST['status'] ?? $user['status']),
                    'password' => trim($_POST['password'] ?? ''),
                ];

                if ($updateData['email'] === '') {
                    throw new \RuntimeException('Email is required.');
                }

                $model->update($id, $updateData);
                redirect('/index.php?action=users');
            } catch (\Throwable $e) {
                $_SESSION['errors'] = [$e->getMessage()];
            }
        }

        renderView('users/form.php', compact('user', 'roles'), 'Edit User', 'users');
    }

    public function delete(int $id): void
    {
        requirePermission('users_delete');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        verify_csrf();

        $model = new UserModel();
        $model->delete($id);
        redirect('/index.php?action=users');
    }
}
