<?php 

/**
 * Plugin Name: Basic Post Plugin
 * Description: A basic post Thunder plugin.
 * Version: 1.0
 * Author: H Ford
 */

$priority = 10;

set_value([
    'admin_route'  => 'admin',
    'plugin_route' => 'post',
    'tables'       => ['post_table' => 'posts'],
]);

$db    = new \Core\Database;
$table = get_value()['tables'];

if (!$db->tableExists($table)) {
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",", $db->missing_tables));
    die;
}

// Register permissions
add_filter('permissions', function ($permissions) {
    return array_merge($permissions, [
        'view_posts',
        'add_post',
        'edit_post',
        'delete_post',
    ]);
});

// Handle POST actions in controller
add_action('controller', function () {
    $ses = new \Core\Session;
    $req = new \Core\Request;
    $vars = get_value();
    $posts = new \BasicPosts\Posts;
	$admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];
    $user_id = $ses->user('id');

    if (URL(1) === $vars['plugin_route'] && $req->posted()) {
        $id = URL(3) ?? null;
        $row = $id ? $posts->find($id) : null;

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
        }
    }
});

// Handle post view on frontend
add_action('view', function () {
    $posts = new \BasicPosts\Posts;
    $matchedPosts = [];
    $categoryNames = [];

    $row = $posts->first(['slug' => page()]);
    if (!$row) return;

    $categoryIds = json_decode($row->categories ?? '[]', true);

    if (is_array($categoryIds) && !empty($categoryIds)) {
        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
        $query = "SELECT category FROM categories WHERE id IN ($placeholders)";
        $cats = $posts->query($query, $categoryIds);
        $categoryNames = array_column($cats, 'category');
    }

    $pageCategories = $categoryIds;
	$posts->limit  = 3000;
	$allPosts = $posts->findAll();

	// Filter out posts with title 'blog-post'
	$allPosts = array_filter($allPosts, function($post) {
		return strtolower(trim($post->porp)) !== '0';
	});

	foreach ($allPosts as $post) {
		$postCategories = json_decode($post->categories, true) ?? [];
		if (array_intersect($pageCategories, $postCategories)) {
			$matchedPosts[] = $post;
		}
	}

    $latest = $posts->query("SELECT * FROM posts WHERE date_deleted IS NULL AND disabled = 0 ORDER BY date_created DESC LIMIT 3");
	$results = $posts->query("SELECT DISTINCT slug FROM categories");
	$categories = [];

	foreach ($results as $cat) {
		$categories[] = $cat->slug;
	}

	if (page() === 'blog-post' || in_array(page(), $categories)) {
		require plugin_path('views/frontend/blog.php');
	} else {
		require plugin_path('views/frontend/post.php');
	}
});


// Admin content routes
add_action('basic-admin_main_content', function () {
    $ses = new \Core\Session;
    $vars = get_value();
    $posts = new \BasicPosts\Posts;
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];
    $user_id = $ses->user('id');
    
    if (page() !== $admin_route || URL(1) !== $plugin_route) return;

    $id = URL(3) ?? null;
    $row = $id ? $posts->find($id) : null;

    switch (URL(2)) {
        case 'add':
        case 'edit':
            $all_items = $posts->query("SELECT * FROM posts");
            require plugin_path("views/admin/" . URL(2) . ".php");
            break;
        case 'delete':
            require plugin_path('views/admin/delete.php');
            break;
        default:
            $limit = 10;
            $pager = new \core\Pager($limit);
            $offset = $pager->offset;

            $posts->limit  = $limit;
            $posts->offset = $offset;
            $posts->order  = 'asc';
            $posts::$query_id = 'get-posts';

            if (!empty($_GET['find'])) {
                $find = '%' . trim($_GET['find']) . '%';
                $query = "SELECT * FROM posts WHERE title LIKE :find ORDER BY list_order ASC LIMIT $limit OFFSET $offset";
                $rows = $posts->query($query, ['find' => $find]);
            } else {
                $rows = $posts->findAll();
            }

            require plugin_path('views/admin/list.php');
            break;
    }
});

// Admin sidebar link
add_filter('basic-admin_before_admin_links', function ($links) {
    if (user_can('view_post')) {
        $vars = get_value();
        $links[] = (object)[
            'title'  => 'Posts',
            'link'   => ROOT . '/' . $vars['admin_route'] . '/' . $vars['plugin_route'],
            'icon'   => 'postcard',
            'parent' => 0,
        ];
    }
    return $links;
});

// Optional post-processing hook (currently inactive)
add_filter('after_query', function ($data) {
    if (empty($data['result'])) return $data;

    // Example post-processing (currently disabled)
    if (false && $data['query_id'] == 'get-posts') {
        foreach ($data['result'] as $key => $row) {
            $role_map = new \UserManager\User_roles_map;

            $query = "SELECT * FROM user_roles WHERE disabled = 0 AND id IN (
                SELECT role_id FROM user_roles_map WHERE disabled = 0 AND user_id = :user_id
            )";
            $roles = $role_map->query($query, ['user_id' => $row->id]);

            if ($roles) {
                $data['result'][$key]->roles = array_column($roles, 'role');
            }

            $role_ids = $role_map->where(['user_id' => $row->id]);
            if ($role_ids) {
                $data['result'][$key]->role_ids = array_column($role_ids, 'role_id');
            }
        }

        usort($data['result'], fn($a, $b) => $a->id <=> $b->id);
    }

    return $data;
});
