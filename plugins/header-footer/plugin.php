<?php

// plugin.php

/**
 * Plugin Name: Header-Footer
 * Description: This seperates the header and footer of the website
 * Version: 1.0
 * Author: Afro Bear
 */

set_value([
    'admin_route'  => 'admin',
    'plugin_route' => 'header-footer',
]);

$priority = 1;

add_action('before_view', function(){
    // basic-admin renders its own complete <html> document, so don't wrap
    // admin pages in this plugin's header - config.json used to exclude
    // this whole file from admin routes for that reason, but this plugin
    // now also needs to register admin-side hooks (the header/footer
    // editor screens), so the exclusion is handled here instead.
    if (page() === 'admin') {
        return;
    }

    $links          = [];
    $link           = (object)[];
    $link -> id     = 0;
    $link -> title  = 'Home';
    $link -> slug   = 'home';
    $link -> permission   = '';
	$link -> list_order = 1;
    $link -> icon   = '';
    $links[]        = $link;
    $links = do_filter(plugin_id().'_before_menu_links',$links);

    // If a real (DB-backed) top-level menu item now covers the "home" slug,
    // it replaces the synthetic fallback above instead of appearing alongside it.
    $hasRealHomeLink = false;
    foreach ($links as $existingLink) {
        if (!empty($existingLink->id) && ($existingLink->slug ?? '') === 'home') {
            $hasRealHomeLink = true;
            break;
        }
    }
    if ($hasRealHomeLink) {
        $links = array_values(array_filter($links, fn($l) => !empty($l->id) || ($l->slug ?? '') !== 'home'));
    }

    require plugin_path('views/header.php');
},$priority);

add_action('after_view', function(){
    if (page() === 'admin') {
        return;
    }

    require plugin_path('views/footer.php');
},$priority);

add_filter('permissions', function ($permissions) {
    $permissions[] = 'manage_header_footer';
    return $permissions;
});

add_filter('basic-admin_before_admin_links', function ($links) {
    if (user_can('manage_header_footer')) {
        $vars = get_value();

        $links[] = (object)[
            'title'  => 'Header & Footer',
            'link'   => ROOT . '/' . $vars['admin_route'] . '/' . $vars['plugin_route'],
            'icon'   => 'layout-window',
            'parent' => 0,
        ];
    }

    return $links;
});

add_action('controller', function () {
    $vars = get_value();
    $req = new \Core\Request;
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (URL(1) === $plugin_route && $req->posted()) {
        switch (URL(2)) {
            case 'header':
                require plugin_path('controllers/header_controller.php');
                break;
            case 'footer':
                require plugin_path('controllers/footer_controller.php');
                break;
        }
    }
});

add_action('basic-admin_main_content', function () {
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (URL(1) !== $plugin_route) {
        return;
    }

    switch (URL(2)) {
        case 'header':
            require plugin_path('views/admin/header.php');
            break;
        case 'footer':
            require plugin_path('views/admin/footer.php');
            break;
        default:
            require plugin_path('views/admin/picker.php');
            break;
    }
});
