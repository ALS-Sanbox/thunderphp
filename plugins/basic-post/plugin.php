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
        'manage_post_layout',
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
            case 'layout':
                require plugin_path('controllers/layout_controller.php');
                break;
            case 'duplicate':
                require plugin_path('controllers/duplicate_controller.php');
                break;
            case 'bulk':
                require plugin_path('controllers/bulk_controller.php');
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

    $posts->incrementViews($row->id);

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
		return strtolower(trim((string) $post->pop)) !== '0';
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
		$layout = setting('post_default_layout');
		$layoutTokens = ['{{POST_TITLE}}', '{{POST_CONTENT}}', '{{POST_LIST}}'];
		$hasLayoutToken = is_array($layout) && !empty($layout['html']) && array_reduce(
			$layoutTokens,
			fn($found, $token) => $found || strpos($layout['html'], $token) !== false,
			false
		);

		if ($hasLayoutToken) {
			$otherPosts = $posts->query(
				"SELECT id, title, slug FROM posts WHERE disabled = 0 AND date_deleted IS NULL AND pop != 0 AND id != ? ORDER BY date_created DESC LIMIT 10",
				[$row->id]
			);

			$postListMarkup = '<ul class="post-list">';
			if (!empty($otherPosts)) {
				foreach ($otherPosts as $otherPost) {
					$postListMarkup .= '<li><a href="' . ROOT . '/' . esc($otherPost->slug) . '">' . esc($otherPost->title) . '</a></li>';
				}
			} else {
				$postListMarkup .= '<li>No other posts yet.</li>';
			}
			$postListMarkup .= '</ul>';

			$merged = str_replace(
				$layoutTokens,
				[esc($row->title), $row->content, $postListMarkup],
				$layout['html']
			);

			echo '<style>' . ($layout['css'] ?? '') . '</style>';
			echo $merged;
		} else {
			require plugin_path('views/frontend/post.php');
		}
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
        case 'layout':
            require plugin_path('views/admin/layout.php');
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
                $total_row = $posts->fetch("SELECT COUNT(*) as count FROM posts $whereSql", $params);
                $total_count = $total_row ? (int) $total_row->count : 0;
            } else {
                $total_count = $posts->totalCount();
            }

            $pager = new \core\Pager($limit, $total_count);
            $offset = $pager->offset;

            $posts->limit  = $limit;
            $posts->offset = $offset;
            $posts->order  = 'asc';
            $posts::$query_id = 'get-posts';

            if ($where) {
                $query = "SELECT * FROM posts $whereSql ORDER BY id ASC LIMIT $limit OFFSET $offset";
                $rows = $posts->query($query, $params);
            } else {
                $rows = $posts->findAll();
            }

            require plugin_path('views/admin/list.php');
            break;
    }
});

// Admin sidebar link
add_filter('basic-admin_before_admin_links', function ($links) {
    if (user_can('view_posts')) {
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
