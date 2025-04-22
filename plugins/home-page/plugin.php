<?php

// plugin.php

/**
 * Plugin Name: Home Page
 * Description: This is the homepage 
 * Version: 1.0
 * Author: Afro Bear
 */


add_action('view', function(){
    require plugin_path('views/view.php');
});
