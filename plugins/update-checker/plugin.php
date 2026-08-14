<?php

/**
 * Plugin Name: Update Checker Plugin
 * Description: Checks GitHub for newer ThunderPHP releases (once a day, cached) and shows a dashboard notice when one is available. Never downloads or applies anything automatically.
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
    if (!is_array($data) || empty($data['tag_name'])) return null;

    return [
        'version' => ltrim((string) $data['tag_name'], 'vV'),
        'url'     => (string) ($data['html_url'] ?? ('https://github.com/' . UPDATE_CHECKER_REPO . '/releases')),
    ];
}

function update_checker_run_check(\Core\Database $db): void {
    $latest = update_checker_fetch_latest();

    update_checker_set_setting($db, 'update_check_last_checked_at', date('Y-m-d H:i:s'));

    if ($latest) {
        update_checker_set_setting($db, 'update_check_latest_version', $latest['version']);
        update_checker_set_setting($db, 'update_check_latest_url', $latest['url']);
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
    update_checker_maybe_check($db);

    if (!setting('update_check_enabled', true)) return;

    $latest = (string) setting('update_check_latest_version', '');
    $current = app_version();

    if ($latest !== '' && version_compare($latest, $current, '>')) {
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
