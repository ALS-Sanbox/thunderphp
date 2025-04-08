<?php

// plugin.php

/**
 * Plugin Name: Site Menus Plugin
 * Description: A menu plugin that allows different menus for pages.
 * Version: 1.0
 * Author: Afro Lion
 */

set_value([
    'admin_route'   =>'admin',
    'plugin_route'  =>'site-menus',
    'table'         =>[
        'menu_table'       => 'menus',
    ],
]);

$db = new \Core\Database;
$table = get_value()['table'];

if(!$db->tableExists($table)){
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",",$db->missing_tables));
    die;
}

add_filter('permissions', function($permissions){

    $permissions[] =  'view_menus';
    $permissions[] =  'add_menu';
    $permissions[] =  'edit_menu';
    $permissions[] =  'delete_menu';

    return $permissions;
});

add_action('header-footer_main_menu', function($data){
    require plugin_path('views/frontend/menu.php');
});

add_filter('basic-admin_before_section_title', function($title){
    $vars = get_value();

    if($vars['plugin_route'] == URL(1)){
        $page_number = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $page_number = $page_number < 1 ? 1 : $page_number;

        $title = $title . ' (page ' .$page_number .')';
    }

    return $title;
});

add_filter('basic-admin_before_admin_links', function($links){
    if(user_can('view_menus')){

        $vars = get_value();
    
        $menu_link = (object)[
            'title'       => 'Menus',
            'link'        => ROOT . '/' . $vars['admin_route'].'/'.$vars['plugin_route'],
            'icon'        => 'menu',
            'parent'      => 0,
        ];
    
        $links[] = $menu_link;
    }
        return $links;
});

add_filter('header-footer_before_menu_links', function ($links) {
    $vars = get_value();
    $menu = new \siteMenus\Menu;
    $menu::$query_id = 'get-menus-with-children';
    $rows = $menu->where(['disabled'=>0, 'parent'=>0]);

    $links = array_merge($links,$rows);

    return $links;
});

add_action('controller', function(){
    $req = new \Core\Request;
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];
    
    if(URL(1) == $vars['plugin_route'] && $req->posted()){
        $ses = new \Core\Session;
        $menus = new \siteMenus\Menu;
        
        $id = URL(3) ?? null;

        if($id){
            $row = $menus->find(URL(3)) ?: '';
        }

        switch (URL(2)) {
            case 'add':
                require plugin_path('controllers/add_controller.php');
                break;
            case 'edit':
                dd('here');
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
    $menu = new \siteMenus\Menu;

    
    $id = URL(3) ?? null;
    if($id){
        $row = $menu->find(URL(3)) ?: '';
    }


    if (URL(1) == $plugin_route) {
        switch (URL(2)) {
            case 'add':
                $all_items = $menu->query("select * from menus");
                require plugin_path('views/add.php');
                break;
            case 'edit':
                $all_items = $menu->query("select * from menus");
                require plugin_path('views/edit.php');
                break;
            case 'delete':
                require plugin_path('views/delete.php');
                break;
            default:
                $limit = 1;
                $pager = new \core\Pager($limit);
                $offset = $pager->offset;
                $menu::$query_id = 'get-menus';

                if (!empty($_GET['find'])) {
                    $find = '%' . trim($_GET['find']) . '%';
                    $query = "SELECT * FROM menus WHERE (title like :find LIMIT $limit OFFSET $offset";
                    $rows = $menu->query($query, ['find' => $find]);
                    dd($rows);
                }else {
                    $rows = $menu->findAll();
                }

                require plugin_path('views/list.php');
                break;
        }
    }
});

add_filter('menu_permisions', function($permissions){


    return $permissions;
});

add_filter('after_query',function($data)
{
	if(empty($data['result']))
		return $data;

        if(false && $data['query_id'] == 'get-menus')
        {
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
        }else
        if($data['query_id'] == 'get-menus-with-children')
        {
            $menu = new \siteMenus\Menu;
            foreach ($data['result'] as $key => $row) {
                $children = $menu->where(['parent' => $row->id, 'disabled'=>0]);
            
                if ($children) {
                    $data['result'][$key]->children = $children;
                    foreach ($children as $ikey => $irow) {
                        $grandchildren = $menu->where(['parent' => $irow->id, 'disabled'=>0]);
                        if ($grandchildren) {
                            $data['result'][$key]->children[$ikey]->grandchildren = $grandchildren;
                        }
                    }
                }
            }

            usort($data['result'], function($a, $b) {
                return $a->id <=> $b->id;
            });
        }
	return $data;
});