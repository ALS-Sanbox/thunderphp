<?php

namespace App\Controllers;

use App\Models\SampleModel;

class SampleController
{
    // Render the index view
    public function index()
    {
        $model = new SampleModel();
        $data = $model->getAll(); // Fetch all records
        $this->render('index', ['data' => $data]);
    }

    // Handle creating new resources
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new SampleModel();
            $data = $_POST; // Sanitize input in production
            if ($model->insert($data)) {
                header("Location: /success");
                exit;
            } else {
                $this->render('create', ['errors' => $model->errors]);
            }
        } else {
            $this->render('create');
        }
    }

    // Handle editing resources
    public function edit($id)
    {
        $model = new SampleModel();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST; // Sanitize input
            if ($model->update_user($id, $data)) {
                header("Location: /success");
                exit;
            } else {
                $this->render('edit', ['errors' => $model->errors]);
            }
        } else {
            $data = $model->getById($id);
            $this->render('edit', ['data' => $data]);
        }
    }

    // Handle deleting resources
    public function delete($id)
    {
        $model = new SampleModel();
        if ($model->delete($id)) {
            header("Location: /success");
            exit;
        } else {
            $this->render('error', ['message' => 'Failed to delete the record.']);
        }
    }

    // Helper method to render view files
    private function render($view, $data = [])
    {
        extract($data);
        require_once plugin_path("views/{$view}.php");
    }
}

// Routing Example
$requestUri = $_SERVER['REQUEST_URI'];
$controller = new SampleController();

switch ($requestUri) {
    case '/index':
        $controller->index();
        break;
    case '/create':
        $controller->create();
        break;
    case '/edit':
        $id = $_GET['id'] ?? null;
        $controller->edit($id);
        break;
    case '/delete':
        $id = $_GET['id'] ?? null;
        $controller->delete($id);
        break;
    default:
        echo "404 Not Found";
        break;
}
