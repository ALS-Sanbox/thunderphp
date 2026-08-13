<?php

/**
 * Plugin Name: Activity Log Plugin
 * Description: A lightweight, read-only audit trail of who added/edited/deleted content, recorded automatically from every write query against a fixed list of content tables.
 * Version: 1.0
 * Author: Afro Lion
 */

set_value([
    'admin_route'  => 'admin',
    'plugin_route' => 'activity-log',
    'table'        => ['activity_log_table' => 'activity_log'],
]);

$db = new \Core\Database;
$table = get_value()['table'];

if (!$db->tableExists($table)) {
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",", $db->missing_tables));
    die;
}

// Deliberately an allowlist, not a denylist: only these tables are ever
// written to activity_log. This both keeps the log focused on real content
// changes (not every session/settings/cache write) and guarantees the
// filter below can never recurse into logging its own inserts.
const ACTIVITY_LOG_TRACKED_TABLES = [
    'pages', 'posts', 'categories', 'menus', 'siteusers',
    'user_role', 'role_permission', 'redirects', 'contact_submissions', 'images',
];

add_filter('permissions', function ($permissions) {
    $permissions[] = 'view_activity_log';
    $permissions[] = 'clear_activity_log';
    return $permissions;
});

add_filter('basic-admin_before_admin_links', function ($links) {
    if (user_can('view_activity_log')) {
        $vars = get_value();

        $links[] = (object) [
            'title'  => 'Activity Log',
            'link'   => ROOT . '/' . $vars['admin_route'] . '/' . $vars['plugin_route'],
            'icon'   => 'clock-history',
            'parent' => 0,
        ];
    }
    return $links;
});

add_filter('after_query', function ($data) {
    if (empty($data['query']) || !is_object($data['query']) || empty($data['query']->queryString)) {
        return $data;
    }

    $sql = $data['query']->queryString;

    // Pageview counters (Pages::incrementViews()/Posts::incrementViews())
    // update the same tracked tables on every single visitor page load -
    // without this exclusion the log would be dominated by view-count
    // noise instead of actual content changes.
    if (preg_match('/^\s*UPDATE\s+`?\w+`?\s+SET\s+views\s*=\s*views\s*\+\s*1\s+WHERE/i', $sql)) {
        return $data;
    }

    if (!preg_match('/^\s*(INSERT INTO|UPDATE|DELETE FROM)\s+`?(\w+)`?\b/i', $sql, $m)) {
        return $data;
    }

    $verb = strtoupper(explode(' ', trim($m[1]))[0]);
    $tableName = $m[2];

    if (!in_array($tableName, ACTIVITY_LOG_TRACKED_TABLES, true)) {
        return $data;
    }

    $ses = new \Core\Session;
    $username = trim((string) $ses->user('first_name') . ' ' . (string) $ses->user('last_name'));

    (new \ActivityLog\ActivityLog)->create([
        'user_id'      => $ses->user('id') ?: null,
        'username'     => $username !== '' ? $username : ($ses->user('email') ?: 'Guest'),
        'action'       => $verb,
        'entity_type'  => $tableName,
        'ip_address'   => $_SERVER['REMOTE_ADDR'] ?? null,
        'date_created' => date('Y-m-d H:i:s'),
    ]);

    return $data;
});

add_action('controller', function () {
    $req = new \Core\Request;
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if ($req->posted() && page() === $admin_route && URL(1) === $plugin_route && URL(2) === 'clear') {
        require plugin_path('controllers/clear_controller.php');
    }
});

add_action('basic-admin_main_content', function () {
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (page() !== $admin_route || URL(1) !== $plugin_route) return;

    $log = new \ActivityLog\ActivityLog;

    $limit = 50;
    $total_count = $log->totalCount();

    $pager = new \Core\Pager($limit, $total_count);
    $offset = $pager->offset;

    $log->limit = $limit;
    $log->offset = $offset;
    $log->order = 'desc';
    $rows = $log->findAll();

    require plugin_path('views/admin/list.php');
});
