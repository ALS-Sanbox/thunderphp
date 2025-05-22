<?php
if (user_can('edit_page')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postdata = $req->post();
        $filedata = $req->files();
        $files_ok = true;
        $page_id = $row->id;
        $existing = $page->find($page_id); 
        $useAdvanced = !empty($postdata['advanced']) && $postdata['advanced'] == '1';

        if (!$existing) {
            message("Page not found", "fail");
            return;
        }

        $old_content = $existing->content;

        if (!$useAdvanced) {
            $postdata['column1_content'] = $content->extract_images($postdata['column1_content'] ?? '');
            $new_content = $postdata['column1_content'];
        } else {
            $new_content = $existing->content;
        }
        if (csrf_verify($postdata['_token'])) {
            $submitted_slug = trim($postdata['slug'] ?? '');
            $original_slug = $existing->slug;
            
            $data = [
                'title' => trim($postdata['title'] ?? $existing->title),
                'description' => trim($postdata['description'] ?? $existing->description),
                'slug' => ($submitted_slug && $submitted_slug !== $original_slug) 
                ? $page->makeSlug($submitted_slug) 
                : $original_slug,
                'keywords' => trim($postdata['keywords'] ?? $existing->keywords),
                'categories' => trim($postdata['categories'] ?? $existing->categories),
                'views' => (int)($postdata['views'] ?? $existing->views),
                'content' => !$useAdvanced ? $new_content : $existing->content,
                'advancedcontent' => ($useAdvanced && !empty($postdata['advancedcontent'])) ? $postdata['advancedcontent'] : $existing->advancedcontent,
                'advanced'        => $useAdvanced ? 1 : 0,
                'disabled' => !empty($postdata['active']) ? 0 : 1,
                'date_updated' => date("Y-m-d H:i:s")
            ];
            
            if ($page->validate_update($data)) {
                $content->delete_unsued_images($old_content, $new_content);
                $page->update_page($page_id, $data);
                message("Page updated successfully!", "success");
            } else {
                message(implode(' ', $page->errors), 'fail');
            }
        } else {
            $info['errors'] = "Invalid CSRF token or file error";
        }
    }
}
