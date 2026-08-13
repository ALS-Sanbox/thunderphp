<?php

// plugin.php

/**
 * Plugin Name: Profiles
 * Description: Self-service "my profile" page - name, avatar, bio, and website.
 * Version: 1.0
 * Author: Afro Lion Studios
 */

set_value([
    'plugin_route' => 'profile',
    'login_page'   => 'login',
]);

$db = new \Core\Database;

if (!$db->tableExists('user_profiles')) {
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",", $db->missing_tables));
    die;
}

add_action('controller', function () {
    $vars = get_value();
    $req = new \Core\Request;

    if (page() === $vars['plugin_route'] && $req->posted()) {
        require plugin_path('controllers/save_controller.php');
    }
});

add_action('view', function () {
    $vars = get_value();

    if (page() === $vars['plugin_route']) {
        require plugin_path('views/profile.php');
    }
});
