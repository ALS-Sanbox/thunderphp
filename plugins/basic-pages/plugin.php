<?php

// plugin.php

/**
 * Plugin Name: Basic Pages Plugin
 * Description: A basic pages Thunder plugin.
 * Version: 1.0
 * Author: H Ford
 */

$priority = 10;

set_value([
    'admin_route'   =>'admin',
    'plugin_route'  =>'pages',
    'tables'        =>[
        'pages_table'       => 'pages',
    ],
]);

$db = new \Core\Database;
$table = get_value()['tables'];

if(!$db->tableExists($table)){
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",",$db->missing_tables));
    die;
}

add_filter('permissions', function($permissions){

    $permissions[] =  'view_pages';
    $permissions[] =  'add_pages';
    $permissions[] =  'edit_page';
    $permissions[] =  'delete_page';

    return $permissions;
});

add_action('controller', function(){
    $ses = new \Core\Session;
    $req = new \Core\Request;
    $vars = get_value();
    $page = new \BasicPages\Pages;
    $content = new \BasicPages\Content;
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];
    $user_id = $ses->user('id');
      
    if(URL(1) == $plugin_route && $req->posted()){
        $id = URL(3) ?? null;
        if($id){
            $page::$query_id = 'get-users';
            $row = $page->find(URL(3)) ?: '';
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

/** Displays the view fiel */
add_action('view', function(){
    $vars = get_value();
    $page = new \BasicPages\Pages;
    $row = $page->first(['slug'=>page()]);

    if($row){
        require plugin_path('views/frontend/view.php');
    }
});

add_action('basic-admin_main_content', function(){
    $ses = new \Core\Session;
    $vars = get_value();
    $pages = new \BasicPages\Pages;
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];
    $user_id = $ses->user('id');

    if (page() == $admin_route && URL(1) == $plugin_route) {

        $id = URL(3) ?? null;
        if($id){
            $pages::$query_id = 'get-users';
            $row = $pages->find(URL(3)) ?: '';
        }

        switch (URL(2)) {
            case 'add':
                $all_items = $pages->query("select * from pages");
                require plugin_path('views/admin/add.php');
                break;
            case 'edit':
                $all_items = $pages->query("select * from pages");
                require plugin_path('views/admin/edit.php');
                break;
            case 'delete':
                require plugin_path('views/admin/delete.php');
                break;
            default:
                $limit = 10;
				$pager = new \core\Pager($limit);
				$offset = $pager->offset;

				$pages->limit = $limit;
				$pages->offset = $offset;
				$pages->order = 'asc';
				$pages::$query_id = 'get-pages';

				if (!empty($_GET['find'])) {
					$find = '%' . trim($_GET['find']) . '%';
					$query = "SELECT * FROM pages WHERE (title like :find) ORDER BY list_order ASC LIMIT $limit OFFSET $offset";
					$rows = $pages->query($query, ['find' => $find]);
				} else {
					$rows = $pages->findAll();
				}

				require plugin_path('views/admin/list.php');
				break;
        }
    }
});

add_filter('basic-admin_before_admin_links', function($links){
    if(user_can('view_pages')){

        $vars = get_value();
    
        $page_link = (object)[
            'title'       => 'Pages',
            'link'        => ROOT . '/' . $vars['admin_route']. '/' .$vars['plugin_route'],
            'icon'        => 'page',
            'parent'      => 0,
        ];
    
        $links[] = $page_link;
    }
        return $links;
});

add_filter('after_query',function($data)
{
	if(empty($data['result']))
		return $data;

        if(false && $data['query_id'] == 'get-pages')
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
        }

	return $data;
});
