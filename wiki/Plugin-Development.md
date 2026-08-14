# Plugin Development

Every feature in ThunderPHP — including the admin panel — is a plugin under `plugins/<name>/`. A plugin folder typically has:

```
plugins/<name>/
  plugin.php        # hooks, routes, table checks
  config.json        # {"active": true/false}
  controllers/
  views/
  models/
  migrations/
```

Scaffold a new one with `php thunder make:plugin <name>`.

## The `active` flag

`config.json`'s `"active"` key controls whether the plugin loads at all. This is what the install wizard's Minimal profile uses to leave content-type plugins switched off without deleting their files — same idea as Drupal leaving unused modules on disk, just disabled.

## The hook system

Plugins register behavior with `add_action($hook, $callback)` and `add_filter($hook, $callback)`, fired elsewhere with `do_action()`/`do_filter()`. Common hooks you'll see in existing plugins:

- `add_action('controller', function () { ... })` — runs on **POST** requests. Every plugin's controller-dispatch switches on `page()` inside a `$req->posted()` check — there's no equivalent "pre-view" hook for GET requests anywhere in this codebase. If a GET-rendered page needs a database lookup (e.g. looking up a page by slug, or validating a token from a query string), do it directly inside the `add_action('view', ...)` closure before `require`-ing the view file — see `basic-pages/plugin.php` or `basic-auth/plugin.php`'s `reset_page` case for examples.
- `add_action('view', function () { ... })` — renders the current page's view file.
- `add_filter('permissions', ...)` / `user_can('some_permission')` — the permission system.

## Two gotchas that have caused real bugs

### 1. `createTable()` wipes staged `addData()` rows

`Migration::createTable()` (in `app/models/Migration.php`) calls a private `clearKeys()` at the end that resets the migration's staged columns *and* staged data. If you call `addData()` **before** `createTable()`, your seed rows are silently discarded — `insert()` runs against empty staged data and does nothing.

**Wrong:**
```php
$this->addData(['role' => 'admin', 'disabled' => 0]);
$this->createTable('user_roles'); // wipes the staged row above
$this->insert('user_roles');       // inserts nothing
```

**Right** (createTable first, then addData + insert):
```php
$this->createTable('user_roles');
$this->addData(['role' => 'admin', 'disabled' => 0]);
$this->insert('user_roles');
```

This exact ordering bug shipped in the `user-roles` plugin's own migration and meant fresh installs got an empty `user_roles` table with no `admin` role to assign — found and fixed while building the install wizard.

`do:migrate` tracks what's already run in a `migrations_log` table (self-bootstrapped by `app/thunder/thunder.php`, not tied to any one plugin) keyed on plugin + migration filename, so re-running `php thunder do:migrate <plugin>` — or `do:migrate all` — is safe and skips anything already applied instead of re-running `up()` and duplicating seed rows. Rolling a migration back (`do:rollback`) removes its log entry so it can be cleanly re-applied.

### 2. Classes resolve relative to the *calling* plugin, not their own namespace

The autoloader (`app/core/init.php`) resolves an unmatched class in two steps: first it checks `app/models/{ShortClassName}.php`, and only if that's not found does it fall back to the **calling file's own plugin directory** (via `debug_backtrace()`) — not the class's namespace.

Practical effect: a model class that lives inside `plugins/plugin-a/models/Thing.php` can be `new`'d from other files *inside* `plugin-a`, but **cannot** be `new`'d from `plugin-b`'s code, even with a `use` statement and the right namespace — the autoloader will look in `plugin-b`'s own `models/` folder instead and fail.

If a class genuinely needs to be shared across plugins, put it in `app/models/` (that's where framework-wide classes like `Session`, `Request`, `Mailer`, and `SmtpClient` live, despite being namespaced `Core`) rather than inside any one plugin's folder.
