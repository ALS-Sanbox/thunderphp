<?php

// plugin.php

/**
 * Plugin Name: Basic Page Plugin
 * Description: A page manager plugin that allows creating, editing, deleting of pages .
 * Version: 1.0
 * Author: Afro Lion
 */

set_value([
    'admin_route'   =>'admin',
    'plugin_route'  =>'pages',
    'tables'         =>[
        'pages_table'       => 'pages',
    ],
    'optional_tables'       =>[

    ],
]);

$db = new \Core\Database;
$table = get_value()['tables'];

if(!$db->tableExists($table)){
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",",$db->missing_tables));
    die;
}

add_filter('permissions', function($permissions){

    $permissions[] =  'all';
    $permissions[] =  'view_pages';
    $permissions[] =  'add_page';
    $permissions[] =  'edit_page';
    $permissions[] =  'delete_page';

    return $permissions;
});

add_filter('basic-admin_before_admin_links', function($links){
    if(user_can('view_pages')){

        $vars = get_value();
    
        $menu_link = (object)[
            'title'       => 'Pages',
            'link'        => ROOT . '/' . $vars['admin_route'].'/'.$vars['plugin_route'],
            'icon'        => 'people',
            'parent'      => 0,
        ];
    
        $links[] = $menu_link;
    }
        return $links;
});

add_action('controller', function(){
    $req = new \Core\Request;
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];
    
    if(page() == $admin_route && URL(1) == $plugin_route){
        $ses = new \Core\Session;

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
            default:
                break;
        }
    }
});

add_action('basic-admin_main_content', function(){
    $vars = get_value();

    if(page() == $vars['admin_route'] && URL(1) == $vars['plugin_route']){
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
                require plugin_path('views/admin/view.php');
                break;
            default:

                break;
        }
    }
});

add_action('view', function(){
    $vars = get_value();

    require plugin_path('views/frontend/view.php');
});

add_filter('after_query', function($data)){
    if(empty($data['result']))
        return $data;
    
    foreach ($data['result'] as $key => $row) {
        # code...
    }

    return $data;
}