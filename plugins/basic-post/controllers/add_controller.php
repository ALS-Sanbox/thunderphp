<?php
 
if (user_can('add_post')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postdata = $req->post();

        if (csrf_verify($postdata['_token'] ?? '')) {
            $data = [
                'user_id'         => $user_id,
                'title'           => trim($postdata['title'] ?? ''),
                'description'     => trim($postdata['description'] ?? ''),
                'slug'            => $postdata['slug']
                    ? $posts->makeSlug(trim($postdata['slug']))
                    : $posts->makeSlug(trim($postdata['title'])),
                'keywords'        => trim($postdata['keywords'] ?? ''),
                'categories' => isset($postdata['categories']) ? json_encode($postdata['categories']) : json_encode([]),
                'content'         => $postdata['content'] ?? '',
                'disabled'        => !empty($postdata['active']) ? 0 : 1,
				'pop'         => !empty($postdata['pop']) ? 1 : 0,
                'date_created'    => date("Y-m-d H:i:s"),
            ];

            if ($posts->validate_insert($data)) {
                if ($posts->insert($data)) {
                    message("Post added successfully!", "success");
                    redirect($admin_route . '/' . $plugin_route);
                } else {
                    message("Failed to save the post. Please try again.", "fail");
                }
            } else {
                message(implode(' ', $posts->errors), 'fail');
            }
        } else {
            $info['errors'] = "Invalid CSRF token or file error";
        }
    }
}
