# ThunderPHP v1.0.0

The first stable release of ThunderPHP — no longer a release candidate. Everything in v1.0.0-rc1 plus a large batch of new plugins, a real security pass (including a critical fix), a working update system, a non-interactive installer, and a HestiaCP Quick Install integration verified against a real, live HestiaCP panel.

## Security fixes

If you're running a pre-1.0.0 copy anywhere reachable, upgrade before anything else:

- **Critical: file-upload remote code execution.** Upload handling validated a file's *content* against an image allowlist but derived the *saved extension* from the attacker-controlled client filename. An image/PHP polyglot uploaded as `shell.php` passed content validation and was written to `uploads/shell.php`, executable by the server — reachable by any logged-in user via their own avatar upload, and by anyone with page/post/menu/settings edit permissions via icon/logo uploads. Fixed by deriving the extension solely from the validated MIME type, never the client filename, plus `uploads/.htaccess` denying execution of `.php`/`.phtml`/`.phar` there as defense-in-depth.
- Stored XSS in site name/description/URL/admin email, exploitable by anyone with the separately-grantable `edit_settings` permission, executing for every site visitor.
- Path traversal in image handling via an unsanitized editor-supplied filename.
- Every database query — including plaintext SMTP passwords and password hashes — was being written to the error log.
- Session fixation (no `session_regenerate_id()` on login), missing `httponly`/`samesite` cookie flags, and no CSP/`X-Frame-Options`/`X-Content-Type-Options`/`Referrer-Policy` headers anywhere.
- Login lockout lived in the PHP session, so clearing cookies reset it instantly. Now keyed by IP+email in a real `login_attempts` table.
- The entire admin dashboard was reachable by anyone, logged in or not — the route was restricted, but nothing inside it checked the `view_admin_page` permission it registers.
- CSRF token comparison used `!==` instead of `hash_equals()`; logout was an unprotected `GET` link.

## Highlights

### Six new plugins
**SEO** (per-page/post title, meta description, Open Graph tags, auto-generated sitemap/robots.txt), **Search** (across published pages and posts), **Contact Form** (honeypot + rate-limited, with an admin inbox), **Redirects** (301/302 management plus 404 logging), **Activity Log** (a real audit trail of who added/edited/deleted what), and **Profiles** (self-service `/profile/{id}` pages, bio/website fields).

### Google Sign-In
A real OAuth flow, configurable per-site from the admin panel, alongside a fix to a signup bug where password/confirmPassword were swapped and validation failures failed silently.

### Site customization without editing plugin files
The **site-overrides system**: any plugin's view, asset, or controller can be overridden from `site-overrides/{plugin-id}/` without touching the plugin's own files. This is what makes wholesale plugin updates safe.

### Real update management
Automatic daily checks against GitHub releases, plus actual **download, backup, and apply** — file and database backups taken automatically before anything is touched, manual or automatic apply mode, restricted to framework-owned paths (never `config.php`, `uploads/`, or `site-overrides/`). Backed by a new `migrations_log` table that makes `do:migrate` idempotent — re-running it now skips what's already applied instead of duplicating seed data, a bug this session's own work had hit more than once.

### Non-interactive install: `php thunder do:install`
Installs a fresh site — `config.php`, migrations, admin account — from one CLI command instead of the browser wizard, built specifically so ThunderPHP can be driven by an automated installer. Also fixed a real, previously-invisible bug this surfaced: plugins migrate in alphabetical folder order, so a plugin seeding rows into the shared `settings` table could run before `settings`' own migration created that table on a truly fresh database — silently losing those seed rows for good, even on retry, since a partially-failed migration still gets logged as applied. `settings` now always migrates first.

### HestiaCP Quick Install
A `ThunderPHPSetup.php` installer (`hestiacp-quick-install/`), meant for contribution to HestiaCP's own repo, giving ThunderPHP the same one-click "Quick Install" experience as WordPress or Drupal. Verified against a real, live HestiaCP panel — not just read from source — which surfaced (and fixed) a HestiaCP-side quirk: `v-run-cli-cmd` silently word-splits and truncates any argument containing a space, a real lockout risk for an admin password that this installer now checks for and rejects outright before ever reaching that layer.

### Admin panel and content editing
A GrapesJS editor for the site header/footer (mirroring the existing page/post editor), a real hover dropdown for the User Menu, button-based menu item permission selection (replacing free text), a meaningfully more informative dashboard Site Info card (version, URL, debug mode — not just "App: ThunderPHP"), and a general styling/labeling pass unifying every admin screen onto the same conventions.

### Developer tooling
Composer (additive — the app's own plugin autoloader is unchanged), a 35-test suite covering the riskiest paths (auth, CSRF, migrations, password reset), and CI running that suite against a real MySQL database on every push.

### Branch model
Adopted HestiaCP's own three-branch convention — `main` (active development), `beta` (next version, testing), `release` (stable, production; what Quick Install and any packaged downloads ship from) — replacing the previous `main`/`nightly` split. See [wiki/Branches.md](wiki/Branches.md).

## Notable fixes

- Duplicate `Siteusers` model classes (two independent copies of the same rules) consolidated into one.
- A real permission-name typo (`view_post` vs. the actually-granted `view_posts`) that hid the Posts nav link for any role scoped exactly that way.
- User Menu dropdown disappearing on mouse-down and low-contrast links.
- `app/core/init.php`'s autoloader `die()`d on any unresolved class instead of returning `false`, which would have hard-killed any request touching a Composer-managed class.

## Known limitations

- No i18n/translation system.
- Settings (including SMTP credentials) are stored in plaintext, matching this app's existing security posture — nothing is encrypted at rest anywhere today.
- "Automatic" update apply means triggered on an admin's next dashboard visit after a check finds something new, not a true background/cron schedule — this app has no task scheduler. Real cron access can run `php thunder do:migrate all` directly instead.
- The HestiaCP Quick Install integration lives in this repo, not HestiaCP's, until it's submitted upstream — see `hestiacp-quick-install/README.md` for status.

## Upgrading from v1.0.0-rc1

Pull `release`, then run `php thunder do:migrate all` against your existing database — every migration since rc1 is additive and idempotent (see the `migrations_log` note above), so this is safe to run even if some of it already applied. If you're running the update-checker plugin, its own "Apply Update Now" button does this for you, backups included.
