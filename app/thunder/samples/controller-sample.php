<?php

class SampleController
{
    // Render the index view
    public function index()
    {
        // Fetch data from the model
        $data = (new SampleModel())->getAll();
        // Pass data to the view
        $this->render('index', $data);
    }

    // Handle creating new resources
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new SampleModel();
            $data = $_POST; // Sanitize input in production
            if ($model->insert($data)) {
                header("Location: /success");
            } else {
                $this->render('create', ['errors' => $model->errors]);
            }
        } else {
            $this->render('create');
        }
    }

    // Example edit method
    public function edit($id)
    {
        $model = new SampleModel();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST; // Sanitize input
            if ($model->update_user($id, $data)) {
                header("Location: /success");
            } else {
                $this->render('edit', ['errors' => $model->errors]);
            }
        } else {
            $data = $model->getById($id);
            $this->render('edit', compact('data'));
        }
    }

    // Render view files dynamically
    private function render($view, $data = [])
    {
        extract($data);
        require_once plugin_path("views/{$view}.php");
    }
}
