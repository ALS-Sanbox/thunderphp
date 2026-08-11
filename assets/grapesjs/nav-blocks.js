(function () {
    function registerNavBlocks(editor) {
        editor.BlockManager.add('nav-basic', {
            label: 'Nav Menu',
            category: 'Navigation',
            content: `
<nav class="gjs-nav gjs-nav-basic">
  <div class="gjs-nav-brand">Brand</div>
  <ul class="gjs-nav-links">
    <li class="gjs-nav-active"><a href="#">Home</a></li>
    <li><a href="#">About</a></li>
    <li><a href="#">Services</a></li>
    <li><a href="#">Contact</a></li>
  </ul>
</nav>
<style>
.gjs-nav-basic { display: flex; align-items: center; justify-content: space-between; padding: 16px 32px; background: #1f2937; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-nav-basic .gjs-nav-brand { color: #fff; font-size: 1.25rem; font-weight: 700; }
.gjs-nav-basic .gjs-nav-links { display: flex; gap: 28px; list-style: none; margin: 0; padding: 0; }
.gjs-nav-basic .gjs-nav-links a { color: #d1d5db; text-decoration: none; font-size: 0.95rem; transition: color .15s ease; }
.gjs-nav-basic .gjs-nav-links li:hover a { color: #fff; }
.gjs-nav-basic .gjs-nav-links .gjs-nav-active a { color: #60a5fa; font-weight: 600; }
</style>`,
        });

        editor.BlockManager.add('nav-sticky', {
            label: 'Sticky Nav Menu',
            category: 'Navigation',
            content: `
<nav class="gjs-nav gjs-nav-basic gjs-nav-sticky">
  <div class="gjs-nav-brand">Brand</div>
  <ul class="gjs-nav-links">
    <li class="gjs-nav-active"><a href="#">Home</a></li>
    <li><a href="#">About</a></li>
    <li><a href="#">Services</a></li>
    <li><a href="#">Contact</a></li>
  </ul>
</nav>
<style>
.gjs-nav-sticky { position: sticky; top: 0; z-index: 999; box-shadow: 0 2px 8px rgba(0,0,0,.15); }
</style>`,
        });

        editor.BlockManager.add('nav-dropdown', {
            label: 'Dropdown / Multi-level Nav',
            category: 'Navigation',
            content: `
<nav class="gjs-nav gjs-nav-dropdown">
  <div class="gjs-nav-brand">Brand</div>
  <ul class="gjs-nav-links">
    <li><a href="#">Home</a></li>
    <li class="gjs-nav-has-dropdown">
      <a href="#">Services <span class="gjs-caret">&#9662;</span></a>
      <ul class="gjs-dropdown">
        <li><a href="#">Web Design</a></li>
        <li class="gjs-nav-has-dropdown gjs-nav-sub">
          <a href="#">Development <span class="gjs-caret">&#9656;</span></a>
          <ul class="gjs-dropdown gjs-dropdown-sub">
            <li><a href="#">Frontend</a></li>
            <li><a href="#">Backend</a></li>
          </ul>
        </li>
        <li><a href="#">SEO</a></li>
      </ul>
    </li>
    <li><a href="#">Contact</a></li>
  </ul>
</nav>
<style>
.gjs-nav-dropdown { position: relative; display: flex; align-items: center; justify-content: space-between; padding: 16px 32px; background: #1f2937; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-nav-dropdown .gjs-nav-brand { color: #fff; font-size: 1.25rem; font-weight: 700; }
.gjs-nav-dropdown .gjs-nav-links { display: flex; gap: 28px; list-style: none; margin: 0; padding: 0; }
.gjs-nav-dropdown .gjs-nav-links > li { position: relative; }
.gjs-nav-dropdown .gjs-nav-links a { color: #d1d5db; text-decoration: none; font-size: 0.95rem; display: block; padding: 6px 0; }
.gjs-nav-dropdown .gjs-nav-links > li:hover > a { color: #fff; }
.gjs-nav-dropdown .gjs-caret { font-size: 0.7em; }
.gjs-nav-dropdown .gjs-dropdown { display: none; position: absolute; top: 100%; left: 0; min-width: 190px; background: #fff; border-radius: 6px; box-shadow: 0 8px 20px rgba(0,0,0,.15); padding: 8px 0; list-style: none; margin: 0; z-index: 20; }
.gjs-nav-dropdown .gjs-nav-has-dropdown:hover > .gjs-dropdown { display: block; }
.gjs-nav-dropdown .gjs-dropdown li { position: relative; }
.gjs-nav-dropdown .gjs-dropdown a { color: #1f2937; padding: 8px 18px; }
.gjs-nav-dropdown .gjs-dropdown li:hover > a { background: #f3f4f6; }
.gjs-nav-dropdown .gjs-dropdown-sub { top: -8px; left: 100%; }
</style>`,
        });

        editor.BlockManager.add('nav-mega', {
            label: 'Mega Menu',
            category: 'Navigation',
            content: `
<nav class="gjs-nav gjs-nav-mega">
  <div class="gjs-nav-brand">Brand</div>
  <ul class="gjs-nav-links">
    <li><a href="#">Home</a></li>
    <li class="gjs-nav-has-mega">
      <a href="#">Products <span class="gjs-caret">&#9662;</span></a>
      <div class="gjs-mega-panel">
        <div class="gjs-mega-col">
          <h4>Category 1</h4>
          <a href="#">Item A</a>
          <a href="#">Item B</a>
        </div>
        <div class="gjs-mega-col">
          <h4>Category 2</h4>
          <a href="#">Item C</a>
          <a href="#">Item D</a>
        </div>
        <div class="gjs-mega-col">
          <h4>Category 3</h4>
          <a href="#">Item E</a>
          <a href="#">Item F</a>
        </div>
      </div>
    </li>
    <li><a href="#">Contact</a></li>
  </ul>
</nav>
<style>
.gjs-nav-mega { position: relative; display: flex; align-items: center; justify-content: space-between; padding: 16px 32px; background: #1f2937; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-nav-mega .gjs-nav-brand { color: #fff; font-size: 1.25rem; font-weight: 700; }
.gjs-nav-mega .gjs-nav-links { display: flex; gap: 28px; list-style: none; margin: 0; padding: 0; }
.gjs-nav-mega .gjs-nav-links > li { position: static; }
.gjs-nav-mega .gjs-nav-links a { color: #d1d5db; text-decoration: none; font-size: 0.95rem; }
.gjs-nav-mega .gjs-nav-links > li:hover > a { color: #fff; }
.gjs-nav-mega .gjs-caret { font-size: 0.7em; }
.gjs-nav-mega .gjs-mega-panel { display: none; position: absolute; left: 0; right: 0; top: 100%; background: #fff; box-shadow: 0 12px 24px rgba(0,0,0,.15); padding: 28px 32px; gap: 40px; z-index: 20; }
.gjs-nav-mega .gjs-nav-has-mega:hover .gjs-mega-panel { display: flex; }
.gjs-nav-mega .gjs-mega-col h4 { font-size: 0.85rem; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; margin: 0 0 10px; }
.gjs-nav-mega .gjs-mega-col a { display: block; color: #1f2937; padding: 4px 0; }
.gjs-nav-mega .gjs-mega-col a:hover { color: #2563eb; }
</style>`,
        });

        editor.BlockManager.add('nav-mobile', {
            label: 'Mobile / Hamburger Nav',
            category: 'Navigation',
            content: `
<nav class="gjs-nav gjs-nav-mobile" data-gjs-nav-open="false">
  <div class="gjs-nav-brand">Brand</div>
  <button class="gjs-hamburger" onclick="var n=this.closest('.gjs-nav-mobile'); n.classList.toggle('gjs-nav-open');">
    <span></span><span></span><span></span>
  </button>
  <ul class="gjs-nav-links">
    <li class="gjs-nav-active"><a href="#">Home</a></li>
    <li><a href="#">About</a></li>
    <li><a href="#">Services</a></li>
    <li><a href="#">Contact</a></li>
  </ul>
</nav>
<style>
.gjs-nav-mobile { position: relative; display: flex; align-items: center; justify-content: space-between; padding: 16px 32px; background: #1f2937; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-nav-mobile .gjs-nav-brand { color: #fff; font-size: 1.25rem; font-weight: 700; }
.gjs-nav-mobile .gjs-nav-links { display: flex; gap: 28px; list-style: none; margin: 0; padding: 0; }
.gjs-nav-mobile .gjs-nav-links a { color: #d1d5db; text-decoration: none; font-size: 0.95rem; }
.gjs-nav-mobile .gjs-nav-links li:hover a { color: #fff; }
.gjs-nav-mobile .gjs-nav-links .gjs-nav-active a { color: #60a5fa; font-weight: 600; }
.gjs-nav-mobile .gjs-hamburger { display: none; flex-direction: column; justify-content: center; gap: 5px; width: 32px; height: 32px; background: none; border: 0; cursor: pointer; padding: 0; }
.gjs-nav-mobile .gjs-hamburger span { display: block; width: 100%; height: 2px; background: #fff; border-radius: 2px; }
@media (max-width: 768px) {
  .gjs-nav-mobile .gjs-hamburger { display: flex; }
  .gjs-nav-mobile .gjs-nav-links { display: none; position: absolute; top: 100%; left: 0; right: 0; flex-direction: column; gap: 0; background: #1f2937; padding: 8px 0; }
  .gjs-nav-mobile .gjs-nav-links li { border-top: 1px solid rgba(255,255,255,.08); }
  .gjs-nav-mobile .gjs-nav-links a { padding: 12px 32px; }
  .gjs-nav-mobile.gjs-nav-open .gjs-nav-links { display: flex; }
}
</style>`,
        });
    }

    window.registerNavBlocks = registerNavBlocks;
})();
