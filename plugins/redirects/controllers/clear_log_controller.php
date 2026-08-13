<?php

if (user_can('view_redirects') && csrf_verify($req->post('_token') ?? null)) {
    $notFoundLog = new \Redirects\NotFoundLog;
    $notFoundLog->query("DELETE FROM not_found_log");
    message("404 log cleared.", "success");
}

redirect($admin_route . '/' . $plugin_route);
