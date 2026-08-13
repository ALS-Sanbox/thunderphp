<?php

if (user_can('manage_header_footer')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postdata = $req->post();

        if (csrf_verify($postdata['_token'] ?? '')) {
            $settingsDb = new \Core\Database;
            $action = $postdata['action'] ?? 'save';

            if ($action === 'reset') {
                $settingsDb->query("DELETE FROM settings WHERE `key` = ?", ['header_layout']);
                $saved = !$settingsDb->has_error;

                message($saved ? "Header reset to default." : "Failed to reset header. Please try again.", $saved ? "success" : "fail");
            } else {
                $layoutJson = json_encode([
                    'html' => $postdata['layout_html'] ?? '',
                    'css'  => $postdata['layout_css'] ?? '',
                ]);

                $existing = $settingsDb->fetch("SELECT id FROM settings WHERE `key` = ?", ['header_layout']);

                if ($existing) {
                    $settingsDb->query("UPDATE settings SET `value` = ?, `type` = 'json' WHERE id = ?", [$layoutJson, $existing->id]);
                    $saved = !$settingsDb->has_error;
                } else {
                    $settingsDb->query("INSERT INTO settings (`key`, `value`, `type`, `environment`) VALUES (?, ?, 'json', 'production')", ['header_layout', $layoutJson]);
                    $saved = !$settingsDb->has_error;
                }

                message($saved ? "Header saved successfully!" : "Failed to save header. Please try again.", $saved ? "success" : "fail");
            }
        } else {
            message("Form expired! Please refresh", "fail");
        }

        redirect($admin_route . '/' . $plugin_route . '/header');
    }
}
