<?php
if (user_can('add_page')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && user_can('add_page')) {
        $info = [];
        $info['success'] = false;
        $info['message'] = "";
        $info['errors'] = [];

        $postdata = $req->post();
        $filedata = $req->files();
        $files_ok = true;
    
        if (csrf_verify($postdata['_token'])) {
            $data = [
                'user_id' => $user_id, 
                'title' => trim($postdata['title'] ?? ''),
                'description' => trim($postdata['description'] ?? ''),
                'slug' => $page->makeSlug($postdata['title']),
                'views' => (int)($postdata['views'] ?? 0),
                'disabled' => !empty($postdata['active']) ? 1 : 0,
                'date_created' => date("Y-m-d H:i:s")
            ];
    
            if ($page->validate_insert($data)) {
                $page->insert($data);
                $info['success'] = true;
                $info['message'] = "Page created successfully!";
                //message("Page created successfully!", "success");
                //redirect($admin_route . '/' . $plugin_route . '/view/' . $page->insert_id);
            } else {
                $info['errors'] = $page->errors;
                echo json_encode($info);
            }
        } else {
            message("Invalid CSRF token or file error", 'fail');
        }
    }

}
