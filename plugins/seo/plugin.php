<?php

/**
 * Plugin Name: SEO Plugin
 * Description: Per-page meta tags/Open Graph output, an auto-regenerated sitemap.xml and robots.txt, and an admin screen for site-wide SEO defaults.
 * Version: 1.0
 * Author: Afro Lion
 */

set_value([
    'admin_route'  => 'admin',
    'plugin_route' => 'seo',
    'table'        => ['settings_table' => 'settings'],
]);

$db = new \Core\Database;
$table = get_value()['table'];

if (!$db->tableExists($table)) {
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",", $db->missing_tables));
    die;
}

/**
 * Writes a value directly via \Core\Database rather than instantiating the
 * settings plugin's own Settings model - that class lives in a different
 * plugin's models/ folder, and the app's autoloader resolves an unqualified
 * plugin model class relative to whichever *file* called `new`, not the
 * class's own plugin. Called from here, `new \Setting\Settings` would
 * resolve to plugins/seo/models/Settings.php (which doesn't exist) instead
 * of plugins/settings/models/Settings.php, and fatal. \Core\Database has no
 * such coupling (it's required directly in app/core/init.php, not
 * autoloaded), so it's safe to use from any plugin.
 */
function seo_set_setting(\Core\Database $db, string $key, string $value): bool {
    $row = $db->fetch("SELECT id FROM settings WHERE `key` = ?", [$key]);

    if ($row) {
        $db->query("UPDATE settings SET `value` = ?, updated_at = NOW() WHERE id = ?", [$value, $row->id]);
        return true;
    }

    // Falls back to a plain insert if this plugin's migration ran before the
    // settings plugin's own migration created the `settings` table in the
    // first place (do:migrate all runs plugin folders alphabetically, not
    // in dependency order, so "seo" runs before "settings" on a fresh
    // install) - this keeps the first real save working either way.
    $db->query("INSERT INTO settings (`key`, `value`, `type`, `environment`) VALUES (?, ?, 'string', 'production')", [$key, $value]);
    return true;
}

/**
 * Writes sitemap.xml as a real file at the site root. Real files are served
 * directly by .htaccess (bypassing index.php entirely), which sidesteps
 * split_url() stripping the '.' out of any dotted filename routed through
 * the app - a real file on disk is the only reliable way to serve this.
 */
function seo_build_sitemap(\Core\Database $db): bool {
    $siteUrl = rtrim((string) setting('site_url', ROOT), '/');
    $urls = [['loc' => $siteUrl . '/', 'lastmod' => date('Y-m-d')]];

    if (setting('seo_include_pages', true) && $db->tableExists('pages')) {
        foreach ($db->query("SELECT slug, date_updated, date_created FROM pages WHERE disabled = 0 AND date_deleted IS NULL") as $row) {
            $urls[] = [
                'loc'     => $siteUrl . '/' . $row->slug,
                'lastmod' => date('Y-m-d', strtotime($row->date_updated ?: ($row->date_created ?: 'now'))),
            ];
        }
    }

    if (setting('seo_include_posts', true) && $db->tableExists('posts')) {
        foreach ($db->query("SELECT slug, date_updated, date_created FROM posts WHERE disabled = 0 AND date_deleted IS NULL") as $row) {
            $urls[] = [
                'loc'     => $siteUrl . '/' . $row->slug,
                'lastmod' => date('Y-m-d', strtotime($row->date_updated ?: ($row->date_created ?: 'now'))),
            ];
        }
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($urls as $u) {
        $xml .= "  <url>\n    <loc>" . esc($u['loc']) . "</loc>\n    <lastmod>" . $u['lastmod'] . "</lastmod>\n  </url>\n";
    }

    $xml .= '</urlset>';

    $ok = file_put_contents(ROOTPATH . 'sitemap.xml', $xml) !== false;

    if ($ok) {
        seo_set_setting($db, 'seo_sitemap_generated_at', date('Y-m-d H:i:s'));
    }

    return $ok;
}

function seo_build_robots(): bool {
    $siteUrl = rtrim((string) setting('site_url', ROOT), '/');
    $custom = trim((string) setting('seo_robots_txt', ''));

    if ($custom !== '') {
        $content = rtrim($custom) . "\n";
    } else {
        $content = "User-agent: *\nDisallow: /admin\nDisallow: /login\nDisallow: /logout\nDisallow: /uploads/\n\nSitemap: {$siteUrl}/sitemap.xml\n";
    }

    return file_put_contents(ROOTPATH . 'robots.txt', $content) !== false;
}

// Self-heal: guarantee both files exist from the very first request, even
// before anyone visits the admin screen.
if (!file_exists(ROOTPATH . 'robots.txt')) {
    seo_build_robots();
}
if (!file_exists(ROOTPATH . 'sitemap.xml')) {
    seo_build_sitemap($db);
}

add_filter('permissions', function ($permissions) {
    $permissions[] = 'manage_seo';
    return $permissions;
});

// Resolve the current page/post's title+meta BEFORE header-footer renders
// <head> (priority 0 runs ahead of header-footer's before_view priority 1).
add_action('before_view', function () {
    if (page() === 'admin') return;

    $db = new \Core\Database;
    $meta = null;

    if ($db->tableExists('pages')) {
        // Pages::$allowedColumns lists an 'image' column, but it was never
        // actually added to the pages table by that plugin's migration -
        // selecting it here throws (caught by Database::query(), but it
        // silently turns every page's SEO meta into a no-op). Pages simply
        // have no per-page image today, so this only ever falls through to
        // the site-wide default OG image below.
        $meta = $db->fetch("SELECT title, description, keywords FROM pages WHERE slug = ? AND disabled = 0 AND date_deleted IS NULL", [page()]);
    }
    if (!$meta && $db->tableExists('posts')) {
        $meta = $db->fetch("SELECT title, description, keywords FROM posts WHERE slug = ? AND disabled = 0 AND date_deleted IS NULL", [page()]);
    }

    set_value('current_meta', $meta ?: null);
}, 0);

add_filter('page_title', function ($default) {
    $meta = get_value('current_meta');
    if (!empty($meta->title)) {
        $siteName = (string) setting('site_name', $default);
        return $meta->title . ($siteName ? ' | ' . $siteName : '');
    }
    return $default;
}, 0);

add_action('before_head_close', function () {
    if (page() === 'admin') return;

    $meta = get_value('current_meta');
    $siteUrl = rtrim((string) setting('site_url', ''), '/');
    $currentPath = implode('/', array_filter(URL()));
    $currentUrl = $siteUrl !== '' ? $siteUrl . '/' . $currentPath : '';

    $description = trim((string) (!empty($meta->description) ? $meta->description : setting('seo_default_description', setting('site_description', ''))));
    $keywords = trim((string) (!empty($meta->keywords) ? $meta->keywords : setting('seo_default_keywords', '')));

    // Pages/posts have no per-item image column today, so the OG image is
    // always the site-wide default configured on the SEO settings screen.
    $ogImage = '';
    if (setting('seo_default_og_image') && file_exists((string) setting('seo_default_og_image'))) {
        $ogImage = get_image((string) setting('seo_default_og_image'));
    }

    $title = do_filter('page_title', APP_NAME);

    if ($description !== '') {
        echo '<meta name="description" content="' . esc($description) . '">' . "\n";
    }
    if ($keywords !== '') {
        echo '<meta name="keywords" content="' . esc($keywords) . '">' . "\n";
    }
    if ($currentUrl !== '') {
        echo '<link rel="canonical" href="' . esc($currentUrl) . '">' . "\n";
    }

    echo '<meta property="og:title" content="' . esc($title) . '">' . "\n";
    if ($description !== '') {
        echo '<meta property="og:description" content="' . esc($description) . '">' . "\n";
    }
    echo '<meta property="og:type" content="website">' . "\n";
    if ($currentUrl !== '') {
        echo '<meta property="og:url" content="' . esc($currentUrl) . '">' . "\n";
    }
    if ($ogImage !== '') {
        echo '<meta property="og:image" content="' . esc($ogImage) . '">' . "\n";
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    } else {
        echo '<meta name="twitter:card" content="summary">' . "\n";
    }
}, 0);

// Auto-regenerate the sitemap whenever a page or post is written to, so it
// never goes stale without anyone having to remember to click "Regenerate".
add_filter('after_query', function ($data) {
    if (empty($data['query']) || !is_object($data['query']) || empty($data['query']->queryString)) {
        return $data;
    }

    $sql = $data['query']->queryString;

    if (preg_match('/^\s*(INSERT INTO|UPDATE|DELETE FROM)\s+`?(pages|posts)`?\b/i', $sql)) {
        seo_build_sitemap(new \Core\Database);
    }

    return $data;
});

add_filter('basic-admin_before_admin_links', function ($links) {
    if (user_can('manage_seo')) {
        $vars = get_value();

        $links[] = (object) [
            'title'  => 'SEO',
            'link'   => ROOT . '/' . $vars['admin_route'] . '/' . $vars['plugin_route'],
            'icon'   => 'graph-up-arrow',
            'parent' => 0,
        ];
    }
    return $links;
});

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
