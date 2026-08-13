<?php

if (user_can('delete_redirect') && !empty($id) && csrf_verify($req->post('_token') ?? null)) {
    $redirects = new \Redirects\Redirects;

    if ($redirects->delete($id)) {
        message("Redirect deleted successfully!", "success");
    } else {
        message("Failed to delete redirect.", "fail");
    }
} else {
    message("Invalid request.", "fail");
}

redirect($admin_route . '/' . $plugin_route);
