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
    'reset_page'         => 'reset',
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
            case $vars['reset_page']:
                require plugin_path('controllers/reset_controller.php');
                break;
        }
    } elseif (page() === $vars['logout_page'] && $ses->is_logged_in()) {
        require plugin_path('controllers/logout_controller.php');
    }
});

// Renders the account area (Login/Signup, or the logged-in user's avatar +
// Admin/Profile/Logout) as its own piece, separate from the site's real
// navigation menu, so the header-footer editor can place it independently
// via the {{USER_MENU}} block instead of it always being glued to the menu.
add_action('header-footer_user_menu', function () {
    $ses = new \Core\Session();
    $vars = get_value();

    if (!$ses->is_logged_in()) {
        ?>
        <div class="hf-user-menu hf-user-menu-guest" style="display:flex;gap:12px;align-items:center;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
            <a href="<?= ROOT ?>/<?= $vars['login_page'] ?>">Login</a>
            <a href="<?= ROOT ?>/<?= $vars['signup_page'] ?>">Signup</a>
        </div>
        <?php
        return;
    }
    ?>
    <style>
        .hf-user-menu-account { position: relative; display: inline-block; font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; }
        .hf-user-menu-trigger { display: flex; gap: 8px; align-items: center; cursor: pointer; }
        /* padding-top (not margin) bridges the gap to the trigger so hover
           never drops out while the mouse travels down into the panel. */
        .hf-user-menu-links { position: absolute; top: 100%; right: 0; padding-top: 6px; display: none; z-index: 50; }
        .hf-user-menu-account:hover .hf-user-menu-links,
        .hf-user-menu-account:focus-within .hf-user-menu-links { display: block; }
        .hf-user-menu-panel { background: #fff; border: 1px solid #ddd; border-radius: 6px; box-shadow: 0 6px 16px rgba(0,0,0,.12); min-width: 150px; display: flex; flex-direction: column; padding: 6px 0; }
        .hf-user-menu-panel a { display: block; padding: 8px 14px; color: #1a1a1a !important; text-decoration: none !important; white-space: nowrap; font-weight: 500; }
        .hf-user-menu-panel a:hover { background: #f1f3f5; }
    </style>
    <div class="hf-user-menu hf-user-menu-account">
        <div class="hf-user-menu-trigger" tabindex="0">
            <img src="<?= esc(get_image($ses->user('image'))) ?>" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
            <span>Hi, <?= esc($ses->user('first_name')) ?></span>
        </div>
        <div class="hf-user-menu-links">
            <div class="hf-user-menu-panel">
                <a href="<?= ROOT ?>/<?= $vars['admin_plugin_route'] ?>">Admin</a>
                <a href="<?= ROOT ?>/profile/<?= $ses->user('id') ?>">Profile</a>
                <a href="<?= ROOT ?>/<?= $vars['logout_page'] ?>?_token=<?= csrf() ?>">Logout</a>
            </div>
        </div>
    </div>
    <?php
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
        case $vars['reset_page']:
            $req = new \Core\Request();
            $resets = new \PasswordResets\PasswordResets();
            $token = (string) $req->get('token');
            $tokenRow = $token !== '' ? $resets->validateToken($token) : false;
            require plugin_path('views/reset.php');
            break;
    }
});
