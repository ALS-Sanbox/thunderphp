<?php

/**
 * Plugin Name: Settings Plugin
 * Description: A settings plugin that allows you to create, manage, and organize hierarchical settings for use across your application.
 * Version: 1.0
 * Author: Afro Lion
 */

set_value([
    'admin_route'   => 'admin',
    'plugin_route'  => 'settings',
    'table'         => [
        'settings_table' => 'settings',
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
    $permissions[] = 'edit_settings';
    return $permissions;
});

add_action('controller', function () {
    $req = new \Core\Request;
    $vars = get_value();
    $set = new Setting\Settings;
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (URL(1) == $plugin_route && $req->posted()) {
		require plugin_path('controllers/edit_controller.php');
    }
});

add_action('basic-admin_main_content', function () {
    $ses = new \Core\Session;
    $vars = get_value();
    $set = new Setting\Settings;
	$pages = $set->query("SELECT title, slug FROM pages WHERE disabled = 0");
	$defaultPage = new stdClass();
	$defaultPage->title = 'Home';
	$defaultPage->slug = 'home';
	array_unshift($pages, $defaultPage);
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (URL(1) == $plugin_route) {
        require plugin_path('views/frontend/view.php');
    }
});

add_filter('after_query', function ($data) {
    if (empty($data['result'])) return $data;

    if ($data['query_id'] === 'get-settings') {
        usort($data['result'], fn($a, $b) => $a->id <=> $b->id);
    }

    return $data;
});
