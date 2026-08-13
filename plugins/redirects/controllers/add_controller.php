<?php

if (user_can('add_redirect')) {
    $postdata = $req->post();

    if (csrf_verify($postdata['_token'] ?? null)) {
        $redirects = new \Redirects\Redirects;

        $data = [
            'from_path'     => ltrim(trim((string) ($postdata['from_path'] ?? '')), '/'),
            'to_path'       => trim((string) ($postdata['to_path'] ?? '')),
            'redirect_type' => in_array($postdata['redirect_type'] ?? '', ['301', '302'], true) ? $postdata['redirect_type'] : '301',
            'disabled'      => empty($postdata['active']) ? 1 : 0,
            'date_created'  => date('Y-m-d H:i:s'),
        ];

        if ($redirects->validate_insert($data)) {
            if ($redirects->findByPath($data['from_path']) || $redirects->first(['from_path' => $data['from_path']])) {
                message("A redirect for that path already exists.", "fail");
            } elseif ($redirects->create($data)) {
                message("Redirect added successfully!", "success");
                redirect($admin_route . '/' . $plugin_route);
            } else {
                message("Failed to add redirect.", "fail");
            }
        } else {
            message(implode(' ', $redirects->errors), 'fail');
        }
    } else {
        message("Form expired! Please refresh the page.", "fail");
    }
}
