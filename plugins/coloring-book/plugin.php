<?php

/**
 * Plugin Name: Coloring Book Plugin
 * Description: Manage coloring books and their pages, with secure SVG upload/sanitization, a public read-only JSON API, and a GrapesJS block that renders them on any Advanced Page using the SVGColoringWidget coloring engine.
 * Version: 1.0.0
 * Author: Afro Lion
 */

set_value([
    'admin_route'  => 'admin',
    'plugin_route' => 'coloring-book',
    'table'        => [
        'books_table' => 'coloring_books',
        'pages_table' => 'coloring_book_pages',
    ],
    'storage_dir'  => 'uploads/coloring-books',
]);

$db = new \Core\Database;
$table = get_value()['table'];

if (!$db->tableExists($table)) {
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",", $db->missing_tables));
    die;
}

require_once plugin_path('includes/SvgSanitizer.php');

add_filter('permissions', function ($permissions) {
    $permissions[] = 'view_coloring_books';
    $permissions[] = 'create_coloring_books';
    $permissions[] = 'edit_coloring_books';
    $permissions[] = 'delete_coloring_books';
    $permissions[] = 'manage_coloring_pages';
    return $permissions;
});

add_filter('basic-admin_before_admin_links', function ($links) {
    if (user_can('view_coloring_books')) {
        $vars = get_value();

        $links[] = (object) [
            'title'  => 'Coloring Books',
            'link'   => ROOT . '/' . $vars['admin_route'] . '/' . $vars['plugin_route'],
            'icon'   => 'palette',
            'parent' => 0,
        ];
    }
    return $links;
});

// ---------------------------------------------------------------------
// Admin CRUD dispatch - mirrors the redirects/basic-pages convention:
// /admin/coloring-book                          list books
// /admin/coloring-book/add                       add book
// /admin/coloring-book/edit/{id}                 edit book
// /admin/coloring-book/delete/{id}                delete book (POST)
// /admin/coloring-book/pages/{bookId}             manage a book's pages
// /admin/coloring-book/pages/{bookId}/add         add page (SVG upload)
// /admin/coloring-book/pages/{bookId}/edit/{id}   edit page
// /admin/coloring-book/pages/{bookId}/delete/{id} delete page (POST)
// /admin/coloring-book/pages/{bookId}/move-up/{id}   reorder (POST)
// /admin/coloring-book/pages/{bookId}/move-down/{id} reorder (POST)
// ---------------------------------------------------------------------
add_action('controller', function () {
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    $req = new \Core\Request;
    if (!$req->posted() || page() !== $admin_route || URL(1) !== $plugin_route) return;

    if (URL(2) === 'pages') {
        $bookId = (int) URL(3);
        switch (URL(4)) {
            case 'add':
                require plugin_path('controllers/add_page_controller.php');
                break;
            case 'edit':
                require plugin_path('controllers/edit_page_controller.php');
                break;
            case 'delete':
                require plugin_path('controllers/delete_page_controller.php');
                break;
            case 'move-up':
                require plugin_path('controllers/reorder_page_controller.php');
                break;
            case 'move-down':
                require plugin_path('controllers/reorder_page_controller.php');
                break;
        }
        return;
    }

    switch (URL(2)) {
        case 'add':
            require plugin_path('controllers/add_book_controller.php');
            break;
        case 'edit':
            require plugin_path('controllers/edit_book_controller.php');
            break;
        case 'delete':
            require plugin_path('controllers/delete_book_controller.php');
            break;
    }
});

add_action('basic-admin_main_content', function () {
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (page() !== $admin_route || URL(1) !== $plugin_route) return;

    $books = new \ColoringBook\ColoringBooks;
    $pages = new \ColoringBook\ColoringBookPages;

    if (URL(2) === 'pages') {
        $bookId = (int) URL(3);
        $book = $books->find($bookId);
        if (!$book) {
            message('That coloring book was not found.', 'fail');
            redirect($admin_route . '/' . $plugin_route);
        }

        switch (URL(4)) {
            case 'add':
                require plugin_path('views/admin/add_page.php');
                break;
            case 'edit':
                $pageId = (int) URL(5);
                $row = $pages->find($pageId);
                if (!$row || (int) $row->coloring_book_id !== $bookId) {
                    message('That page was not found in this coloring book.', 'fail');
                    redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId);
                }
                require plugin_path('views/admin/edit_page.php');
                break;
            default:
                $rows = $pages->findForBook($bookId);
                require plugin_path('views/admin/pages.php');
                break;
        }
        return;
    }

    switch (URL(2)) {
        case 'add':
            require plugin_path('views/admin/add.php');
            break;
        case 'edit':
            $id = (int) URL(3);
            $row = $books->find($id);
            if (!$row) {
                message('That coloring book was not found.', 'fail');
                redirect($admin_route . '/' . $plugin_route);
            }
            require plugin_path('views/admin/edit.php');
            break;
        default:
            $limit = 25;
            $total_count = $books->totalCount();
            $pager = new \Core\Pager($limit, $total_count);
            $books->limit = $limit;
            $books->offset = $pager->offset;
            $rows = $books->findAll();
            require plugin_path('views/admin/list.php');
            break;
    }
});

// ---------------------------------------------------------------------
// Public, read-only JSON API - /coloring-book/api/{slug}
// (Not /api/coloring-books/{slug} as first sketched: ThunderPHP has no
// existing /api/ namespace convention anywhere else, so this stays under
// the plugin's own top-level public route instead, the same way
// /contact-form and /search are public routes under their own plugin's
// name rather than a shared prefix.)
// ---------------------------------------------------------------------
add_action('controller', function () {
    if (page() !== 'coloring-book' || URL(1) !== 'api') return;

    require plugin_path('controllers/api_controller.php');
    exit;
});

// ---------------------------------------------------------------------
// Frontend: a small, always-loaded initializer script that looks for
// .thunder-coloring-book markup and only *then* pulls in the heavier
// SVGColoringWidget engine - so every other page on the site pays only
// for a tiny (few-hundred-byte) script, not the full widget.
// ---------------------------------------------------------------------
add_action('before_head_close', function () {
    if (page() === get_value()['admin_route']) return;
    ?>
    <script>
      window.coloringBookApiBase = "<?= esc(ROOT) ?>/coloring-book/api/";
    </script>
    <script src="<?= plugin_http_path('assets/js/frontend-init.js') ?>" defer></script>
    <?php
});
