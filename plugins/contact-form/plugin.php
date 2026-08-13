<?php

/**
 * Plugin Name: Contact Form Plugin
 * Description: A public contact form at /contact-form, an admin inbox to read submissions, and email notification via the configured mailer.
 * Version: 1.0
 * Author: Afro Lion
 */

set_value([
    'admin_route'  => 'admin',
    'plugin_route' => 'contact-form',
    'table'        => ['contact_submissions_table' => 'contact_submissions'],
]);

$db = new \Core\Database;
$table = get_value()['table'];

if (!$db->tableExists($table)) {
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",", $db->missing_tables));
    die;
}

add_filter('permissions', function ($permissions) {
    $permissions[] = 'view_contact_submissions';
    $permissions[] = 'delete_contact_submissions';
    $permissions[] = 'manage_contact_form';
    return $permissions;
});

add_filter('basic-admin_before_admin_links', function ($links) {
    if (user_can('view_contact_submissions')) {
        $vars = get_value();

        $links[] = (object) [
            'title'  => 'Contact Submissions',
            'link'   => ROOT . '/' . $vars['admin_route'] . '/' . $vars['plugin_route'],
            'icon'   => 'envelope',
            'parent' => 0,
        ];
    }
    return $links;
});

add_action('controller', function () {
    $req = new \Core\Request;
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (!$req->posted()) return;

    if (page() === $plugin_route) {
        require plugin_path('controllers/submit_controller.php');
        return;
    }

    if (page() === $admin_route && URL(1) === $plugin_route) {
        $id = URL(3) ?? null;

        switch (URL(2)) {
            case 'delete':
                require plugin_path('controllers/delete_controller.php');
                break;
            case 'read':
                require plugin_path('controllers/mark_read_controller.php');
                break;
        }
    }
});

add_action('view', function () {
    $vars = get_value();
    $plugin_route = $vars['plugin_route'];

    if (page() !== $plugin_route) return;

    require plugin_path('views/frontend/view.php');
});

add_action('basic-admin_main_content', function () {
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (page() !== $admin_route || URL(1) !== $plugin_route) return;

    $submissions = new \ContactForm\ContactSubmissions;

    $id = URL(2) ?? null;
    if ($id && is_numeric($id)) {
        $row = $submissions->find($id) ?: null;

        if ($row && empty($row->is_read) && user_can('view_contact_submissions')) {
            $submissions->update($id, ['is_read' => 1]);
            $row->is_read = 1;
        }

        require plugin_path('views/admin/view.php');
        return;
    }

    $limit = (int) setting('pagination_limit', 25) ?: 25;
    $total_count = $submissions->totalCount();

    $pager = new \Core\Pager($limit, $total_count);
    $offset = $pager->offset;

    $submissions->limit = $limit;
    $submissions->offset = $offset;
    $rows = $submissions->findAll();

    require plugin_path('views/admin/list.php');
});
