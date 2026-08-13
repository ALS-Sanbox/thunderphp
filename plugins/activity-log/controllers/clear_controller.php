<?php

if (user_can('clear_activity_log') && csrf_verify($req->post('_token') ?? null)) {
    (new \ActivityLog\ActivityLog)->query("DELETE FROM activity_log");
    message("Activity log cleared.", "success");
}

redirect($admin_route . '/' . $plugin_route);
