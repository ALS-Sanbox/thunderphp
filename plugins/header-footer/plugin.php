<?php

// plugin.php

/**
 * Plugin Name: Header-Footer
 * Description: This seperates the header and footer of the website
 * Version: 1.0
 * Author: Afro Bear
 */

$priority = 1;

add_action('before_view', function(){
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
    require plugin_path('views/footer.php');
},$priority);

