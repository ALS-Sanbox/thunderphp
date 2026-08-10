<?php

if (user_can('manage_post_layout')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postdata = $req->post();

        if (csrf_verify($postdata['_token'] ?? '')) {
            $layoutJson = json_encode([
                'html' => $postdata['layout_html'] ?? '',
                'css'  => $postdata['layout_css'] ?? '',
            ]);

            $settingsDb = new \Core\Database;
            $existing = $settingsDb->fetch("SELECT id FROM settings WHERE `key` = ?", ['post_default_layout']);

            if ($existing) {
                $settingsDb->query("UPDATE settings SET `value` = ?, `type` = 'json' WHERE id = ?", [$layoutJson, $existing->id]);
                $saved = !$settingsDb->has_error;
            } else {
                $settingsDb->query("INSERT INTO settings (`key`, `value`, `type`, `environment`) VALUES (?, ?, 'json', 'production')", ['post_default_layout', $layoutJson]);
                $saved = !$settingsDb->has_error;
            }

            if ($saved) {
                message("Layout saved successfully!", "success");
            } else {
                message("Failed to save layout. Please try again.", "fail");
            }
        } else {
            message("Form expired! Please refresh", "fail");
        }

        redirect($admin_route . '/' . $plugin_route . '/layout');
    }
}
