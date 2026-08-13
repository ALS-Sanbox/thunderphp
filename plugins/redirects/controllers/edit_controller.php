<?php

if (user_can('edit_redirect') && !empty($id)) {
    $postdata = $req->post();

    if (csrf_verify($postdata['_token'] ?? null)) {
        $redirects = new \Redirects\Redirects;

        $data = [
            'from_path'     => ltrim(trim((string) ($postdata['from_path'] ?? '')), '/'),
            'to_path'       => trim((string) ($postdata['to_path'] ?? '')),
            'redirect_type' => in_array($postdata['redirect_type'] ?? '', ['301', '302'], true) ? $postdata['redirect_type'] : '301',
            'disabled'      => empty($postdata['active']) ? 1 : 0,
        ];

        if ($redirects->validate_insert($data)) {
            if ($redirects->update($id, $data)) {
                message("Redirect updated successfully!", "success");
            } else {
                message("Failed to update redirect.", "fail");
            }
        } else {
            message(implode(' ', $redirects->errors), 'fail');
        }
    } else {
        message("Form expired! Please refresh the page.", "fail");
    }
}

redirect($admin_route . '/' . $plugin_route);
