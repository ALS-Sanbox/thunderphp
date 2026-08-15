# ThunderPHP v1.0.1

A bug-fix release.

## Fixed

- **Default header: account area landed below the nav instead of top-right.** On any site with no custom header layout saved yet — true for every fresh install, including one created via `do:install` or the HestiaCP Quick Install, until an admin opens the header editor and builds one — the account area (Login/Signup, or the logged-in user's avatar and Admin/Profile/Logout menu) rendered on its own line below the navigation bar, left-aligned, instead of sitting in the bar itself. It now sits in the nav's top-right corner as expected, with the menu links (Home, by default) on the left where a primary nav normally sits. See [wiki/Header-Footer-Editor.md](wiki/Header-Footer-Editor.md) for how the header editor's fallback works and why this was the fallback specifically, not the editor itself.

## Also in this release

- New wiki page: [Header & Footer Editor](wiki/Header-Footer-Editor.md), documenting the GrapesJS header/footer editor, its four dynamic tokens (`{{SITE_MENU}}`, `{{SITE_LOGO}}`, `{{HOME_LINK}}`, `{{USER_MENU}}`), and the default-fallback behavior this release fixes.

## Upgrading from v1.0.0

Pull `release` and redeploy `plugins/header-footer/views/header.php` — no database changes, no migration to run.
