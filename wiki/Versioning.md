# Versioning

ThunderPHP's version number lives in a single file: `VERSION`, at the repository root. It's a plain text file containing just the version string (e.g. `1.0.0-rc1`) — nothing else reads or writes it automatically.

## Reading the version

- **In PHP code**: `app_version()` (defined in `app/core/functions.php`) reads and caches the file's contents, falling back to `0.0.0` if it's missing.
- **From the CLI**: `php thunder version` prints it.
- **In the admin panel**: shown in the footer of every admin page.

## Bumping it

Edit the `VERSION` file. There's no build step or generated file that needs to stay in sync — `app_version()` reads it directly.

## Update checking

The `update-checker` plugin builds on this file: it compares it against GitHub's releases API once a day and can download, back up, and apply a newer release — manually or automatically. See its admin screen at `/admin/update-checker`.
