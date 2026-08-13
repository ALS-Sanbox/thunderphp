# ThunderPHP v1.0.0-rc1

The first tagged release of ThunderPHP — a plugin-based PHP MVC framework where every feature, including the admin panel itself, is a plugin. This is a **release candidate**: the framework is feature-complete for a 1.0, but hasn't seen wide real-world use yet, so bug reports are very welcome.

## Highlights

### Install wizard
A Drupal-style interactive installer (`/install.php`) replaces manually copying `config-sample.php` and hand-running CLI migrations. Walks through a Standard/Minimal install profile, a requirements check, database setup, migrations (with a live log), and creation of the site's one real admin account. There's no default/shared admin account shipped with ThunderPHP anymore — the installer is how the only admin account gets created, and it refuses to run again once a site is set up.

### Password reset
"Forgot Password?" is a real, working flow now: single-use, hashed, time-limited reset tokens; a hand-rolled SMTP client with a native-`mail()` fallback (no external dependencies); and SMTP settings configurable from the admin panel for real deliverability. The flow never reveals whether a submitted email actually has an account.

### User roles & permissions
A full role/permission system (`user-roles` plugin) with a searchable, grouped permission picker (select-all, clear-all, per-group toggles) in the admin UI.

### Content editing
A GrapesJS-based advanced page/post editor with a 49-block library (nav, hero, footer, features, CTA, testimonials, pricing, team, FAQ, contact) across 10 categories, each with its own icon. Redesigned Pages/Post admin list screens (stat cards, search/status filtering, bulk actions, duplicate). A shared Images plugin provides a media library usable from both Summernote and GrapesJS.

### Admin panel
A real dashboard (stat cards, recent activity, quick actions) instead of a blank landing page, plus light-theme contrast fixes and a manageable default Home menu link.

### Versioning & licensing
A single `VERSION` file (read via `app_version()`, exposed through `php thunder version` and the admin footer) as the foundation for version-aware tooling later. Licensed under MIT.

## Notable fixes since the previous unreleased state

- Fixed a migration bug where the `user-roles` plugin's own `Userroles` migration staged its `admin` role seed row *before* calling `createTable()` — which silently wipes staged data — so fresh installs got an empty `user_roles` table with no role to assign anyone. (See the wiki's [Plugin Development](wiki/Plugin-Development.md) page for the general gotcha.)
- Fixed a bug in `Settings::isValidValue()` that would have rejected the settings form entirely once optional blank fields (the new SMTP settings) existed.
- Fixed duplicated/conflicting role & permission models that two different plugins each independently defined against the same three database tables.
- Fixed several admin-panel bugs: user role assignment (empty selector on add, wrong row updated on edit), a fatal error saving roles, a pre-existing image-upload bug on user creation, a menu-editing crash, and the GrapesJS editor leaking saved content between pages.

## Upgrading from an unreleased/manually-installed copy

There's no automated migration path from a pre-1.0 manual install to this tag. If you have an existing ThunderPHP site, review the commit history for schema changes before pulling this in, particularly around the `user_roles`/`user_roles_map`/`permission_roles` tables (see the "Notable fixes" section above) and the removal of the default seeded admin account.

## Known limitations

- No i18n/translation system — the install wizard intentionally skips a language-selection step because there'd be nothing for it to do.
- SMTP credentials are stored in plaintext in the generic settings table, matching this app's existing security posture (nothing is encrypted at rest anywhere today).
- No automatic update-checking yet — `VERSION` is a foundation for that, not the feature itself.
