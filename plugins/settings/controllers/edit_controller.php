<?php

if (user_can('edit_settings')) {
    $req = new \Core\Request;
    $postdata = $req->post();
    $filedata = $req->files();
    $files_ok = true;

    if (!empty($filedata['site_logo']) && $filedata['site_logo']['error'] != UPLOAD_ERR_NO_FILE) {
        $logoPath = $req->upload_files('site_logo');

        if (!is_array($logoPath)) {
            $postdata['site_logo'] = $logoPath;
        } else {
            $files_ok = false;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (csrf_verify($postdata['_token'])) {

            if (isset($postdata['smtp_password']) && $postdata['smtp_password'] === '') {
                unset($postdata['smtp_password']);
            }

            if (!$files_ok) {
                message(implode(' ', $req->upload_errors), 'fail');
            } elseif ($set->validate_settings_data($postdata)) {
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