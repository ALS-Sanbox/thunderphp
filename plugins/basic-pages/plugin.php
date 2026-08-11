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
    $permissions[] =  'add_page';
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
            case 'duplicate':
                require plugin_path('controllers/duplicate_controller.php');
                break;
            case 'bulk':
                require plugin_path('controllers/bulk_controller.php');
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
        $page->incrementViews($row->id);
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
				$search = trim($_GET['search'] ?? $_GET['find'] ?? '');
				$status = $_GET['status'] ?? '';

				$where = [];
				$params = [];

				if ($search !== '') {
					$where[] = '(title LIKE :find1 OR description LIKE :find2)';
					$params['find1'] = '%' . $search . '%';
					$params['find2'] = '%' . $search . '%';
				}

				if ($status === 'active') {
					$where[] = 'disabled = 0';
				} elseif ($status === 'inactive') {
					$where[] = 'disabled = 1';
				}

				$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

				if ($where) {
					$total_row = $pages->fetch("SELECT COUNT(*) as count FROM pages $whereSql", $params);
					$total_count = $total_row ? (int) $total_row->count : 0;
				} else {
					$total_count = $pages->totalCount();
				}

				$pager = new \core\Pager($limit, $total_count);
				$offset = $pager->offset;

				$pages->limit = $limit;
				$pages->offset = $offset;
				$pages->order = 'asc';
				$pages::$query_id = 'get-pages';

				if ($where) {
					$query = "SELECT * FROM pages $whereSql ORDER BY id ASC LIMIT $limit OFFSET $offset";
					$rows = $pages->query($query, $params);
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

