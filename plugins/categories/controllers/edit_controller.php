<?php

if (user_can('edit_category')) {

    $req = new \Core\Request;
    $postdata = $req->post();
    $existing = $cat->find($row->id);

    if (!$existing) {
        message("Category not found", "fail");
        return;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        if (csrf_verify($postdata['_token'])) {
            $new_slug = $postdata['slug'];

            if ($new_slug !== $existing->slug) {
                $postdata['slug'] = $new_slug;
            } else {
                unset($postdata['slug']);
            }
            
            if(empty($postdata['parent_id']))
                $postdata['parent_id'] = null;

            if ($cat->validate_update($postdata)) {

                $cat->update_category($row->id, $postdata);

                message("Category updated successfully!", "success");
                redirect($admin_route . '/' . $plugin_route . '/view/' . $row->id);
            }

            message(implode(' ', $cat->errors), 'fail');

        } else {
            message("Invalid CSRF token", "fail");
        }
    }
}
