<?php
declare(strict_types=1);

namespace Controller;

use Model\PositionModel;

class PositionController
{
    public function index(): void
    {
        $model = new PositionModel();
        $positions = $model->findAll();
        renderView('position/index.php', compact('positions'), 'Positions', 'position');
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $model = new PositionModel();
            $model->create($_POST);
            redirect('/index.php?action=positions');
        }
        renderView('position/form.php', ['position' => $position ?? null], 'Position Form', 'position');
    }

    public function edit(int $id): void
    {
        $model = new PositionModel();
        $position = $model->findById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $model->update($id, $_POST);
            redirect('/index.php?action=positions');
        }
        renderView('position/form.php', compact('position'), 'Position Form', 'position');
    }

    public function delete(int $id): void
    {
        $model = new PositionModel();
        $model->delete($id);
        redirect('/index.php?action=positions');
    }

    public function search(): void
    {
        $term = $_GET['q'] ?? '';
        $model = new PositionModel();
        $positions = $model->search($term);
        renderView('position/index.php', compact('positions'), 'Search Results', 'position');
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
            $model = new PositionModel();
            
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (empty($name)) {
                echo json_encode(['success' => false, 'message' => 'Position name is required']);
                exit;
            }
            
            // Check if position already exists
            $existing = $model->findByName($name);
            if ($existing) {
                echo json_encode(['success' => false, 'message' => 'Position already exists']);
                exit;
            }
            
            $model->create(['name' => $name, 'description' => $description]);
            
            // Fetch the newly created position to get its ID
            $newPosition = $model->findByName($name);
            
            echo json_encode([
                'success' => true,
                'position_id' => $newPosition['id'],
                'position_name' => $name
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
