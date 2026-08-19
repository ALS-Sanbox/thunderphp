# ThunderPHP v1.1.0

A feature release: a new content plugin, and a real bug fix in the page editor.

## Added

### Coloring Book plugin
A complete new content type for interactive, kid-friendly coloring books - admin CRUD for books and their pages, secure server-side SVG upload/sanitization, a public read-only JSON API, and a native GrapesJS block that renders them on any Advanced Page using [`SVGColoringWidget`](https://github.com/ALS-Sanbox/Coloring-Book-App) as the coloring engine (zoom/pan, undo/redo, print, download, local progress - no accounts, nothing colored ever uploaded automatically).

SVG uploads are handled entirely separately from the general image uploader (which stays raster-only): a dedicated `SvgSanitizer` parses with PHP's DOM facilities (not regex), rejecting anything with a DOCTYPE/ENTITY declaration outright (the XXE vector) and stripping `<script>`, `<foreignObject>`, `<iframe>`, `<object>`, `<embed>`, every `on*` event attribute, and `javascript:`/`vbscript:` URLs - while leaving legitimate paths, gradients, patterns, and transforms untouched. Verified with 20 automated test cases and a real hostile-SVG upload through the live HTTP flow.

See [wiki/Coloring-Book-Plugin.md](wiki/Coloring-Book-Plugin.md) for the full architecture and [wiki/Header-Footer-Editor.md](wiki/Header-Footer-Editor.md)-style documentation of the block.

## Fixed

- **GrapesJS media upload was broken on both Add Page and Edit Page.** The Advanced Editor's image upload handler referenced a variable that was never defined at all on Add Page, and pointed at a non-functional path (not a real upload endpoint) on Edit Page. It now uses the same real Images-plugin endpoint (`window.imageLibraryUrls`) Summernote's editor was already using correctly, with the right field name, CSRF token, and response handling.

## Upgrading from v1.0.1

Pull `release` and run `php thunder do:migrate all` - two new tables (`coloring_books`, `coloring_book_pages`), both additive. No changes to any existing table.
