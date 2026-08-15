# ThunderPHP Wiki

ThunderPHP is a plugin-based PHP MVC framework — every feature, including the admin panel itself, is a plugin under `plugins/`.

## Pages

- **[Installation](Installation.md)** — running the install wizard, Standard vs. Minimal profiles, requirements.
- **[Plugin Development](Plugin-Development.md)** — the plugin architecture, the hook system, and two easy-to-hit gotchas in the migration and autoloading systems.
- **[Header & Footer Editor](Header-Footer-Editor.md)** — the GrapesJS editor for your site's header/footer, the four dynamic tokens, and what a fresh site looks like before you've saved a layout.
- **[Site Overrides](Site-Overrides.md)** — customizing a plugin's views, assets, or behavior without editing its files directly, so updates never destroy your customizations.
- **[Versioning](Versioning.md)** — how ThunderPHP's version number works.
- **[Branches](Branches.md)** — `main`, `beta`, and `release`, and how a change moves between them.

## Branches

- `main` — active development. Expect breaking changes.
- `beta` — next version, in testing. Should be stable, but not yet promoted to `release`.
- `release` — stable, production. What the HestiaCP Quick Install app and any packaged downloads ship from.

See [Branches](Branches.md) for how work moves between them.

## License

ThunderPHP is [MIT licensed](https://github.com/ALS-Sanbox/thunderphp/blob/main/LICENSE).
