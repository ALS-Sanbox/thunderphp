<?php

// plugin.php

/**
 * Plugin Name: Users Manager Plugin
 * Description: A admin manager plugin that allows creating, editing, deleting of users.
 * Version: 1.0
 * Author: Afro Lion
 */

set_value([
    'admin_route'   =>'admin',
    'plugin_route'  =>'users',
    'table'         =>[
        'users_table'       => 'siteusers',
    ],
    'optional_tables'       =>[
        'roles_table'       => 'users_roles',
        'permissions_table' => 'permissions_roles',
        'roles_map_table'   => 'users_roles_map',
    ],
]);

$db = new \Core\Database;
$table = get_value()['table'];

if(!$db->tableExists($table)){
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",",$db->missing_tables));
    die;
}

add_filter('permissions', function($permissions){

    $permissions[] =  'all';
    $permissions[] =  'view_users';
    $permissions[] =  'view_user_detail';
    $permissions[] =  'add_user';
    $permissions[] =  'edit_user';
    $permissions[] =  'delete_user';

    return $permissions;
});

add_filter('basic-admin_before_admin_links', function($links){
    if(user_can('view_users')){

        $vars = get_value();
    
        $menu_link = (object)[
            'title'       => 'Users',
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
    
    if(URL(1) == $vars['plugin_route'] && $req->posted()){
        $ses = new \Core\Session;
        $user = new \UserManager\Siteusers;
        $user_roles = new \UserManager\User_role;
        $user_roles_map = new \UserManager\User_roles_map;
        
        $id = URL(3) ?? null;
        if($id){
            $user::$query_id = 'get-users';
            $row = $user->find(URL(3)) ?: '';
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
            default:
                break;
        }

        
    }
});

add_action('basic-admin_main_content', function(){
    $ses = new \Core\Session;
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];
    $siteusers = new \UserManager\Siteusers;
    $user_roles = new \UserManager\User_role;
    $user_roles_map = new \UserManager\User_roles_map;

    if (URL(1) == $vars['plugin_route']) {
        
        $id = URL(3) ?? null;
        if($id){
            $row = $siteusers->find(URL(3)) ?: '';
        }

        switch (URL(2)) {
            case 'add':
                require plugin_path('views/add.php');
                break;
            case 'edit':
                require plugin_path('views/edit.php');
                break;
            case 'delete':
                require plugin_path('views/delete.php');
                break;
            case 'view':
                require plugin_path('views/view.php');
                break;
            default:
                $limit = 30;
                $pager = new \core\Pager($limit);
                $offset = $pager->offset;

                $siteusers->limit = $limit;
                $siteusers->offset = $offset;
                $siteusers::$query_id = 'get-users';

                if (!empty($_GET['find'])) {
                    $find = '%' . trim($_GET['find']) . '%';
                    $query = "SELECT * FROM users WHERE (first_name LIKE :find || last_name LIKE :find) LIMIT $limit OFFSET $offset";
                    $rows = $siteusers->query($query, ['find' => $find]);
                }else {
                    $rows = $siteusers->findAll();
                }
                require plugin_path('views/list.php');
                break;
        }
    }
});

add_filter('user_permisions', function($permissions){
    $ses = new \Core\Session;

    if($ses->is_logged_in()){
        $vars = get_value();
        $db = new \Core\Database;

        $query = "select * from " . $vars['optional_tables']['roles_table'];
        $roles = $db->query($query);

        if(is_array($roles)){

        }else{
            $permissions[] = 'all';
        }
    }

    return $permissions;
});

add_filter('after_query',function($data)
{
	if(empty($data['result']))
		return $data;

        if($data['query_id'] == 'get-users')
        {
            $role_map = new \UserManager\User_roles_map;
            foreach ($data['result'] as $key => $row) {
                $query = "SELECT * FROM user_roles WHERE disabled = 0 AND  id IN (SELECT role_id FROM user_roles_map WHERE disabled = 0 AND user_id = :user_id)";
                $roles = $role_map->query($query, ['user_id' => $row->id]);
            
                if ($roles)
                    $data['result'][$key]->roles = array_column($roles, 'role');
            
                $user_roles_map = new \UserManager\User_roles_map;
                $role_ids = $user_roles_map->where(['user_id' => $row->id]);
            
                if ($role_ids) {
                    $data['result'][$key]->role_ids = array_column($role_ids, 'role_id');
                }
            }

            usort($data['result'], function($a, $b) {
                return $a->id <=> $b->id;
            });
        }

	return $data;
});