<?php

/**
 * Plugin Name: Basic Admin Plugin
 * Description: A Basic Admin plugin that manages the settings and site identifiers.
 * Version: 1.0
 * Author: Afro Bear
 */

set_value([
    'admin_plugin_route'  =>'admin',
    'logout_page'         =>'logout',
]);

add_filter('permissions', function($permissions){

    $permissions[] =  'view_admin_page';

    return $permissions;
});

add_action('controller', function(){
    do_action(plugin_id().'_controller');

});

add_action('view', function(){
    $vars = get_value();

    $section_title = ucfirst(str_replace("-"," ",(URL(1)??'')));

    if(empty($section_title)){
        $section_title = 'Dashboard';
    }

    $section_title = do_filter(plugin_id().'_before_section_title',$section_title);
    
    $dashboard_link = (object)[
        'title'       => 'Dashboard',
        'link'        => ROOT . '/' . $vars['admin_plugin_route'],
        'icon'        => 'dashboard',
        'parent'      => 0,
    ];

    $links[] = $dashboard_link;

    $links = do_filter(plugin_id().'_before_admin_links', $links);

	$home_link = (object)[
        'title'       => 'Home',
        'link'        => 'home',
        'link'        => ROOT ,
        'icon'        => 'house-fill',
        'parent'      => 0,
    ];
    $bottom_links[] = $home_link;
	
    $setting_link = (object)[
        'title'       => 'Settings',
        'link'        => 'settings',
        'link'        => ROOT . '/' . $vars['admin_plugin_route'].'/'.'settings',
        'icon'        => 'gear-wide-connected',
        'parent'      => 0,
    ];
    $bottom_links[] = $setting_link;

    $logout_link = (object)[
        'title'       => 'Logout',
        'link'        => ROOT . '/' . $vars['logout_page'],
        'icon'        => 'door-closed',
        'parent'      => 0,
    ];
    $bottom_links[] = $logout_link;

    require plugin_path('views/view.php');
});

add_action('basic-admin_main_content', function () {
    $vars = get_value();

    if (URL(1) !== '') {
        return;
    }

    $db = new \Core\Database;

    $countTable = function (string $table, string $where = '') use ($db): int {
        if (!$db->tableExists($table)) {
            return 0;
        }
        $row = $db->fetch("SELECT COUNT(*) as count FROM $table" . ($where ? " WHERE $where" : ''));
        return $row ? (int) $row->count : 0;
    };

    $stat_cards = [];

    if (user_can('view_pages')) {
        $stat_cards[] = ['label' => 'Pages', 'count' => $countTable('pages', 'date_deleted IS NULL'), 'icon' => 'file-earmark-text', 'color' => 'primary', 'link' => ROOT . '/' . $vars['admin_plugin_route'] . '/pages'];
    }
    if (user_can('view_posts')) {
        $stat_cards[] = ['label' => 'Posts', 'count' => $countTable('posts', 'date_deleted IS NULL'), 'icon' => 'postcard', 'color' => 'success', 'link' => ROOT . '/' . $vars['admin_plugin_route'] . '/post'];
    }
    if (user_can('view_users')) {
        $stat_cards[] = ['label' => 'Users', 'count' => $countTable('siteusers', 'deleted = 0'), 'icon' => 'people', 'color' => 'info', 'link' => ROOT . '/' . $vars['admin_plugin_route'] . '/users'];
    }
    if (user_can('view_menus')) {
        $stat_cards[] = ['label' => 'Menu Items', 'count' => $countTable('menus'), 'icon' => 'list', 'color' => 'warning', 'link' => ROOT . '/' . $vars['admin_plugin_route'] . '/site-menus'];
    }
    if (user_can('view_categories')) {
        $stat_cards[] = ['label' => 'Categories', 'count' => $countTable('categories', 'disabled = 0'), 'icon' => 'tags', 'color' => 'secondary', 'link' => ROOT . '/' . $vars['admin_plugin_route'] . '/categories'];
    }
    if (user_can('view_images')) {
        $stat_cards[] = ['label' => 'Images', 'count' => $countTable('images'), 'icon' => 'image', 'color' => 'dark', 'link' => ROOT . '/' . $vars['admin_plugin_route'] . '/images'];
    }

    $recent_activity = [];

    if (user_can('view_pages') && $db->tableExists('pages')) {
        foreach ($db->query("SELECT id, title, date_created FROM pages WHERE date_deleted IS NULL ORDER BY date_created DESC LIMIT 5") as $row) {
            $recent_activity[] = [
                'type'  => 'Page',
                'title' => $row->title,
                'date'  => $row->date_created,
                'link'  => ROOT . '/' . $vars['admin_plugin_route'] . '/pages/edit/' . $row->id,
            ];
        }
    }
    if (user_can('view_posts') && $db->tableExists('posts')) {
        foreach ($db->query("SELECT id, title, date_created FROM posts WHERE date_deleted IS NULL ORDER BY date_created DESC LIMIT 5") as $row) {
            $recent_activity[] = [
                'type'  => 'Post',
                'title' => $row->title,
                'date'  => $row->date_created,
                'link'  => ROOT . '/' . $vars['admin_plugin_route'] . '/post/edit/' . $row->id,
            ];
        }
    }

    usort($recent_activity, fn($a, $b) => strtotime($b['date'] ?? '') <=> strtotime($a['date'] ?? ''));
    $recent_activity = array_slice($recent_activity, 0, 8);

    $quick_actions = [];
    if (user_can('add_page')) {
        $quick_actions[] = ['label' => 'New Page', 'icon' => 'file-earmark-plus', 'link' => ROOT . '/' . $vars['admin_plugin_route'] . '/pages/add'];
    }
    if (user_can('add_post')) {
        $quick_actions[] = ['label' => 'New Post', 'icon' => 'postcard', 'link' => ROOT . '/' . $vars['admin_plugin_route'] . '/post/add'];
    }
    if (user_can('add_user')) {
        $quick_actions[] = ['label' => 'New User', 'icon' => 'person-plus', 'link' => ROOT . '/' . $vars['admin_plugin_route'] . '/users/add'];
    }
    if (user_can('add_menu')) {
        $quick_actions[] = ['label' => 'New Menu Item', 'icon' => 'list', 'link' => ROOT . '/' . $vars['admin_plugin_route'] . '/site-menus/add'];
    }

    require plugin_path('views/dashboard.php');
});
