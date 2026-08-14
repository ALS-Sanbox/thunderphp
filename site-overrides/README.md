# Site Overrides

Put site-specific customizations here instead of editing a plugin's own files directly. This is what makes it safe for the Update Checker plugin to replace every file under `plugins/` during an update without destroying anything you've customized.

## How it works

Mirror the plugin's own folder structure under `site-overrides/{plugin-id}/`. Any file that exists here is used instead of the plugin's own copy — checked automatically by `plugin_path()` and `plugin_http_path()`, the same functions every plugin already uses to find its own views and assets, so no plugin code needs to change to support this.

Example: to customize the homepage's content, instead of editing `plugins/home-page/views/view.php` directly, create:

```
site-overrides/home-page/views/view.php
```

That file is used instead — `plugins/home-page/views/view.php` is left completely alone, so a future update to the `home-page` plugin can safely overwrite it.

The same applies to assets (`site-overrides/home-page/assets/css/style.css` overrides `plugins/home-page/assets/css/style.css`), and in fact to any file a plugin resolves through `plugin_path()`/`plugin_http_path()` — controllers included, not just views.

## Full logic overrides

Template overrides cover most customization. Occasionally a plugin's actual *behavior* is what's customized (e.g. a homepage that queries and displays "latest posts" — logic that lives in `plugin.php`, not in a view file that can just be swapped). A plugin can opt into supporting a full override of a specific hook by checking a filter at the very top of it:

```php
add_action('view', function () {
    if (do_filter(plugin_id().'_override_active', false)) {
        return; // a site override has taken over this hook entirely
    }

    // ... the plugin's normal behavior ...
});
```

A site override then registers its own replacement and flips that filter on:

```php
add_filter('home-page_override_active', fn() => true);

add_action('view', function () {
    if (page() !== 'home') return;
    // ... fully custom homepage logic ...
});
```

This isn't retrofitted into every plugin — only worth adding to a hook you actually need to replace outright, not just re-skin. See the wiki's [Site Overrides](../wiki/Site-Overrides.md) page for more detail.
