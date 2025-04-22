<?php

if(user_can('delete_user')){
    $record_id = $req->post('user_id');
    
    if (csrf_verify($req->post('_token')) && !empty($record_id)) {
        if ($user->delete($record_id)) {
            message("Record deleted successfully!", "success");
            redirect($admin_route . '/' . $plugin_route . '/list');
        } else {
            message("Failed to delete record.", "fail");
        }
    } else {
        message("Invalid request.", "fail");
    }
}
