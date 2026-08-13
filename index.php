<?php
session_set_cookie_params([
  'httponly' => true,
  'samesite' => 'Lax',
  'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com; img-src 'self' data: https:; frame-ancestors 'self'; base-uri 'self'; form-action 'self';");

$minPHPVersion = '8.0';

if (version_compare(PHP_VERSION, $minPHPVersion, '<')) {
  die("You need a minimum of PHP Version $minPHPVersion to run this app");
}

define('DS', DIRECTORY_SEPARATOR);
define('ROOTPATH',__DIR__.DS);

if (!file_exists(__DIR__ . DS . 'config.php')) {
  header('Location: install.php');
  exit;
}

require 'config.php';
require 'app'.DS.'core'.DS.'init.php';

try {
  $db = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASSWORD);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("DB Error: " . $e->getMessage());
}

$SETTINGS = load_settings($db);

$SETTINGS['debug_mode'] ? ini_set('display_errors', 1) : ini_set('display_errors', 0);
$ACTIONS            = [];
$FILTERS            = [];
$USER_DATA          = [];
$APP['permissions'] = [];
$APP['URL']         = split_url($_GET['url'] ?? $SETTINGS['site_homepage']);

$PLUGINS = get_plugin_folders();

if(!load_plugins($PLUGINS))
{
  die("<center><h1>No plugins were found! Please put at least one plugin in the plugins folder.</center></h1>");
}

$APP['permissions'] = do_filter('permissions',$APP['permissions']);

$app = new \Core\App();
$app->index();