<?php   
session_start();

$minPHPVersion = '8.0';
if(phpversion() <= $minPHPVersion)
  die("You need a minimum of PHP Version $minPHPVersion to run this app");

define('DS', DIRECTORY_SEPARATOR);
define('ROOTPATH',__DIR__.DS);

require 'config.php';
require 'app'.DS.'core'.DS.'init.php';

DEBUG ? ini_set('display_errors', 1) : ini_set('display_errors', 0);

$ACTIONS            = [];
$FILTERS            = [];
$USER_DATA          = [];
$APP['permissions'] = [];
$APP['URL']         = split_url($_GET['url'] ?? 'home');

$PLUGINS = get_plugin_folders();
if(!load_plugins($PLUGINS))
{
  die("<center><h1>No plugins were found! Please put at least one plugin in the plugins folder.</center></h1>");
}

$APP['permissions'] = do_filter('permissions',$APP['permissions']);

$app = new \Core\App();
$app->index();