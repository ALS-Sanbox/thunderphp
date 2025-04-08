<?php

// plugin.php

/**
 * Plugin Name: basic-auth
 * Description: Control logging in and signup for site
 * Version: 1.0
 * Author: Your Name
 */

 set_value([
    'login_page'         => 'login',
    'logout_page'        => 'logout',
    'signup_page'        => 'signup',
    'forgot_page'        => 'forgot',
    'admin_plugin_route' => 'admin',
    'tables'             => [
        'users_table' => 'siteusers',
    ],
    'optional_tables'    => [
        'roles_table'       => 'user_roles',
        'permissions_table' => 'permission_roles',
        'roles_map_table'   => 'user_roles_map',
    ],
]);

$db = new \Core\Database;
$allTables = array_merge(get_value()['tables'], get_value()['optional_tables']);

if (!$db->tableExists(array_values($allTables))) {
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(", ", $db->missing_tables));
    die;
}

add_filter('user_permissions', function ($permissions) {
    $ses = new \Core\Session;
    if ($ses->is_logged_in()) {
        $db = new \Core\Database;
        $vars = get_value();
        $user_id = $ses->user('id');

        $query = "SELECT role_id FROM {$vars['optional_tables']['roles_map_table']} WHERE user_id = :user_id AND disabled = 0";
        $user_roles = $db->query($query, ['user_id' => $user_id]);

        if (!empty($user_roles)) {
            $role_ids = array_column($user_roles, 'role_id');
            $placeholders = implode(',', array_fill(0, count($role_ids), '?'));

            $query = "SELECT permission FROM {$vars['optional_tables']['permissions_table']} 
                      WHERE role_id IN ($placeholders) AND disabled = 0";
            $perms = $db->query($query, $role_ids);

            if (!empty($perms)) {
                $permissions = array_merge($permissions, array_column($perms, 'permission'));
            }
        } else {
            $permissions[] = '';
        }
    }
    return $permissions;
});


add_action('controller', function () {
    $vars = get_value();
    $req = new \Core\Request;
    $ses = new \Core\Session();

    if ($req->posted()) {
        switch (page()) {
            case $vars['login_page']:
                require plugin_path('controllers/login_controller.php');
                break;
            case $vars['signup_page']:
                require plugin_path('controllers/signup_controller.php');
                break;
            case $vars['forgot_page']:
                require plugin_path('controllers/forgot_controller.php');
                break;
        }
    } elseif (page() === $vars['logout_page'] && $ses->is_logged_in()) {
        require plugin_path('controllers/logout_controller.php');
    }
});

add_filter('header-footer_before_menu_links', function ($links) {
    $ses = new \Core\Session();
    $vars = get_value();

    if (!$ses->is_logged_in()) {
        $links[] = (object)['id' => 1, 'title' => 'Login', 'slug' => $vars['login_page'], 'icon' => '', 'permission' => 'not_logged_in'];
        $links[] = (object)['id' => 2, 'title' => 'Signup', 'slug' => $vars['signup_page'], 'icon' => '', 'permission' => 'not_logged_in'];
    } else {
        $links[] = (object)[
            'id' => 3,
            'title' => 'Hi, ' . htmlspecialchars($ses->user('first_name'), ENT_QUOTES, 'UTF-8'),
            'slug' => 'profile/' . $ses->user('id'),
            'icon' => '',
            'permission' => 'logged_in'
        ];
        $links[] = (object)['id' => 4, 'title' => 'Admin', 'slug' => $vars['admin_plugin_route'], 'icon' => '', 'permission' => 'logged_in'];
        $links[] = (object)['id' => 5, 'title' => 'Logout', 'slug' => $vars['logout_page'], 'icon' => '', 'permission' => 'logged_in'];
    }
    return $links;
});

add_action('view', function () {
    $vars = get_value();
    switch (page()) {
        case $vars['login_page']:
            require plugin_path('views/login.php');
            break;
        case $vars['signup_page']:
            require plugin_path('views/signup.php');
            break;
        case $vars['forgot_page']:
            require plugin_path('views/forgot.php');
            break;
    }
});

add_filter('after_query', function ($data) {
    error_log("Query executed: " . json_encode($data));
    return $data;
});