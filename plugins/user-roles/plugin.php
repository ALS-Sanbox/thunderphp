<?php

// plugin.php

/**
 * Plugin Name: Users Roles Plugin
 * Description: A admin roles plugin that adds the ability to add user roles and define groups of user permissions.
 * Version: 1.0
 * Author: Afro Lion
 */

set_value([
    'admin_route'   =>'admin',
    'plugin_route'  =>'roles',
    'table'         =>[
        'users_table'       => 'siteusers',
        'roles_table'       => 'user_roles',
        'permissions_table' => 'permission_roles',
        'roles_map_table'   => 'user_roles_map',
    ],
    'optional_tables'       =>[
        
    ],
]);

$db = new \Core\Database;
$table = get_value()['table'];

if(!$db->tableExists($table)){
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",",$db->missing_tables));
    die;
}

add_filter('permissions', function($permissions){

    $permissions[] =  'view_roles';
    $permissions[] =  'add_role';
    $permissions[] =  'edit_role';
    $permissions[] =  'edit_permissions';
    $permissions[] =  'delete_role';

    return $permissions;
});



add_filter('basic-admin_before_admin_links', function($links){
    if(user_can('view_roless')){

        $vars = get_value();
    
        $menu_link = (object)[
            'title'       => 'User Roles',
            'link'        => ROOT . '/' . $vars['admin_route'].'/'.$vars['plugin_route'],
            'icon'        => 'person-box',
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

    
    if(URL(1) == $vars['plugin_route']){
        $user_map = new \UserRoles\User_roles_map;
        $user_roles = new \UserRoles\User_role;
        $permissions = new \UserRoles\Role_permission;
        $ses = new \Core\Session;
        require plugin_path('controllers/list_controller.php');       
    }
});

add_action('basic-admin_main_content', function(){
    $ses = new \Core\Session;
	$vars = get_value();
	$admin_route = $vars['admin_route'];
	$plugin_route = $vars['plugin_route'];
	$errors = $vars['errors'] ?? [];
	$user_roles = new \UserRoles\User_role;
	$user_map = new \UserRoles\User_roles_map;
    $permissions = new \UserRoles\Role_permission;
    $allPermissions = array_unique(APP('permissions'));
    sort($allPermissions);
    $roles = $user_roles->findAll();

    $req = new \Core\Request;

    if (URL(1) == $vars['plugin_route']) {
        require plugin_path('views/list.php');
    }
});

add_filter('after_query',function($data)
{
	if(empty($data['result']))
		return $data;
    
	if ($data['query_id'] == 'get-roles') {
		$user_permission = new \UserRoles\Role_permission;
		foreach ($data['result'] as $key => $row) {
			$permissions = $user_permission->where(['role_id' => $row->id, 'disabled' => 0]);
			if($permissions)
				$data ['result'][$key]->permissions = array_column($permissions, 'permission');
		}
	}
	return $data;
});
