<?php
defined('INSTALL_ROOT') or die('Direct access not permitted');

if (empty($_SESSION['install_profile']) || empty($_SESSION['install_db'])) {
    header('Location: install.php?step=profile');
    exit;
}

// Already fully installed (e.g. the user navigated back here after
// finishing) - don't re-run migrations against a live site.
if (install_already_completed()) {
    header('Location: install.php?step=done');
    exit;
}

$log = [];
$fatalError = null;

$db = $_SESSION['install_db'];
$root = install_detect_root_url();

if (!install_write_config($db, $root)) {
    $fatalError = "Couldn't write config.php. Check that the application root is writable and try again.";
} else {
    require_once INSTALL_ROOT . 'config.php';

    if (!defined('FCPATH')) {
        define('FCPATH', INSTALL_ROOT);
    }
    chdir(INSTALL_ROOT);

    require_once INSTALL_ROOT . 'app/core/functions.php';
    require_once INSTALL_ROOT . 'app/core/Database.php';
    require_once INSTALL_ROOT . 'app/models/Migration.php';
    require_once INSTALL_ROOT . 'app/thunder/thunder.php';

    $thunder = new \Thunder\Thunder();

    ob_start();
    try {
        if ($_SESSION['install_profile'] === 'minimal') {
            foreach (INSTALL_MINIMAL_PLUGINS as $pluginName) {
                $migrationsFolder = INSTALL_ROOT . 'plugins' . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . 'migrations';
                if (!is_dir($migrationsFolder) || empty(glob($migrationsFolder . DIRECTORY_SEPARATOR . '*.php'))) {
                    continue;
                }
                $thunder->doMigrate([$pluginName]);
            }

            foreach (install_get_all_plugin_folders() as $pluginFolder) {
                install_set_plugin_active($pluginFolder, in_array($pluginFolder, INSTALL_MINIMAL_PLUGINS, true));
            }
        } else {
            $thunder->doMigrate(['all']);
        }
    } catch (\Throwable $e) {
        $fatalError = 'Migration error: ' . $e->getMessage();
    }
    $log[] = ob_get_clean();
}

$hadLoggedError = $fatalError === null && str_contains(implode("\n", $log), 'Error');

$step = 'installing';
require INSTALL_ROOT . 'install/header.php';
?>
<h2 class="h5 mb-1">Installing&hellip;</h2>
<p class="text-muted mb-3">Setting up the database for the <?= $_SESSION['install_profile'] === 'minimal' ? 'Minimal' : 'Standard' ?> profile.</p>

<div class="install-log mb-4"><?= install_esc(trim(implode("\n", $log))) ?: 'No output.' ?></div>

<?php if ($fatalError): ?>
    <div class="alert alert-danger"><?= install_esc($fatalError) ?></div>
    <a href="install.php?step=database" class="btn btn-outline-secondary w-100">Back to database settings</a>
<?php else: ?>
    <?php if ($hadLoggedError): ?>
        <div class="alert alert-warning">Some migrations reported errors above &mdash; review the log before continuing.</div>
    <?php else: ?>
        <div class="alert alert-success">Database set up successfully.</div>
    <?php endif; ?>
    <a href="install.php?step=site" class="btn btn-primary w-100">Continue</a>
<?php endif; ?>
<?php
require INSTALL_ROOT . 'install/footer.php';
