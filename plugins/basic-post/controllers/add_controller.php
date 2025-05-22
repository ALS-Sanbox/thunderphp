<?php

if (user_can('add_page')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postdata = $req->post();
        $filedata = $req->files();
        
        $useAdvanced = !empty($postdata['advanced']) && $postdata['advanced'] == '1';

        // Only process basic content if not using advanced mode
        if (!$useAdvanced) {
            $postdata['column1_content'] = $content->extract_images($postdata['column1_content'] ?? '');
        }

        if (csrf_verify($postdata['_token'] ?? '')) {
            $data = [
                'user_id'         => $user_id,
                'title'           => trim($postdata['title'] ?? ''),
                'description'     => trim($postdata['description'] ?? ''),
                'slug'            => $postdata['slug']
                    ? $page->makeSlug(trim($postdata['slug']))
                    : $page->makeSlug(trim($postdata['title'])),
                'keywords'        => trim($postdata['keywords'] ?? ''),
                'categories'      => trim($postdata['categories'] ?? ''),
                'views'           => (int)($postdata['views'] ?? 0),
                'content'         => $postdata['column1_content'] ?? '',
                'advancedcontent' => $postdata['advancedcontent'] ?? '',
                'disabled'        => !empty($postdata['active']) ? 0 : 1,
                'advanced'        => $useAdvanced ? 1 : 0,
                'date_created'    => date("Y-m-d H:i:s"),
            ];

            if ($page->validate_insert($data)) {
                $page->insert($data);
                message("Page added successfully!", "success");
                redirect($admin_route . '/' . $plugin_route);
            } else {
                message(implode(' ', $page->errors), 'fail');
            }
        } else {
            $info['errors'] = "Invalid CSRF token or file error";
        }
    }
}
