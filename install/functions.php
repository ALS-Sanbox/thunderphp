<?php
// install/functions.php
//
// Shared helpers for the install wizard. Deliberately self-contained and
// framework-free: this runs before config.php exists, so it can't rely on
// the app's normal bootstrap (app/core/init.php, the plugin autoloader,
// the Model/Database classes, etc.) the way every other PHP file in this
// codebase can.

defined('INSTALL_ROOT') or die('Direct access not permitted');

const INSTALL_MINIMAL_PLUGINS = [
    '404',
    'basic-admin',
    'basic-auth',
    'header-footer',
    'settings',
    'user-roles',
    'users-manager',
];

function install_csrf_token(): string
{
    if (empty($_SESSION['install_csrf'])) {
        $_SESSION['install_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['install_csrf'];
}

function install_csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . htmlspecialchars(install_csrf_token()) . '">';
}

function install_csrf_verify(?string $token): bool
{
    return !empty($_SESSION['install_csrf']) && is_string($token) && hash_equals($_SESSION['install_csrf'], $token);
}

function install_esc(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function install_check_requirements(): array
{
    $checks = [];

    $checks[] = [
        'label'  => 'PHP version 8.0 or higher',
        'pass'   => version_compare(PHP_VERSION, '8.0', '>='),
        'detail' => 'Detected PHP ' . PHP_VERSION,
    ];

    foreach (['pdo_mysql', 'mbstring', 'json', 'fileinfo'] as $ext) {
        $checks[] = [
            'label'  => "PHP extension: $ext",
            'pass'   => extension_loaded($ext),
            'detail' => extension_loaded($ext) ? 'Loaded' : 'Not loaded',
        ];
    }

    $checks[] = [
        'label'  => 'Application root is writable (to create config.php)',
        'pass'   => is_writable(INSTALL_ROOT),
        'detail' => INSTALL_ROOT,
    ];

    $uploadsDir = INSTALL_ROOT . 'uploads';
    $checks[] = [
        'label'  => "'uploads/' directory is writable",
        'pass'   => is_dir($uploadsDir) && is_writable($uploadsDir),
        'detail' => $uploadsDir,
    ];

    return $checks;
}

function install_requirements_pass(array $checks): bool
{
    foreach ($checks as $check) {
        if (!$check['pass']) return false;
    }
    return true;
}

function install_detect_root_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');

    return $scheme . '://' . $host . $scriptDir;
}

function install_test_db_connection(string $host, string $name, string $user, string $password): ?string
{
    try {
        new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        return null;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function install_write_config(array $db, string $root): bool
{
    $template = <<<'PHP'
<?php

define('APP_NAME', 'ThunderPHP');
define('APP_DESCRIPTION', 'An Plugin Based PHP Framework, Everything here is an Plugin.');

define('DB_NAME', %s);
define('DB_USER', %s);
define('DB_PASSWORD', %s);
define('DB_HOST', %s);
define('DB_DRIVER', 'mysql');

define('ROOT', %s);
PHP;

    $content = sprintf(
        $template,
        var_export($db['name'], true),
        var_export($db['user'], true),
        var_export($db['password'], true),
        var_export($db['host'], true),
        var_export($root, true)
    );

    return file_put_contents(INSTALL_ROOT . 'config.php', $content) !== false;
}

/** Is there already a working, fully-created install here? Checked by
 * actually querying siteusers rather than just "does config.php exist",
 * so an interrupted install (e.g. closed the tab before creating the
 * admin account) can be resumed instead of being permanently locked out. */
function install_already_completed(): bool
{
    if (!file_exists(INSTALL_ROOT . 'config.php')) {
        return false;
    }

    require_once INSTALL_ROOT . 'config.php';

    try {
        $pdo = new PDO(DB_DRIVER . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $stmt = $pdo->query("SELECT COUNT(*) FROM siteusers");
        return ((int) $stmt->fetchColumn()) > 0;
    } catch (\Throwable $e) {
        return false;
    }
}

function install_get_all_plugin_folders(): array
{
    $folders = [];
    foreach (glob(INSTALL_ROOT . 'plugins' . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) as $dir) {
        $folders[] = basename($dir);
    }
    sort($folders);
    return $folders;
}

function install_set_plugin_active(string $pluginFolder, bool $active): void
{
    $path = INSTALL_ROOT . 'plugins' . DIRECTORY_SEPARATOR . $pluginFolder . DIRECTORY_SEPARATOR . 'config.json';
    if (!file_exists($path)) {
        return;
    }

    $json = json_decode(file_get_contents($path));
    if (!$json) {
        return;
    }

    $json->active = $active;
    file_put_contents($path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function install_validate_admin_account(array $data): array
{
    $errors = [];

    if (empty($data['first_name']) || !preg_match('/^[a-zA-Z]+$/', trim($data['first_name']))) {
        $errors['first_name'] = 'First name is required and can only contain letters.';
    }
    if (empty($data['last_name']) || !preg_match('/^[a-zA-Z]+$/', trim($data['last_name']))) {
        $errors['last_name'] = 'Last name is required and can only contain letters.';
    }
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'A valid email address is required.';
    }
    if (empty($data['password']) || strlen($data['password']) < 8) {
        $errors['password'] = 'Password must be at least 8 characters long.';
    } elseif (!preg_match('/[\W]/', $data['password'])) {
        $errors['password'] = 'Password must contain at least one special character.';
    } elseif (($data['password'] ?? '') !== ($data['confirm_password'] ?? '')) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    return $errors;
}
