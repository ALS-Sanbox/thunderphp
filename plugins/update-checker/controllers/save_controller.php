<?php

if (!user_can('manage_updates')) {
    return;
}

$req = new \Core\Request;
$postdata = $req->post();

if (!csrf_verify($postdata['_token'] ?? null)) {
    message("Form expired! Please refresh the page.", "fail");
    redirect($admin_route . '/' . $plugin_route);
}

$db = new \Core\Database;
$action = $postdata['action'] ?? '';

if ($action === 'check_now') {
    update_checker_run_check($db);
    message("Checked for updates.", "success");
    redirect($admin_route . '/' . $plugin_route);
}

if ($action === 'apply_now') {
    $report = update_checker_apply_update($db);
    message($report['message'], $report['ok'] ? 'success' : 'fail');
    redirect($admin_route . '/' . $plugin_route);
}

update_checker_set_setting($db, 'update_check_enabled', !empty($postdata['update_check_enabled']) ? '1' : '0');
update_checker_set_setting($db, 'update_check_auto_apply', !empty($postdata['update_check_auto_apply']) ? '1' : '0');

message("Settings saved.", "success");
redirect($admin_route . '/' . $plugin_route);
