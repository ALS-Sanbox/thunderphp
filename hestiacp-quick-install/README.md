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

`resources.archive.src` points at `main`'s tarball
(`archive/refs/heads/main.tar.gz`) rather than a specific release tag, so
installs always get the latest stable commit without this file needing an
update on every release. Point it at a tagged release instead
(`archive/refs/tags/vX.Y.Z.tar.gz`) if that's ever preferred over
following `main` directly.

## Open questions before submitting

- **`--site-url` assumes `https://`.** ThunderPHP's own install wizard
  detects this from the live request; a CLI install has no request to
  read it from. This assumes HestiaCP has already provisioned SSL for the
  domain by the time Quick Install runs (true for a typical modern
  HestiaCP setup with Let's Encrypt auto-issued at domain creation) - if
  that's not reliably true, this needs to fall back to `http://` instead.
- **Not yet tested against a real HestiaCP Quick Install run.** Everything
  above was verified by reading HestiaCP's actual installed source on
  ai.tylilaford.com's box (`BaseSetup.php`, `HestiaApp.php`,
  `v-run-cli-cmd`, `v-extract-fs-archive`, and the real `DrupalSetup.php`/
  `LaravelSetup.php`/`GravSetup.php` classes it ships), and `do:install`
  itself was verified end-to-end in scratch - but the two have not yet
  been exercised together through HestiaCP's actual UI.
- **PHP extensions.** ThunderPHP's `composer.json` requires `ext-gd`,
  `ext-pdo_mysql`, `ext-mbstring`, `ext-json`, `ext-fileinfo`, and
  `ext-zip`. Quick Install doesn't check for these before running -
  they're standard on most PHP-FPM setups HestiaCP manages, but worth
  confirming before this ships.
