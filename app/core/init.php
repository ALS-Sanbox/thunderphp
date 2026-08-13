<?php

defined('ROOT') or die("Direct script access denied");

spl_autoload_register(function($classname){
    $parts = explode("\\",$classname);
    $classname = end($parts);
    $path = 'app' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . ucfirst($classname) . '.php';
    if(file_exists($path)){
        require_once $path;
    } else {
        $called_from = debug_backtrace();
        
        $key = array_search(__FUNCTION__, array_column($called_from, 'function'));
        $plugin_path = get_plugin_dir(debug_backtrace()[$key]['file']) . 'models' . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $classname) . '.php';

        if (stripos($plugin_path, DIRECTORY_SEPARATOR . 'controllers') !== false) {
            $plugin_path = str_ireplace(DIRECTORY_SEPARATOR . 'controllers', "", $plugin_path);
        }
        
        if(file_exists($plugin_path)){
            require_once $plugin_path;
        } else {
            // Don't die() here - return so PHP can try any other registered
            // autoloader (e.g. Composer's, for vendor packages) and only
            // raise its own normal "Class not found" error if none resolve
            // it either. A hard die() here would kill the whole request
            // before a later-registered autoloader ever got a chance to run.
            return false;
        }
    }
});

require 'functions.php';
require 'extensions.php';
require 'Database.php';
require 'Model.php';
require 'App.php'; //Always last