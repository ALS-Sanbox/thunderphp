# Site Overrides

A plugin folder under `plugins/<name>/` holds two things that are easy to conflate: the plugin's *code*, and — in practice, on real sites — whatever a site owner customizes directly by editing that plugin's own view/asset files. There's no built-in separation between the two, which means a plugin update that overwrites its own files can silently destroy site-specific customization along with it.

`site-overrides/` fixes that by giving customization a home outside `plugins/` entirely.

## Basic usage: overriding a view or asset

Mirror the plugin's folder structure under `site-overrides/<plugin-id>/`. Any file placed there is used instead of the plugin's own copy of that same relative path.

```
plugins/home-page/views/view.php          <- plugin's own default
site-overrides/home-page/views/view.php   <- if this exists, it wins
```

This works because `plugin_path()` and `plugin_http_path()` — the two functions every plugin already calls to find its own views/assets/controllers (`app/core/functions.php`) — check `site-overrides/<plugin-id>/<path>` first, and only fall back to the plugin's own file when no override exists. No plugin code needs to change to support this; it's automatic for every plugin, including ones written before this existed.

It applies to any path a plugin resolves this way, not just views — `assets/css/style.css`, `controllers/add_controller.php`, anything.

## Why this matters for updates

The Update Checker plugin's apply step replaces everything under `plugins/` with what's in the new release. That's only safe because site customization no longer has to live inside `plugins/` — it lives in `site-overrides/`, which the updater never touches.

## Full logic overrides

Most customization is presentation — a template, a stylesheet — and a plain file override handles it. Occasionally the thing being customized is *behavior*: a homepage that queries and renders "latest posts," say, where that logic lives inside `plugin.php`'s hook registration, not in a file you can just swap out.

A plugin can opt a specific hook into being fully replaceable by checking a filter as the very first thing it does:

```php
// plugins/home-page/plugin.php
add_action('view', function () {
    if (do_filter(plugin_id().'_override_active', false)) {
        return; // a site override owns this hook now
    }

    $siteName = setting('site_name');
    $siteDesc = setting('site_description');
    require plugin_path('views/view.php');
});
```

A site override then claims the hook and provides its own implementation:

```php
// site-overrides/home-page/override.php (loaded however your site loads overrides —
// e.g. required from a small bootstrap plugin, or from config.php)
add_filter('home-page_override_active', fn() => true);

add_action('view', function () {
    if (page() !== 'home') return;

    $posts = new \BasicPosts\Posts;
    $latest = $posts->query("SELECT * FROM posts WHERE pop = 1 AND disabled = 0 ORDER BY date_created DESC LIMIT 3");
    // ... fully custom homepage rendering ...
});
```

Don't add this guard to every hook in every plugin pre-emptively — only to a hook you actually have a concrete need to replace outright. It's documented here so the pattern is ready when that need shows up, not as something every plugin author should reach for by default.

## What isn't covered

`site-overrides/` is for customizing a plugin's presentation or behavior. It's not a place for a whole separate plugin (put those in `plugins/` as normal — the updater only ever touches plugins it ships, never one it doesn't recognize) and it's not a migration/database mechanism — a plugin's own `migrations/` folder is still the only place table structure comes from.
