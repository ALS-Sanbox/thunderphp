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

add_action('before_controller', function(){
    $vars = get_value();

    if(false && page() == $vars['admin_plugin_route'] && !user_can('view_admin_page')){
        message('Please Login as Admin!');
        redirect('login');
    }

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
        'icon'        => 'house-fill',
        'parent'      => 0,
    ];

    $links[] = $dashboard_link;

    $links = do_filter(plugin_id().'_before_admin_links', $links);

    $setting_link = (object)[
        'title'       => 'Setting',
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
