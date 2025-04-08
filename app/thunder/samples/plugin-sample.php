<?php

// plugin.php

/**
 * Plugin Name: Sample Plugin
 * Description: A sample Thunder plugin.
 * Version: 1.0
 * Author: Your Name
 */

$priority = 10;

set_value([
    'plugin_route'  =>'my-plugin',
    'tables'         =>[
        'sample_table'       => 'sample',
    ],
]);

$db = new \Core\Database;
$table = get_value()['tables'];

if(!$db->tableExists($table)){
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",",$db->missing_tables));
    die;
}

add_filter('permissions', function($permissions){

    $permissions[] =  'my_permission';

    return $permissions;
});

add_action('controller', function(){
    $vars = get_value();

    require plugin_path('controllers/controller.php');
},$priority);

add_action('view', function(){
    $vars = get_value();

    require plugin_path('views/view.php');
});

add_filter('after_query', function($data){

    return $data;
});
