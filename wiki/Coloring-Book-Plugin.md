# Coloring Book Plugin

`coloring-book` manages coloring books and their pages, and renders them on the public site using [`SVGColoringWidget`](https://github.com/ALS-Sanbox/Coloring-Book-App) as the actual coloring engine. ThunderPHP owns the content (books, pages, SVG storage, sanitization, admin CRUD, the public API, the GrapesJS block); the widget owns the interactive coloring experience itself (rendering, coloring, zoom/pan, undo/redo, print/download, local progress).

## Why SVG gets its own upload path

ThunderPHP's general image uploader (`Request::upload_files()`, used by the Images plugin) only accepts raster formats - JPEG, PNG, GIF, WebP. That's deliberate: SVG is XML, and XML can carry `<script>` tags, event-handler attributes, and external entity references. Simply adding `image/svg+xml` to that uploader's allowlist would mean the general-purpose image picker used across the whole admin - page/post featured images, avatars, site logo - could suddenly accept active content anywhere an admin can upload an image.

Instead, coloring page SVGs go through a dedicated path: `add_page_controller.php`/`edit_page_controller.php` read the uploaded file directly (not via `upload_files()`) and run it through `ColoringBook\SvgSanitizer` before anything touches disk.

## Server-side SVG sanitization

`plugins/coloring-book/includes/SvgSanitizer.php` is DOM-based, not regex-based:

1. Rejects anything over 5MB or containing a `<!DOCTYPE` / `<!ENTITY` declaration outright - a legitimate coloring page never needs one, and it's the classic XXE (XML external entity) attack vector. Modern libxml already disables external entity resolution by default, but this is rejected explicitly rather than relying solely on that default.
2. Parses with `DOMDocument::loadXML(..., LIBXML_NONET)` - `LIBXML_NONET` means no network access is attempted for any external reference, regardless.
3. Rejects malformed XML and anything whose root element isn't `<svg>`.
4. Removes `<script>`, `<foreignObject>`, `<iframe>`, `<object>`, `<embed>`, `<link>`, `<meta>` - matched via XPath `local-name()`, not `getElementsByTagName()`, specifically so a namespace-prefix trick (`<svg:script>`) can't dodge the check.
5. Strips every `on*` event-handler attribute, `javascript:`/`vbscript:`/`data:text/html` URLs from `href`/`xlink:href`/`src` (internal `#fragment` references - what gradients and patterns actually use - are left alone), and `style` attributes containing `javascript:` or the legacy `expression()` CSS attack.

What it does **not** restrict: paths, groups, shapes, fills, strokes, gradients, patterns, IDs, transforms, `viewBox` - anything that isn't on the explicit danger list survives untouched. The goal is removing what's actively dangerous, not narrowing SVG down to a safe subset that might break real artwork.

This mirrors (independently - it's not code-shared, since PHP and JS can't share a sanitizer implementation) the client-side sanitizer in `SVGColoringWidget` itself, which protects the browser that loads a given SVG. Both layers matter: the client-side one protects whoever's looking at a page right now; the server-side one is what actually decides what's safe to write to disk and serve to everyone else, and it's the one that can't be bypassed by skipping the browser entirely and POSTing straight to the upload endpoint.

Verified with 20 automated test cases (malformed XML, non-SVG root, XXE via DOCTYPE/ENTITY, an entity-expansion bomb, namespace-prefixed script tags, every dangerous element/attribute above) plus a full real-HTTP round trip through the actual upload controllers - not just the sanitizer in isolation.

## Storage

```
uploads/
    coloring-books/
        a-trip-to-grandmas/
            packing-for-grandmas.svg
            packing-for-grandmas.webp   (thumbnail, if uploaded)
```

Filenames are built from the already-validated (`^[a-z0-9-]+$`) page slug - never the client-supplied filename - so there's no path-traversal surface. Thumbnails reuse the existing general-purpose raster uploader (same MIME-derived-extension hardening as every other image upload in the app), just pointed at the book's own directory instead of the shared `uploads/` root.

No automatic SVG-to-thumbnail generation - that would need a real rendering engine (a headless browser, or a library like Imagick with SVG support, which isn't a safe default assumption for every host this runs on) as an external dependency, which is more than this feature needs. An admin uploads or replaces a thumbnail separately; a coloring page with no thumbnail just shows without one in the page picker.

## Data model

Two tables, no SQL `FOREIGN KEY` constraint between them - matching this codebase's existing convention (no migration anywhere declares one; see `redirects`/`not_found_log` for the same pattern). `coloring_book_pages.coloring_book_id` is managed at the application level instead: deleting a book explicitly deletes its pages (and their files) first, in `delete_book_controller.php`.

- `coloring_books`: title, slug (globally unique), description, cover_image, status (draft/published), sort_order.
- `coloring_book_pages`: title, slug (unique **within its book**, not globally - two different books can each have a page called "cover"), svg_path, thumbnail_path, sort_order, status.

## The public API

`GET /coloring-book/api/{slug}` - read-only, unauthenticated, published-only. Not `/api/coloring-books/{slug}` as originally sketched: ThunderPHP has no existing `/api/` namespace anywhere else, so this stays under the plugin's own top-level public route, the same way `/contact-form` and `/search` are public routes under their own plugin's name rather than a shared prefix.

Returns only public URLs - never a filesystem path - and only published books/published pages, in `sort_order`.

## The GrapesJS block

`assets/grapesjs/coloring-book-block.js` (top-level, alongside `hero-blocks.js`/`faq-blocks.js`/etc. - not inside this plugin's own folder, matching how every other block file is wired into `basic-pages`' Advanced Editor) registers a custom GrapesJS component type with real traits (Coloring Book, Height, Show Title, Show Page Picker, Show Instructions, Show Fullscreen, Show Print, Show Download, Remember Progress) - not just a static content block, since this one needs a dropdown of actual published books.

That dropdown is populated from `window.coloringBooksList`, rendered server-side by `basic-pages`' `add.php`/`edit.php` (published `coloring_books`, `{slug, title}`) - the same pattern already used for `window.imageLibraryUrls`. GrapesJS's trait options have to be known when the editor initializes, so this is resolved server-side rather than fetched asynchronously from inside the block file itself, which would race the trait panel's own rendering. The query goes through basic-pages' own model instance (`$pages->query(...)`, `$pages->tableExists('coloring_books')` guarding against the plugin not being installed) rather than instantiating `\ColoringBook\ColoringBooks` directly - see [Plugin Development](Plugin-Development.md) for why a plugin's model class can't safely be `new`'d from outside its own plugin folder.

Inside the editor, the component renders a static, clearly-labeled preview instead of the live interactive widget - the widget does its own click/drag/keyboard handling, which would likely fight GrapesJS's own selection and drag events if it ran inside the canvas. The real, interactive widget only ever initializes on the actual public page.

The block's output is plain declarative markup, nothing else:

```html
<div class="thunder-coloring-book" data-thunder-coloring-book="true" data-coloring-book="a-trip-to-grandmas" data-height="700px" ...></div>
```

## The frontend initializer

`assets/js/frontend-init.js` is loaded on every public page (it's genuinely tiny) via a `before_head_close` hook. It looks for `.thunder-coloring-book` elements; if there are none, it does nothing else at all. If it finds one, only then does it load the much heavier `svg-coloring-widget.js`/`.css` (vendored into this plugin - see below), fetch that block's book data from the public API, render the title/instructions/page picker per its trait attributes, and initialize `SVGColoringWidget`.

Persistence keys are namespaced per book+page (`coloring:{book-slug}:{page-slug}`), so switching pages in the picker loads that page's own saved progress if any exists, or the clean SVG otherwise - never mixes up progress between pages or books.

## Vendoring `SVGColoringWidget`

`plugins/coloring-book/assets/{js,css}/svg-coloring-widget.*` are **unmodified, synced copies** from [`ALS-Sanbox/Coloring-Book-App`](https://github.com/ALS-Sanbox/Coloring-Book-App), not developed here. That repository stays the source of truth and a generic, ThunderPHP-agnostic library - the vendored files just make it possible to actually serve the engine from this plugin without permanently merging the two repositories. Each vendored file has a header comment with the exact source commit; to update, pull the latest from Coloring-Book-App and replace both files wholesale.

## Theming

The frontend block's own CSS (`assets/css/frontend.css`) doesn't set a `font-family` (it inherits the surrounding site's typography) and exposes its few colors as CSS custom properties (`--tcb-accent`, `--tcb-bg`, `--tcb-border`, `--tcb-text-muted`) with sensible defaults - a site overrides these in its own stylesheet rather than this plugin hardcoding any one site's brand colors.
