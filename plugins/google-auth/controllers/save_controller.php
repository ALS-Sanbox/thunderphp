<?php

if (!user_can('manage_google_auth')) {
    return;
}

$req = new \Core\Request;
$postdata = $req->post();

if (!csrf_verify($postdata['_token'] ?? null)) {
    message("Form expired! Please refresh the page.", "fail");
    redirect($admin_route . '/' . $plugin_route);
}

$db = new \Core\Database;

google_auth_set_setting($db, 'google_oauth_client_id', trim((string) ($postdata['google_oauth_client_id'] ?? '')));

if (trim((string) ($postdata['google_oauth_client_secret'] ?? '')) !== '') {
    google_auth_set_setting($db, 'google_oauth_client_secret', trim($postdata['google_oauth_client_secret']));
}

google_auth_set_setting($db, 'google_oauth_enabled', !empty($postdata['google_oauth_enabled']) ? '1' : '0');

message("Google Sign-In settings saved.", "success");
redirect($admin_route . '/' . $plugin_route);
