<?php

if (user_can('edit_settings')) {
    $req = new \Core\Request;
    $postdata = $req->post();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (csrf_verify($postdata['_token'])) {

            if ($set->validate_settings_data($postdata)) {
                if ($set->update_settings($postdata)) {
                    message("Settings updated successfully!", "success");
					redirect($admin_route . '/' . $plugin_route);
                } else {
                    message("Update failed: " . implode(', ', $set->errors), "fail");
                }
            } else {
                message("Validation errors: " . implode(', ', $set->errors), "fail");
            }

        } else {
            message("Invalid CSRF token", "fail");
        }
    }
}