<?php

if (user_can('add_category')) {

    $req = new \Core\Request;
    $postdata = $req->post();

    if (csrf_verify($postdata['_token']) && $cat->validate_insert($postdata)) {

        $slug = !empty($postdata['slug']) 
            ? $cat->makeslug($postdata['slug']) 
            : $cat->makeslug($postdata['category']);

        $postdata['slug'] = $slug;

        if ($cat->exists(['category' => $postdata['category']])) {
            message("Category already exists.", "fail");
            redirect($admin_route . '/' . $plugin_route . '/add/');
        } else {
            $cat->insert_category($postdata);
            message("Category added successfully!", "success");
            redirect($admin_route . '/' . $plugin_route . '/view/' . $cat->insert_id);
        }
    }

    message(implode(' ', $cat->errors), 'fail');
}
