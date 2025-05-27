<?php
if (user_can('delete_post')) {
    $record_id = $req->post('post_id');

    if (csrf_verify($req->post('_token')) && !empty($record_id)) {

        // Load content before deleting
        $postsData = $posts->first(['id' => $record_id]);

        // Delete the post record
        if ($posts->delete($record_id)) {
            message("Record deleted successfully!", "success");
            redirect($admin_route . '/' . $plugin_route . '/list');
        } else {
            message("Failed to delete record.", "fail");
        }
    } else {
        message("Invalid request.", "fail");
    }
}