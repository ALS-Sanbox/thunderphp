<?php
// install.php
//
// Standalone entry point for first-time setup, in the spirit of
// WordPress/Drupal's install wizards. Deliberately does NOT require
// config.php or any part of the plugin system - this is what runs when
// config.php doesn't exist yet. index.php redirects here automatically
// when it detects that.

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

define('INSTALL_ROOT', __DIR__ . DIRECTORY_SEPARATOR);

require INSTALL_ROOT . 'install' . DIRECTORY_SEPARATOR . 'functions.php';

$steps = ['profile', 'requirements', 'database', 'installing', 'site', 'done'];
$step = in_array($_GET['step'] ?? '', $steps, true) ? $_GET['step'] : 'profile';

// Already fully set up (a real admin account exists) - don't let this
// wizard run again against a live site. The 'done' step handles showing
// the success screen for the request that just finished the install.
if ($step !== 'done' && install_already_completed()) {
    header('Location: install.php?step=done');
    exit;
}

require INSTALL_ROOT . 'install' . DIRECTORY_SEPARATOR . "step-$step.php";
