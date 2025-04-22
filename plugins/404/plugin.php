<?php

// plugin.php

/**
 * Plugin Name: Sample Plugin
 * Description: A sample Thunder plugin.
 * Version: 1.0
 * Author: Your Name
 */

add_action('view', function(){
    $vars = get_value();

    require plugin_path('views/view.php');
});