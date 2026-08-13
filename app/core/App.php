<?php

namespace Core;
defined('ROOT') or die("Direct script access denied");
/**
 * App Class
 */
class App
{
    public function index()
    {
        do_action('before_controller');
        do_action('controller');
        do_action('after_controller');
        ob_start();
        do_action('before_view');
        $before_content = ob_get_contents();
        do_action('view');
        $after_content = ob_get_contents();

        if (strcmp($before_content, $after_content) === 0) {
            if(page() != '404'){
                do_action('404_not_found', ['url' => URL()]);
                redirect('404');
            }
        }

        do_action('after_view');
    }
}