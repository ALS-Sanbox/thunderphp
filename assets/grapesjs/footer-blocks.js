(function () {
    function registerFooterBlocks(editor) {
        editor.BlockManager.add('footer-simple', {
            label: 'Footer (Simple)',
            category: 'Footer',
            content: `
<footer class="gjs-footer gjs-footer-simple">
  <div class="gjs-footer-brand">Brand</div>
  <ul class="gjs-footer-links">
    <li><a href="#">Home</a></li>
    <li><a href="#">About</a></li>
    <li><a href="#">Services</a></li>
    <li><a href="#">Contact</a></li>
  </ul>
  <div class="gjs-footer-copy">&copy; 2026 Brand. All rights reserved.</div>
</footer>
<style>
.gjs-footer-simple { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; padding: 28px 32px; background: #111827; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-footer-simple .gjs-footer-brand { color: #fff; font-size: 1.1rem; font-weight: 700; }
.gjs-footer-simple .gjs-footer-links { display: flex; gap: 24px; list-style: none; margin: 0; padding: 0; }
.gjs-footer-simple .gjs-footer-links a { color: #9ca3af; text-decoration: none; font-size: 0.9rem; transition: color .15s ease; }
.gjs-footer-simple .gjs-footer-links a:hover { color: #fff; }
.gjs-footer-simple .gjs-footer-copy { color: #6b7280; font-size: 0.85rem; }
@media (max-width: 768px) { .gjs-footer-simple { justify-content: center; text-align: center; } }
</style>`,
        });

        editor.BlockManager.add('footer-columns', {
            label: 'Footer (Columns)',
            category: 'Footer',
            content: `
<footer class="gjs-footer gjs-footer-columns">
  <div class="gjs-footer-cols">
    <div class="gjs-footer-col gjs-footer-about">
      <div class="gjs-footer-brand">Brand</div>
      <p class="gjs-footer-desc">A short description of the company or product goes here, a sentence or two is plenty.</p>
    </div>
    <div class="gjs-footer-col">
      <h4>Company</h4>
      <a href="#">About</a>
      <a href="#">Careers</a>
      <a href="#">Blog</a>
    </div>
    <div class="gjs-footer-col">
      <h4>Resources</h4>
      <a href="#">Docs</a>
      <a href="#">Support</a>
      <a href="#">FAQ</a>
    </div>
    <div class="gjs-footer-col">
      <h4>Legal</h4>
      <a href="#">Privacy</a>
      <a href="#">Terms</a>
    </div>
  </div>
  <div class="gjs-footer-bottom">&copy; 2026 Brand. All rights reserved.</div>
</footer>
<style>
.gjs-footer-columns { padding: 56px 40px 24px; background: #0f172a; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-footer-columns .gjs-footer-cols { display: flex; gap: 48px; flex-wrap: wrap; padding-bottom: 32px; border-bottom: 1px solid rgba(255,255,255,.08); }
.gjs-footer-columns .gjs-footer-col { flex: 1; min-width: 160px; display: flex; flex-direction: column; gap: 10px; }
.gjs-footer-columns .gjs-footer-about { flex: 1.6; min-width: 220px; }
.gjs-footer-columns .gjs-footer-brand { color: #fff; font-size: 1.15rem; font-weight: 700; margin-bottom: 6px; }
.gjs-footer-columns .gjs-footer-desc { color: #94a3b8; font-size: 0.9rem; line-height: 1.5; margin: 0; }
.gjs-footer-columns .gjs-footer-col h4 { color: #fff; font-size: 0.85rem; text-transform: uppercase; letter-spacing: .04em; margin: 0 0 4px; }
.gjs-footer-columns .gjs-footer-col a { color: #94a3b8; text-decoration: none; font-size: 0.9rem; transition: color .15s ease; }
.gjs-footer-columns .gjs-footer-col a:hover { color: #fff; }
.gjs-footer-columns .gjs-footer-bottom { color: #64748b; font-size: 0.85rem; padding-top: 20px; text-align: center; }
@media (max-width: 768px) { .gjs-footer-columns .gjs-footer-cols { flex-direction: column; gap: 28px; } }
</style>`,
        });

        editor.BlockManager.add('footer-newsletter', {
            label: 'Footer (With Newsletter)',
            category: 'Footer',
            content: `
<footer class="gjs-footer gjs-footer-newsletter">
  <div class="gjs-footer-nl-top">
    <div class="gjs-footer-nl-text">
      <h3>Stay in the loop</h3>
      <p>Get product updates and news, no spam.</p>
    </div>
    <form class="gjs-footer-nl-form" onsubmit="return false;">
      <input type="email" class="gjs-footer-nl-input" placeholder="you@example.com" required>
      <button type="submit" class="gjs-footer-nl-btn">Subscribe</button>
    </form>
  </div>
  <div class="gjs-footer-nl-bottom">
    <div class="gjs-footer-brand">Brand</div>
    <ul class="gjs-footer-links">
      <li><a href="#">Privacy</a></li>
      <li><a href="#">Terms</a></li>
      <li><a href="#">Contact</a></li>
    </ul>
    <div class="gjs-footer-copy">&copy; 2026 Brand.</div>
  </div>
</footer>
<style>
.gjs-footer-newsletter { background: #111827; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-footer-newsletter .gjs-footer-nl-top { display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap; padding: 40px 40px; border-bottom: 1px solid rgba(255,255,255,.08); }
.gjs-footer-newsletter .gjs-footer-nl-text h3 { color: #fff; font-size: 1.4rem; margin: 0 0 6px; }
.gjs-footer-newsletter .gjs-footer-nl-text p { color: #9ca3af; margin: 0; font-size: 0.95rem; }
.gjs-footer-newsletter .gjs-footer-nl-form { display: flex; gap: 10px; flex-wrap: wrap; }
.gjs-footer-newsletter .gjs-footer-nl-input { padding: 12px 16px; border-radius: 8px; border: 0; font-size: 0.95rem; min-width: 220px; }
.gjs-footer-newsletter .gjs-footer-nl-btn { background: #2563eb; color: #fff; border: 0; font-weight: 600; padding: 12px 24px; border-radius: 8px; cursor: pointer; transition: background .15s ease; }
.gjs-footer-newsletter .gjs-footer-nl-btn:hover { background: #1d4ed8; }
.gjs-footer-newsletter .gjs-footer-nl-bottom { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; padding: 20px 40px; }
.gjs-footer-newsletter .gjs-footer-brand { color: #fff; font-weight: 700; }
.gjs-footer-newsletter .gjs-footer-links { display: flex; gap: 20px; list-style: none; margin: 0; padding: 0; }
.gjs-footer-newsletter .gjs-footer-links a { color: #9ca3af; text-decoration: none; font-size: 0.85rem; }
.gjs-footer-newsletter .gjs-footer-links a:hover { color: #fff; }
.gjs-footer-newsletter .gjs-footer-copy { color: #6b7280; font-size: 0.85rem; }
</style>`,
        });

        editor.BlockManager.add('footer-social', {
            label: 'Footer (Centered + Social)',
            category: 'Footer',
            content: `
<footer class="gjs-footer gjs-footer-social">
  <div class="gjs-footer-brand">Brand</div>
  <ul class="gjs-footer-links">
    <li><a href="#">Home</a></li>
    <li><a href="#">About</a></li>
    <li><a href="#">Services</a></li>
    <li><a href="#">Contact</a></li>
  </ul>
  <div class="gjs-footer-social-row">
    <a href="#" class="gjs-footer-social-icon">X</a>
    <a href="#" class="gjs-footer-social-icon">In</a>
    <a href="#" class="gjs-footer-social-icon">Ig</a>
    <a href="#" class="gjs-footer-social-icon">Yt</a>
  </div>
  <div class="gjs-footer-copy">&copy; 2026 Brand. All rights reserved.</div>
</footer>
<style>
.gjs-footer-social { text-align: center; padding: 48px 32px 32px; background: #0f172a; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-footer-social .gjs-footer-brand { color: #fff; font-size: 1.2rem; font-weight: 700; margin-bottom: 16px; }
.gjs-footer-social .gjs-footer-links { display: flex; justify-content: center; gap: 24px; list-style: none; margin: 0 0 24px; padding: 0; }
.gjs-footer-social .gjs-footer-links a { color: #94a3b8; text-decoration: none; font-size: 0.9rem; }
.gjs-footer-social .gjs-footer-links a:hover { color: #fff; }
.gjs-footer-social .gjs-footer-social-row { display: flex; justify-content: center; gap: 12px; margin-bottom: 24px; }
.gjs-footer-social .gjs-footer-social-icon { width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,.06); color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 0.8rem; font-weight: 600; transition: background .15s ease; }
.gjs-footer-social .gjs-footer-social-icon:hover { background: #2563eb; }
.gjs-footer-social .gjs-footer-copy { color: #64748b; font-size: 0.85rem; padding-top: 20px; border-top: 1px solid rgba(255,255,255,.08); }
</style>`,
        });

        editor.BlockManager.add('footer-minimal', {
            label: 'Footer (Minimal)',
            category: 'Footer',
            content: `
<footer class="gjs-footer gjs-footer-minimal">
  <span>&copy; 2026 Brand. All rights reserved.</span>
</footer>
<style>
.gjs-footer-minimal { text-align: center; padding: 20px; background: #f9fafb; color: #6b7280; font-size: 0.85rem; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
</style>`,
        });
    }

    window.registerFooterBlocks = registerFooterBlocks;
})();
