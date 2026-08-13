<?php

/**
 * Plugin Name: Redirects Plugin
 * Description: Manage 301/302 redirects for old URLs, and log which unmatched URLs actually get hit so you know what to redirect next.
 * Version: 1.0
 * Author: Afro Lion
 */

set_value([
    'admin_route'  => 'admin',
    'plugin_route' => 'redirects',
    'table'        => [
        'redirects_table'      => 'redirects',
        'not_found_log_table'  => 'not_found_log',
    ],
]);

$db = new \Core\Database;
$table = get_value()['table'];

if (!$db->tableExists($table)) {
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",", $db->missing_tables));
    die;
}

add_filter('permissions', function ($permissions) {
    $permissions[] = 'view_redirects';
    $permissions[] = 'add_redirect';
    $permissions[] = 'edit_redirect';
    $permissions[] = 'delete_redirect';
    return $permissions;
});

add_filter('basic-admin_before_admin_links', function ($links) {
    if (user_can('view_redirects')) {
        $vars = get_value();

        $links[] = (object) [
            'title'  => 'Redirects',
            'link'   => ROOT . '/' . $vars['admin_route'] . '/' . $vars['plugin_route'],
            'icon'   => 'signpost-split',
            'parent' => 0,
        ];
    }
    return $links;
});

// Hooked on 'before_view' at a very low priority so it runs before anything
// else in the request - including header-footer's own before_view handler,
// which is what actually prints <!DOCTYPE html>...<head>. Catching the
// redirect here (rather than on 'view') means a match sends a clean
// Location header with nothing already buffered, instead of a redirect
// response with a stray half-rendered <head> in its body.
add_action('before_view', function () {
    if (page() === 'admin') return;

    $path = implode('/', array_filter(URL()));
    if ($path === '') return;

    $redirects = new \Redirects\Redirects;
    $match = $redirects->findByPath($path);

    if ($match) {
        $redirects->incrementHits($match->id);
        header('Location: ' . (str_starts_with($match->to_path, 'http') ? $match->to_path : ROOT . '/' . ltrim($match->to_path, '/')), true, (int) $match->redirect_type);
        die;
    }
}, -10);

add_action('404_not_found', function ($data) {
    $url = implode('/', array_filter($data['url'] ?? []));
    if ($url === '') return;

    (new \Redirects\NotFoundLog)->logHit($url);
});

add_action('controller', function () {
    $req = new \Core\Request;
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (!$req->posted() || page() !== $admin_route || URL(1) !== $plugin_route) return;

    $id = URL(3) ?? null;

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
        case 'clear-log':
            require plugin_path('controllers/clear_log_controller.php');
            break;
    }
});

add_action('basic-admin_main_content', function () {
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (page() !== $admin_route || URL(1) !== $plugin_route) return;

    $redirects = new \Redirects\Redirects;

    $id = URL(3) ?? null;
    if ($id) {
        $row = $redirects->find($id) ?: null;
    }

    switch (URL(2)) {
        case 'add':
            require plugin_path('views/admin/add.php');
            break;
        case 'edit':
            require plugin_path('views/admin/edit.php');
            break;
        default:
            $limit = 25;
            $total_count = $redirects->totalCount();

            $pager = new \Core\Pager($limit, $total_count);
            $offset = $pager->offset;

            $redirects->limit = $limit;
            $redirects->offset = $offset;
            $redirects->order_column = 'id';
            $rows = $redirects->findAll();

            $notFoundLog = new \Redirects\NotFoundLog;
            $notFoundLog->limit = 15;
            $notFoundLog->order_column = 'last_seen';
            $recentNotFound = $notFoundLog->findAll();

            require plugin_path('views/admin/list.php');
            break;
    }
});
