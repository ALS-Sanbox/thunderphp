<?php

/**
 * Plugin Name: Images Plugin
 * Description: A media library for uploading, browsing, and deleting images, with ready-to-copy links for use in Summernote or GrapesJS.
 * Version: 1.0
 * Author: Afro Lion
 */

set_value([
    'admin_route'  => 'admin',
    'plugin_route' => 'images',
    'table'        => [
        'images_table' => 'images',
    ],
    'optional_tables' => [],
]);

$db = new \Core\Database;
$table = get_value()['table'];

if (!$db->tableExists($table)) {
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",", $db->missing_tables));
    die;
}

add_filter('permissions', function ($permissions) {
    $permissions[] = 'view_images';
    $permissions[] = 'add_image';
    $permissions[] = 'delete_image';
    return $permissions;
});

add_filter('basic-admin_before_admin_links', function ($links) {
    if (user_can('view_images')) {
        $vars = get_value();

        $links[] = (object)[
            'title'  => 'Images',
            'link'   => ROOT . '/' . $vars['admin_route'] . '/' . $vars['plugin_route'],
            'icon'   => 'card',
            'parent' => 0,
        ];
    }
    return $links;
});

add_action('controller', function () {
    $req = new \Core\Request;
    $vars = get_value();
    $images = new \Images\Images;
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];
    $user_id = (new \Core\Session)->user('id');

    if (URL(1) === $vars['plugin_route'] && $req->posted()) {
        switch (URL(2)) {
            case 'add':
                require plugin_path('controllers/add_controller.php');
                break;
            case 'delete':
                $id = URL(3) ?? null;
                require plugin_path('controllers/delete_controller.php');
                break;
        }
    }
});

add_action('basic-admin_main_content', function () {
    $vars = get_value();
    $images = new \Images\Images;
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (page() !== $admin_route || URL(1) !== $plugin_route) return;

    $limit = 24;
    $total_count = $images->totalCount();

    $pager = new \Core\Pager($limit, $total_count);
    $offset = $pager->offset;

    $images->limit  = $limit;
    $images->offset = $offset;

    $rows = $images->findAll();

    require plugin_path('views/admin/list.php');
});
