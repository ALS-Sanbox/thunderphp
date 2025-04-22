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
    require plugin_path('views/header.php');
},$priority);

add_action('after_view', function(){
    require plugin_path('views/footer.php');
},$priority);

