<?php

/**
 * Plugin Name: Search Plugin
 * Description: Site-wide search across published pages and posts, at /search?q=term.
 * Version: 1.0
 * Author: Afro Lion
 */

set_value([
    'plugin_route' => 'search',
]);

add_action('view', function () {
    $db = new \Core\Database;
    $q = trim((string) ($_GET['q'] ?? ''));
    $results = [];

    if ($q !== '') {
        $like = '%' . $q . '%';

        if ($db->tableExists('pages')) {
            $rows = $db->query(
                "SELECT title, description, slug, 'Page' AS type FROM pages
                 WHERE disabled = 0 AND date_deleted IS NULL
                 AND (title LIKE ? OR description LIKE ? OR content LIKE ?)
                 ORDER BY title ASC LIMIT 50",
                [$like, $like, $like]
            );
            $results = array_merge($results, $rows);
        }

        if ($db->tableExists('posts')) {
            $rows = $db->query(
                "SELECT title, description, slug, 'Post' AS type FROM posts
                 WHERE disabled = 0 AND date_deleted IS NULL
                 AND (title LIKE ? OR description LIKE ? OR content LIKE ?)
                 ORDER BY title ASC LIMIT 50",
                [$like, $like, $like]
            );
            $results = array_merge($results, $rows);
        }

        usort($results, fn($a, $b) => strcasecmp($a->title ?? '', $b->title ?? ''));
    }

    require plugin_path('views/frontend/view.php');
});
