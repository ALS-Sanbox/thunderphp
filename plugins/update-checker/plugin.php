<?php

/**
 * Plugin Name: Update Checker Plugin
 * Description: Checks GitHub for newer ThunderPHP releases (once a day, cached), shows a dashboard notice when one is available, and can download/apply it - manually via a button, or automatically if enabled. Always backs up files and database first; only ever touches app/, plugins/, and other framework paths, never config.php, uploads/, or site-overrides/.
 * Version: 1.0
 * Author: Afro Lion
 */

set_value([
    'admin_route'  => 'admin',
    'plugin_route' => 'update-checker',
    'table'        => ['settings_table' => 'settings'],
]);

$db = new \Core\Database;
$table = get_value()['table'];

if (!$db->tableExists($table)) {
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",", $db->missing_tables));
    die;
}

const UPDATE_CHECKER_REPO = 'ALS-Sanbox/thunderphp';
const UPDATE_CHECKER_INTERVAL_SECONDS = 86400; // once a day

/**
 * Writes directly via \Core\Database, same reason as seo_set_setting()/
 * google_auth_set_setting(): another plugin's own model class can't be
 * safely instantiated from outside that plugin's folder.
 */
function update_checker_set_setting(\Core\Database $db, string $key, string $value): bool {
    $row = $db->fetch("SELECT id FROM settings WHERE `key` = ?", [$key]);

    if ($row) {
        $db->query("UPDATE settings SET `value` = ?, updated_at = NOW() WHERE id = ?", [$value, $row->id]);
        return true;
    }

    $db->query("INSERT INTO settings (`key`, `value`, `type`, `environment`) VALUES (?, ?, 'string', 'production')", [$key, $value]);
    return true;
}

/**
 * Fetches the latest published GitHub release for this repo. Returns null
 * on any failure (network, non-200, unexpected shape) - always fails soft,
 * since this can never be allowed to break an admin page load.
 */
function update_checker_fetch_latest(): ?array {
    $context = stream_context_create([
        'http' => [
            'method'        => 'GET',
            'header'        => "User-Agent: ThunderPHP-UpdateChecker\r\nAccept: application/vnd.github+json\r\n",
            'timeout'       => 5,
            'ignore_errors' => true,
        ],
        'ssl' => [
            'verify_peer'      => true,
            'verify_peer_name' => true,
        ],
    ]);

    $response = @file_get_contents('https://api.github.com/repos/' . UPDATE_CHECKER_REPO . '/releases/latest', false, $context);
    if ($response === false) return null;

    $data = json_decode($response, true);
    if (!is_array($data) || empty($data['tag_name']) || empty($data['zipball_url'])) return null;

    return [
        'version'      => ltrim((string) $data['tag_name'], 'vV'),
        'url'          => (string) ($data['html_url'] ?? ('https://github.com/' . UPDATE_CHECKER_REPO . '/releases')),
        'zipball_url'  => (string) $data['zipball_url'],
    ];
}

function update_checker_run_check(\Core\Database $db): void {
    $latest = update_checker_fetch_latest();

    update_checker_set_setting($db, 'update_check_last_checked_at', date('Y-m-d H:i:s'));

    if ($latest) {
        update_checker_set_setting($db, 'update_check_latest_version', $latest['version']);
        update_checker_set_setting($db, 'update_check_latest_url', $latest['url']);
        update_checker_set_setting($db, 'update_check_latest_zipball_url', $latest['zipball_url']);
    }
}

/** Runs the real check only if the cached result is missing or stale. */
function update_checker_maybe_check(\Core\Database $db): void {
    if (!setting('update_check_enabled', true)) return;

    $lastChecked = (string) setting('update_check_last_checked_at', '');

    if ($lastChecked !== '' && (time() - strtotime($lastChecked)) < UPDATE_CHECKER_INTERVAL_SECONDS) {
        return;
    }

    update_checker_run_check($db);
}

// ---------------------------------------------------------------------
// Download & apply
// ---------------------------------------------------------------------

const UPDATE_CHECKER_STAGING_DIR = 'update-staging';
const UPDATE_CHECKER_BACKUP_DIR = 'update-backups';

/** Paths (relative to site root) an apply is allowed to overwrite. Never config.php, uploads/, site-overrides/, or anything else. */
const UPDATE_CHECKER_SAFE_PATHS = ['app', 'plugins', 'assets', 'install', 'index.php', 'install.php', 'thunder', 'VERSION', '.htaccess', '403.html', '500.html'];

function update_checker_copy_recursive(string $src, string $dst): void {
    if (is_dir($src)) {
        if (!is_dir($dst)) {
            mkdir($dst, 0755, true);
        }
        foreach (array_diff(scandir($src), ['.', '..']) as $item) {
            update_checker_copy_recursive($src . DIRECTORY_SEPARATOR . $item, $dst . DIRECTORY_SEPARATOR . $item);
        }
    } elseif (is_file($src)) {
        copy($src, $dst);
    }
}

function update_checker_delete_recursive(string $path): void {
    if (is_dir($path) && !is_link($path)) {
        foreach (array_diff(scandir($path), ['.', '..']) as $item) {
            update_checker_delete_recursive($path . DIRECTORY_SEPARATOR . $item);
        }
        rmdir($path);
    } elseif (file_exists($path)) {
        unlink($path);
    }
}

/** Locks down a backup/staging directory the same way uploads/.htaccess already does for uploads/ - these can contain a full DB dump. */
function update_checker_protect_dir(string $dir): void {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $htaccess = $dir . DIRECTORY_SEPARATOR . '.htaccess';
    if (!file_exists($htaccess)) {
        file_put_contents($htaccess, "Require all denied\nDeny from all\n");
    }
}

/** Downloads the release archive to a local zip file. Returns the local path, or null on any failure. */
function update_checker_download_release(string $zipballUrl): ?string {
    update_checker_protect_dir(ROOTPATH . UPDATE_CHECKER_STAGING_DIR);

    $context = stream_context_create([
        'http' => [
            'method'        => 'GET',
            'header'        => "User-Agent: ThunderPHP-UpdateChecker\r\n",
            'timeout'       => 120,
            'ignore_errors' => true,
            'follow_location' => 1,
        ],
        'ssl' => [
            'verify_peer'      => true,
            'verify_peer_name' => true,
        ],
    ]);

    $zipContents = @file_get_contents($zipballUrl, false, $context);
    if ($zipContents === false || $zipContents === '') return null;

    $zipPath = ROOTPATH . UPDATE_CHECKER_STAGING_DIR . DIRECTORY_SEPARATOR . 'release.zip';
    if (file_put_contents($zipPath, $zipContents) === false) return null;

    return $zipPath;
}

/** Extracts the downloaded zip and returns the path to its real content root (GitHub archives wrap everything in one folder). */
function update_checker_extract_zip(string $zipPath): ?string {
    if (!class_exists('ZipArchive')) return null;

    $extractDir = ROOTPATH . UPDATE_CHECKER_STAGING_DIR . DIRECTORY_SEPARATOR . 'extracted';
    update_checker_delete_recursive($extractDir);
    mkdir($extractDir, 0755, true);

    $zip = new \ZipArchive();
    if ($zip->open($zipPath) !== true) return null;

    $ok = $zip->extractTo($extractDir);
    $zip->close();
    if (!$ok) return null;

    $entries = array_values(array_diff(scandir($extractDir), ['.', '..']));
    if (count($entries) === 1 && is_dir($extractDir . DIRECTORY_SEPARATOR . $entries[0])) {
        return $extractDir . DIRECTORY_SEPARATOR . $entries[0];
    }

    return $extractDir;
}

/** Pure-PHP/PDO schema+data dump - no shelling out to mysqldump, matching this app never using exec()/shell_exec() anywhere. */
function update_checker_backup_database(\Core\Database $db, string $destPath): bool {
    $tables = $db->query("SHOW TABLES", [], 'assoc');
    if (empty($tables)) return false;

    $fh = fopen($destPath, 'w');
    if (!$fh) return false;

    fwrite($fh, "-- ThunderPHP update-checker backup, " . date('Y-m-d H:i:s') . "\n");
    fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n\n");

    foreach ($tables as $tableRow) {
        $tableName = reset($tableRow);

        $createRow = $db->fetch("SHOW CREATE TABLE `$tableName`", []);
        if (!$createRow) continue;
        $createSql = (array) $createRow;
        $createSql = end($createSql);

        fwrite($fh, "DROP TABLE IF EXISTS `$tableName`;\n{$createSql};\n\n");

        $rows = $db->query("SELECT * FROM `$tableName`", [], 'assoc');
        foreach (array_chunk($rows, 200) as $chunk) {
            if (empty($chunk)) continue;

            $columns = array_keys($chunk[0]);
            $columnList = '`' . implode('`,`', $columns) . '`';

            $valueGroups = [];
            foreach ($chunk as $row) {
                $values = array_map(fn($v) => $db->quote($v === null ? null : (string) $v), $row);
                $valueGroups[] = '(' . implode(',', $values) . ')';
            }

            fwrite($fh, "INSERT INTO `$tableName` ($columnList) VALUES " . implode(',', $valueGroups) . ";\n");
        }
        fwrite($fh, "\n");
    }

    fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
    fclose($fh);

    return true;
}

/** Recursive copy of the live site's own safe-path files, before anything is overwritten. */
function update_checker_backup_files(): string {
    $backupRoot = ROOTPATH . UPDATE_CHECKER_BACKUP_DIR . DIRECTORY_SEPARATOR . date('Ymd_His');
    update_checker_protect_dir(ROOTPATH . UPDATE_CHECKER_BACKUP_DIR);
    mkdir($backupRoot, 0755, true);

    foreach (UPDATE_CHECKER_SAFE_PATHS as $path) {
        $src = ROOTPATH . $path;
        if (file_exists($src)) {
            update_checker_copy_recursive($src, $backupRoot . DIRECTORY_SEPARATOR . $path);
        }
    }

    return $backupRoot;
}

/**
 * Runs any migrations the new release added, in-process - requiring
 * app/thunder/thunder.php directly and calling Thunder::doMigrate(['all'])
 * rather than shelling out to the CLI (this app never uses exec()/
 * shell_exec() anywhere). Safe to call unconditionally after every apply:
 * Phase 2's migrations_log means anything already applied is skipped, not
 * re-run. Output is captured (thunder.php echoes progress) rather than
 * leaked into the page.
 */
function update_checker_run_migrations(): string {
    // Every migration file guards on defined('FCPATH') - only ever defined
    // by the `thunder` CLI bootstrap, never by a normal web request. Define
    // it here (same value ROOTPATH already has: site root + trailing
    // separator) so migrations run correctly from this in-process call too.
    if (!defined('FCPATH')) {
        define('FCPATH', ROOTPATH);
    }

    // require_once (not class_exists()+conditional require) deliberately -
    // class_exists() with its default $autoload=true tries to trigger this
    // app's own autoloader first, which has no idea how to resolve
    // \Thunder\Thunder (it's not in app/models/ or any plugin folder) and
    // fatals rather than just returning false.
    require_once ROOTPATH . 'app' . DIRECTORY_SEPARATOR . 'thunder' . DIRECTORY_SEPARATOR . 'thunder.php';

    $thunder = new \Thunder\Thunder();

    ob_start();
    try {
        $thunder->doMigrate(['all']);
    } catch (\Throwable $e) {
        ob_end_clean();
        return "Migration run failed: " . $e->getMessage();
    }

    return ob_get_clean();
}

/**
 * Downloads, backs up, and applies the currently-known latest release.
 * Only ever touches UPDATE_CHECKER_SAFE_PATHS - config.php, uploads/, and
 * site-overrides/ are never in that list, so they're never touched. Runs
 * any migrations the release added (see update_checker_run_migrations())
 * after files are in place - safe regardless of how many times this has
 * run before, thanks to migrations_log.
 *
 * Returns a report array: ['ok' => bool, 'message' => string, 'backup' => ?string, 'files_changed' => int].
 */
function update_checker_apply_update(\Core\Database $db): array {
    if (!setting('update_check_enabled', true)) {
        return ['ok' => false, 'message' => 'Update checking is disabled.'];
    }

    $zipballUrl = (string) setting('update_check_latest_zipball_url', '');
    $latestVersion = (string) setting('update_check_latest_version', '');

    if ($zipballUrl === '' || $latestVersion === '') {
        return ['ok' => false, 'message' => 'No known update to apply - run a check first.'];
    }

    if (version_compare($latestVersion, app_version(), '<=')) {
        return ['ok' => false, 'message' => "Already up to date (v" . app_version() . ")."];
    }

    if (!class_exists('ZipArchive')) {
        return ['ok' => false, 'message' => 'The PHP zip extension (ZipArchive) is not available on this server - cannot apply updates automatically.'];
    }

    @set_time_limit(300);

    $zipPath = update_checker_download_release($zipballUrl);
    if ($zipPath === null) {
        return ['ok' => false, 'message' => 'Download failed. Nothing was changed.'];
    }

    $releaseRoot = update_checker_extract_zip($zipPath);
    if ($releaseRoot === null) {
        return ['ok' => false, 'message' => 'Could not extract the downloaded update. Nothing was changed.'];
    }

    // Backup - files then database - must both succeed before anything live is touched.
    $backupDir = update_checker_backup_files();
    $dbBackupOk = update_checker_backup_database($db, $backupDir . DIRECTORY_SEPARATOR . 'database.sql');

    if (!is_dir($backupDir) || !$dbBackupOk) {
        return ['ok' => false, 'message' => 'Backup failed - aborting before touching any live files. Nothing was changed.'];
    }

    $filesChanged = 0;
    foreach (UPDATE_CHECKER_SAFE_PATHS as $path) {
        $src = $releaseRoot . DIRECTORY_SEPARATOR . $path;
        if (file_exists($src)) {
            update_checker_copy_recursive($src, ROOTPATH . $path);
            $filesChanged++;
        }
    }

    update_checker_delete_recursive(ROOTPATH . UPDATE_CHECKER_STAGING_DIR . DIRECTORY_SEPARATOR . 'extracted');
    @unlink($zipPath);

    $migrationOutput = update_checker_run_migrations();

    update_checker_set_setting($db, 'update_check_last_checked_at', date('Y-m-d H:i:s'));

    $message = "Updated to v{$latestVersion}. Backup saved to " . UPDATE_CHECKER_BACKUP_DIR . '/' . basename($backupDir) . ".";

    update_checker_set_setting($db, 'update_check_last_apply_report', $message . "\n\n" . $migrationOutput);

    return [
        'ok'                => true,
        'message'           => $message,
        'backup'            => $backupDir,
        'files_changed'     => $filesChanged,
        'migration_output'  => $migrationOutput,
    ];
}

add_filter('permissions', function ($permissions) {
    $permissions[] = 'manage_updates';
    return $permissions;
});

add_filter('basic-admin_before_admin_links', function ($links) {
    if (user_can('manage_updates')) {
        $vars = get_value();

        $links[] = (object) [
            'title'  => 'Updates',
            'link'   => ROOT . '/' . $vars['admin_route'] . '/' . $vars['plugin_route'],
            'icon'   => 'cloud-arrow-down',
            'parent' => 0,
        ];
    }
    return $links;
});

// Priority 0 so this renders above basic-admin's own stat cards/recent
// activity, which register on the same hook at the default priority.
add_action('basic-admin_main_content', function () {
    if (page() !== 'admin' || URL(1) !== '') return;

    $db = new \Core\Database;
    $wasChecked = (string) setting('update_check_last_checked_at', '');
    update_checker_maybe_check($db);

    if (!setting('update_check_enabled', true)) return;

    $latest = (string) setting('update_check_latest_version', '');
    $current = app_version();
    $updateAvailable = $latest !== '' && version_compare($latest, $current, '>');

    // Auto-apply only fires right after a check that *just* ran (i.e. this
    // request is the one that found the new version), not on every single
    // dashboard load while auto-apply is on - avoids re-attempting a
    // download+apply on every page view if something about the update
    // itself is failing.
    $justChecked = (string) setting('update_check_last_checked_at', '') !== $wasChecked;

    if ($updateAvailable && $justChecked && setting('update_check_auto_apply', false)) {
        update_checker_apply_update($db);
        $latest = (string) setting('update_check_latest_version', '');
        $current = app_version();
        $updateAvailable = $latest !== '' && version_compare($latest, $current, '>');
    }

    if ($updateAvailable) {
        $url = (string) setting('update_check_latest_url', '');
        ?>
        <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <i class="bi bi-cloud-arrow-down"></i>
                <strong>ThunderPHP v<?= esc($latest) ?> is available</strong> — you're running v<?= esc($current) ?>.
            </div>
            <?php if ($url !== ''): ?>
                <a href="<?= esc($url) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-primary">View Release</a>
            <?php endif; ?>
        </div>
        <?php
    }
}, 0);

add_action('controller', function () {
    $req = new \Core\Request;
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (URL(1) === $plugin_route && $req->posted()) {
        require plugin_path('controllers/save_controller.php');
    }
});

add_action('basic-admin_main_content', function () {
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (page() !== $admin_route || URL(1) !== $plugin_route) return;

    require plugin_path('views/admin/view.php');
});
