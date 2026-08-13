# Versioning

ThunderPHP's version number lives in a single file: `VERSION`, at the repository root. It's a plain text file containing just the version string (e.g. `1.0.0-rc1`) — nothing else reads or writes it automatically.

## Reading the version

- **In PHP code**: `app_version()` (defined in `app/core/functions.php`) reads and caches the file's contents, falling back to `0.0.0` if it's missing.
- **From the CLI**: `php thunder version` prints it.
- **In the admin panel**: shown in the footer of every admin page.

## Bumping it

Edit the `VERSION` file. There's no build step or generated file that needs to stay in sync — `app_version()` reads it directly.

## What this doesn't do (yet)

This is just a version *identifier*, not an update-checker. ThunderPHP doesn't currently check GitHub (or anywhere else) for newer releases and notify you — that would be a natural thing to build on top of this, but isn't part of what's here today.
