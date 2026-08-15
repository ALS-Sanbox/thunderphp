# Header & Footer Editor

`header-footer` is the plugin that renders every page's `<head>`, opening `<body>`, and closing `</body>`/`</html>` — including the site's navigation and account area. Its admin screens (`/admin/header-footer`) give you a GrapesJS drag-and-drop editor for both, the same editor used for pages/posts.

## The four dynamic tokens

The editor's canvas isn't rendered as literal HTML — it's saved as a template containing up to four placeholder tokens, each replaced with real markup at render time:

| Token | Replaced with |
|---|---|
| `{{SITE_MENU}}` | Your site's real navigation menu (from the `site-menus` plugin) |
| `{{SITE_LOGO}}` | The image set in Settings → Site Logo, linked to the homepage |
| `{{HOME_LINK}}` | A plain "Home" link to the homepage |
| `{{USER_MENU}}` | Login/Signup links (logged out), or the current user's avatar + Admin/Profile/Logout dropdown (logged in) |

Each corresponds to a block in the editor's **Dynamic** block category — drag one in to mark where that piece should appear. You control the layout (flexbox, positioning, whatever); the editor doesn't assume anything about where these blocks sit relative to each other.

## Before you've saved anything

A brand new site — including one installed via `do:install` or the HestiaCP Quick Install — has no saved `header_layout` setting yet. Two things follow from that, both by design, not bugs:

- **Opening the editor for the first time** shows a starting canvas with only a `{{SITE_MENU}}` block — not logo, home link, or user menu. You have to drag those in yourself if you want them; there's no default header layout that ships pre-built.
- **The live site**, until you save a layout, falls back to a plain, non-editable default: `site-menus`' own nav bar, with the account area (Login/Signup or your avatar) pinned to its top-right corner. This fallback exists purely so a fresh site doesn't look broken before you've had a chance to design a real header — it's not meant to be a good long-term header, just a reasonable placeholder. `header.php`'s `else` branch (used exactly when `setting('header_layout')` is empty) is what builds it — see the comment there for the CSS positioning details and why site-menus' dead `href="#"` "Logo" placeholder gets hidden in that fallback specifically (it was pushing the real menu links to the same corner the account area needs).

The moment you save a layout in the editor — even a simple one — that fallback stops being used entirely, and your layout renders instead.

## Custom code and JavaScript

The editor includes GrapesJS's custom-code plugin, so you can drop in raw HTML/CSS (and inline `<script>`) for anything the block library doesn't cover.
