<?php
// tests/bootstrap.php
//
// Minimal bootstrap for both test tiers:
//  - Unit tests only need app/core/functions.php (pure/near-pure helpers).
//  - Integration tests additionally need real DB constants (from env vars,
//    so CI and local runs can point at different scratch databases) and
//    the Database/Model/Migration base classes - but never the full plugin
//    system, since these tests exercise individual classes directly.

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
}

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('ROOT')) {
    define('ROOT', 'http://localhost');
}

if (!defined('FCPATH')) {
    define('FCPATH', dirname(__DIR__) . DS);
}

if (!defined('APP_NAME')) {
    define('APP_NAME', 'ThunderPHP Test Suite');
}

// Database constants for integration tests. Never used by the app itself in
// this process - only by tests that explicitly need a real connection.
if (!defined('DB_DRIVER')) {
    define('DB_DRIVER', getenv('TEST_DB_DRIVER') ?: 'mysql');
    define('DB_HOST', getenv('TEST_DB_HOST') ?: '127.0.0.1');
    define('DB_NAME', getenv('TEST_DB_NAME') ?: 'thunderphp_test');
    define('DB_USER', getenv('TEST_DB_USER') ?: 'root');
    define('DB_PASSWORD', getenv('TEST_DB_PASSWORD') ?: '');
}

require_once FCPATH . 'app/core/functions.php';

// Defining these classes doesn't connect to anything - Database only
// connects when actually instantiated, which integration tests do
// explicitly against the TEST_DB_* connection above.
require_once FCPATH . 'app/core/Database.php';
require_once FCPATH . 'app/core/Model.php';
require_once FCPATH . 'app/models/Migration.php';
