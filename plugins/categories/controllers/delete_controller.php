<?php

if (user_can('delete_category')) {
    $record_id = $req->post('category_id');

    if (csrf_verify($req->post('_token')) && !empty($record_id)) {
        if ($cat->delete($record_id)) {
            message("Category deleted successfully!", "success");
            redirect($admin_route . '/' . $plugin_route . '/list');
        } else {
            message("Failed to delete category.", "fail");
        }
    } else {
        message("Invalid request.", "fail");
    }
}