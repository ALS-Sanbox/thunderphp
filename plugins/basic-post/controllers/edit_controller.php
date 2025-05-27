<?php
if (user_can('edit_page')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postdata = $req->post();
        $post_id = $row->id;
        $existing = $posts->find($post_id); 
        
        if (!$existing) {
            message("Page not found", "fail");
            return;
        }

        if (csrf_verify($postdata['_token'])) {
            $submitted_slug = trim($postdata['slug'] ?? '');
            $original_slug = $existing->slug;
            
            $new_data = [
                'title' => trim($postdata['title'] ?? $existing->title),
                'description' => trim($postdata['description'] ?? $existing->description),
                'slug' => ($submitted_slug && $submitted_slug !== $original_slug) 
                    ? $posts->makeSlug($submitted_slug) 
                    : $original_slug,
                'keywords' => trim($postdata['keywords'] ?? $existing->keywords),
                'categories' => isset($postdata['categories']) ? json_encode($postdata['categories']) : json_encode([]),
                'views' => (int)($postdata['views'] ?? $existing->views),
                'content' => trim($postdata['content'] ?? $existing->content),
                'disabled' => !empty($postdata['active']) ? 0 : 1,
            ];

            // Prepare current data for comparison
            $current_data = [
                'title' => $existing->title,
                'description' => $existing->description,
                'slug' => $existing->slug,
                'keywords' => $existing->keywords,
                'categories' => $existing->categories,
                'views' => (int)$existing->views,
                'content' => $existing->content,
                'disabled' => (int)$existing->disabled,
            ];

            // Check if data has changed
            if ($new_data != $current_data) {
                $new_data['date_updated'] = date("Y-m-d H:i:s");

                if ($posts->validate_update($new_data)) {
                    $posts->update_post($post_id, $new_data);
                    message("Page updated successfully!", "success");
                } else {
                    message(implode(' ', $posts->errors), 'fail');
                }
            } else {
                message("No changes detected.", "info");
            }
        } else {
            $info['errors'] = "Invalid CSRF token or file error";
        }
    }
}
