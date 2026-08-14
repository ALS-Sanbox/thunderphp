# ThunderPHP as a HestiaCP Quick Install App

Lets a HestiaCP admin install ThunderPHP onto a domain with the same
one-click "Quick Install" flow used for WordPress, Drupal, Laravel, etc. -
pick ThunderPHP, fill in a short form, HestiaCP does the rest.

## Why this lives here and not in HestiaCP's repo yet

Quick Install App installer classes (`Setup.php` subclasses under
`web/src/app/WebApp/Installers/`) are part of HestiaCP's own codebase, not
ThunderPHP's - they only take effect once merged into
[hestiacp/hestiacp](https://github.com/hestiacp/hestiacp). This folder is
the contribution as it would be submitted, kept here until that PR is
opened.

## What's here

- **`ThunderPHP/ThunderPHPSetup.php`** - the installer class itself.
- **`ThunderPHP/thunderphp-thumb.png`** - the app-grid icon HestiaCP shows
  next to every installable app.

## How it works

HestiaCP's `BaseSetup::install()` (called first, via `parent::install()`)
downloads and extracts the `resources.archive.src` URL below into the
domain's `public_html` and provisions a MySQL database (`"database" =>
true` in `$config`). `ThunderPHPSetup::install()` then runs the one
command ThunderPHP itself needs to finish setting up -
[`php thunder do:install`](../app/thunder/thunder.php) - via HestiaCP's
`v-run-cli-cmd`, passing the database credentials HestiaCP just
provisioned and the admin account details from the install form. That
command writes `config.php`, runs every plugin's migrations, and creates
the admin account - see `Thunder::doInstall()` for what it actually does
and `wiki/Plugin-Development.md` for the migration system it drives.

`v-run-cli-cmd` doesn't `cd` into the app's directory before running
anything, so every path passed to it has to be absolute - `getDocRoot()`
gives the installer the domain's real `public_html` path, and ThunderPHP's
own `thunder` script already resolves its own location via `__DIR__`
rather than assuming a particular working directory, so no extra `chdir()`
is needed here (unlike, e.g., `GravSetup.php`, which does need one).

## Source archive

`resources.archive.src` currently points at `nightly`'s tarball
(`archive/refs/heads/nightly.tar.gz`), **not** `main` - `do:install` (what
this class runs) only exists on `nightly` right now. Switch this to `main`
(or a tagged release, `archive/refs/tags/vX.Y.Z.tar.gz`) once that work is
promoted to the stable branch; installing from `main` today would extract
the old `app/thunder/init.php` that unconditionally requires `config.php`,
which fails outright for exactly the reason `do:install` exists to fix.
Confirmed live, not theoretical - see the verification section below.

## The `v-run-cli-cmd` space-truncation issue

`v-run-cli-cmd` (`/usr/local/hestia/bin/v-run-cli-cmd`) rebuilds every
argument into one unquoted bash string right before running it
(`runuser -u "$user" -- $clicmd $cmdArgs`, `$cmdArgs` never quoted) - so
any option value containing a space gets silently word-split there and
truncated at the first space. No error is raised anywhere in that path.

Confirmed live: a `site_name` of `"ThunderPHP Quick Install Test"` arrived
at `do:install` as just `ThunderPHP` - the rest didn't get dropped, it
became stray unflagged tokens that `do:install`'s own argument parser
silently ignores. Harmless for a cosmetic field like that (which is why
`site_name` isn't collected in the install form at all - `do:install`
just defaults it, rename later from the dashboard's Settings screen), but
a real lockout risk for the admin password: a truncated password can
still pass `do:install`'s own length/complexity check and get silently
stored instead of what was actually typed, with nothing anywhere to
indicate that happened. `ThunderPHPSetup::install()` rejects any admin
password containing a space up front, before the archive download,
database creation, or CLI call - a real HestiaCP limitation, not a
ThunderPHP one, but not something to route around by, say, stripping the
spaces instead: that would silently store a different password than what
was typed, which is the exact failure mode being avoided here.

## Verification

Tested end-to-end against the real HestiaCP install on this box, not just
read from source - deployed `ThunderPHPSetup.php` to
`/usr/local/hestia/web/src/app/WebApp/Installers/ThunderPHP/` and drove
the actual `AppWizard` + `ThunderPHPSetup` code path (the same one
`/add/webapp/index.php` calls) against `ai.tylilaford.com` after backing
up and clearing its `public_html`. This is what caught both issues
above - neither was visible from reading HestiaCP's source alone. Result:
real archive download, real database provisioning, `do:install` run via
the real `v-run-cli-cmd`, and a real HTTP login into the resulting
dashboard, all successful. The test harness itself was scratch-only and
was not committed.

## Remaining open question

- **`--site-url` assumes `https://`.** ThunderPHP's own install wizard
  detects this from the live request; a CLI install has no request to
  read it from. This assumes HestiaCP has already provisioned SSL for the
  domain by the time Quick Install runs (true for ai.tylilaford.com, and
  for a typical modern HestiaCP setup with Let's Encrypt auto-issued at
  domain creation) - if that's not reliably true on every box this ships
  to, this needs to fall back to `http://` instead.

PHP extensions aren't an open question anymore - `composer.json` requires
`ext-gd`, `ext-pdo_mysql`, `ext-mbstring`, `ext-json`, `ext-fileinfo`, and
`ext-zip`; confirmed all six loaded under `php8.2` on this box (`php8.2 -m`).
Quick Install still doesn't check for these itself before running, so a
box missing one would fail during `do:install` rather than with a clear
upfront message - worth a preflight check in `ThunderPHPSetup::install()`
if this turns out to matter on boxes less consistently provisioned than
this one.
