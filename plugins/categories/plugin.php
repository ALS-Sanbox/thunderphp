<?php

/**
 * Plugin Name: Categories Plugin
 * Description: A categories plugin that allows you to create, manage, and organize hierarchical categories for use across your application.
 * Version: 1.0
 * Author: Afro Lion
 */

set_value([
    'admin_route'   => 'admin',
    'plugin_route'  => 'categories',
    'table'         => [
        'categories_table' => 'categories',
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
    $permissions[] = 'view_categories';
    $permissions[] = 'add_category';
    $permissions[] = 'edit_category';
    $permissions[] = 'delete_category';
    return $permissions;
});

add_filter('basic-admin_before_admin_links', function ($links) {
    if (user_can('view_categories')) {
        $vars = get_value();

        $menu_link = (object)[
            'title'  => 'Categories',
            'link'   => ROOT . '/' . $vars['admin_route'] . '/' . $vars['plugin_route'],
            'icon'   => 'card',
            'parent' => 0,
        ];

        $links[] = $menu_link;
    }
    return $links;
});

add_action('controller', function () {
    $req = new \Core\Request;
    $vars = get_value();
    $cat = new Category\Categories;
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (URL(1) == $vars['plugin_route'] && $req->posted()) {
        $id = URL(3) ?? null;
        if ($id) {
            $cat::$query_id = 'get-categories';
            $row = $cat->find($id) ?: '';
        }

        switch (URL(2)) {
            case 'add':
                require plugin_path('controllers/add_controller.php');
                break;
            case 'edit':
                require plugin_path('controllers/edit_controller.php');
                break;
            case 'delete':
                require plugin_path('controllers/delete_controller.php');
                break;
        }
    }
});

add_action('basic-admin_main_content', function () {
    $ses = new \Core\Session;
    $vars = get_value();
    $cat = new Category\Categories;
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (URL(1) == $plugin_route) {
        $id = URL(3) ?? null;

        if ($id) {
            $row = $cat->find($id) ?: '';
        }

        switch (URL(2)) {
            case 'add':
                require plugin_path('views/admin/add.php');
                break;
            case 'edit':
                require plugin_path('views/admin/edit.php');
                break;
            case 'delete':
                require plugin_path('views/admin/delete.php');
                break;
            case 'view':
                require plugin_path('views/frontend/view.php');
                break;
            default:
                $limit = 30;
                $pager = new \Core\Pager($limit);
                $offset = $pager->offset;

                $cat->limit = $limit;
                $cat->offset = $offset;
                $cat::$query_id = 'get-categories';

                if (!empty($_GET['find'])) {
                    $find = '%' . trim($_GET['find']) . '%';
                    $query = "SELECT * FROM categories WHERE category LIKE :find LIMIT $limit OFFSET $offset";
                    $rows = $cat->query($query, ['find' => $find]);
                } else {
                    $rows = $cat->findAll();
                }

                require plugin_path('views/admin/list.php');
                break;
        }
    }
});

add_filter('after_query', function ($data) {
    if (empty($data['result'])) return $data;

    if ($data['query_id'] === 'get-categories') {
        usort($data['result'], fn($a, $b) => $a->id <=> $b->id);
    }

    return $data;
});
