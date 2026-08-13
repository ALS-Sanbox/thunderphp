# Installation

## Requirements

The install wizard checks these automatically and won't let you continue until they pass:

- PHP 8.0 or newer
- `pdo_mysql`, `mbstring`, `json`, and `fileinfo` PHP extensions
- A writable application root and `uploads/` directory
- A MySQL/MariaDB database and user that already exist (the wizard creates the *tables*, not the database itself)

## Steps

1. Point your webroot at the repository. If `config.php` doesn't exist yet, visiting any page automatically redirects to `/install.php`.
2. **Profile** — choose:
   - **Standard**: every shipped plugin active (pages, posts, categories, images, menus, the works). This is what most sites want.
   - **Minimal**: just the admin shell — `404`, `basic-admin`, `basic-auth`, `header-footer`, `settings`, `user-roles`, `users-manager`. No content-type plugins. Good for a bare starting point you'll build your own content plugins on top of.
3. **Requirements** — automatic pass/fail checklist.
4. **Database** — host, database name, username, password. The wizard tests the connection before letting you continue.
5. **Installing** — writes `config.php` and runs migrations for your chosen profile. You'll see a live log of each migration as it runs.
6. **Site & admin account** — site name, contact email, and the one real admin account for this install (first/last name, email, password). There's no default/shared admin account shipped with ThunderPHP — this step is how the *only* admin account gets created.
7. **Done** — you're redirected to `/login`.

Re-visiting `/install.php` after a site is already set up redirects straight to the "done" screen instead of re-running — it won't let you wipe or re-migrate a live site by accident.

## Switching a Minimal install to Standard later

The wizard doesn't do this for you post-install. To turn a plugin back on by hand:

1. Set `"active": true` in that plugin's `plugins/<name>/config.json`.
2. Run its migrations: `php thunder do:migrate <plugin-name>`.

See [Plugin Development](Plugin-Development.md) for how migrations work.
