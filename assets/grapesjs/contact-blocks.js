(function () {
    function registerContactBlocks(editor) {
        editor.BlockManager.add('contact-form', {
            label: 'Contact (Simple Form)',
            category: 'Contact',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="10" y="3" width="28" height="6" rx="1"/><rect x="10" y="12" width="28" height="6" rx="1"/><rect x="10" y="21" width="28" height="6" rx="1"/><rect x="18" y="30" width="12" height="4" rx="2"/></svg>',
            content: `
<section class="gjs-contact gjs-contact-form-wrap">
  <h2>Get in touch</h2>
  <p>We'd love to hear from you, send us a message and we'll reply within a day.</p>
  <form class="gjs-contact-form" onsubmit="return false;">
    <input type="text" placeholder="Your name" required>
    <input type="email" placeholder="Your email" required>
    <textarea rows="5" placeholder="Your message" required></textarea>
    <button type="submit">Send Message</button>
  </form>
</section>
<style>
.gjs-contact-form-wrap { max-width: 560px; margin: 0 auto; padding: 72px 24px; text-align: center; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-contact-form-wrap h2 { color: #111827; font-size: 1.8rem; margin: 0 0 12px; }
.gjs-contact-form-wrap p { color: #6b7280; margin: 0 0 32px; }
.gjs-contact-form-wrap .gjs-contact-form { display: flex; flex-direction: column; gap: 14px; text-align: left; }
.gjs-contact-form-wrap input, .gjs-contact-form-wrap textarea { padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.95rem; font-family: inherit; resize: vertical; }
.gjs-contact-form-wrap button { background: #2563eb; color: #fff; border: 0; font-weight: 700; padding: 13px 0; border-radius: 8px; cursor: pointer; transition: background .15s ease; }
.gjs-contact-form-wrap button:hover { background: #1d4ed8; }
</style>`,
        });

        editor.BlockManager.add('contact-split', {
            label: 'Contact (Split with Info)',
            category: 'Contact',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="10" x2="19" y2="10"/><line x1="3" y1="16" x2="16" y2="16"/><line x1="3" y1="22" x2="19" y2="22"/><rect x="26" y="6" width="19" height="6" rx="1"/><rect x="26" y="14" width="19" height="6" rx="1"/><rect x="26" y="22" width="12" height="6" rx="1"/></svg>',
            content: `
<section class="gjs-contact gjs-contact-split">
  <div class="gjs-contact-info">
    <h2>Let's talk</h2>
    <p>Reach out through the form or find us directly below.</p>
    <div class="gjs-contact-info-row"><strong>Email</strong><span>hello@brand.com</span></div>
    <div class="gjs-contact-info-row"><strong>Phone</strong><span>+1 (555) 123-4567</span></div>
    <div class="gjs-contact-info-row"><strong>Address</strong><span>123 Market St, San Francisco, CA</span></div>
  </div>
  <form class="gjs-contact-form" onsubmit="return false;">
    <input type="text" placeholder="Your name" required>
    <input type="email" placeholder="Your email" required>
    <textarea rows="4" placeholder="Your message" required></textarea>
    <button type="submit">Send Message</button>
  </form>
</section>
<style>
.gjs-contact-split { display: flex; gap: 56px; padding: 80px 48px; background: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-contact-split .gjs-contact-info { flex: 1; min-width: 240px; }
.gjs-contact-split h2 { color: #111827; font-size: 1.8rem; margin: 0 0 12px; }
.gjs-contact-split .gjs-contact-info > p { color: #6b7280; margin: 0 0 28px; }
.gjs-contact-split .gjs-contact-info-row { display: flex; flex-direction: column; gap: 2px; margin-bottom: 18px; }
.gjs-contact-split .gjs-contact-info-row strong { color: #111827; font-size: 0.85rem; text-transform: uppercase; letter-spacing: .03em; }
.gjs-contact-split .gjs-contact-info-row span { color: #4b5563; font-size: 0.95rem; }
.gjs-contact-split .gjs-contact-form { flex: 1; min-width: 240px; display: flex; flex-direction: column; gap: 14px; background: #fff; padding: 28px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
.gjs-contact-split input, .gjs-contact-split textarea { padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.95rem; font-family: inherit; resize: vertical; }
.gjs-contact-split button { background: #111827; color: #fff; border: 0; font-weight: 700; padding: 13px 0; border-radius: 8px; cursor: pointer; transition: background .15s ease; }
.gjs-contact-split button:hover { background: #000; }
@media (max-width: 768px) { .gjs-contact-split { flex-direction: column; padding: 56px 24px; } }
</style>`,
        });

        editor.BlockManager.add('contact-info-cards', {
            label: 'Contact (Info Cards)',
            category: 'Contact',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="8" width="12" height="20" rx="1"/><rect x="18" y="8" width="12" height="20" rx="1"/><rect x="33" y="8" width="12" height="20" rx="1"/><circle cx="9" cy="15" r="3"/><circle cx="24" cy="15" r="3"/><circle cx="39" cy="15" r="3"/></svg>',
            content: `
<section class="gjs-contact gjs-contact-cards">
  <div class="gjs-contact-card"><div class="gjs-contact-card-icon">&#9993;</div><h4>Email</h4><p>hello@brand.com</p></div>
  <div class="gjs-contact-card"><div class="gjs-contact-card-icon">&#9742;</div><h4>Phone</h4><p>+1 (555) 123-4567</p></div>
  <div class="gjs-contact-card"><div class="gjs-contact-card-icon">&#128205;</div><h4>Office</h4><p>123 Market St, San Francisco</p></div>
</section>
<style>
.gjs-contact-cards { display: flex; gap: 24px; padding: 72px 48px; background: #fff; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-contact-cards .gjs-contact-card { flex: 1; min-width: 200px; text-align: center; background: #f9fafb; border-radius: 12px; padding: 32px 20px; }
.gjs-contact-cards .gjs-contact-card-icon { font-size: 1.8rem; margin-bottom: 14px; }
.gjs-contact-cards h4 { color: #111827; font-size: 1rem; margin: 0 0 8px; }
.gjs-contact-cards p { color: #6b7280; font-size: 0.9rem; margin: 0; }
@media (max-width: 768px) { .gjs-contact-cards { flex-direction: column; padding: 56px 24px; } }
</style>`,
        });

        editor.BlockManager.add('contact-map-form', {
            label: 'Contact (Map + Form)',
            category: 'Contact',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="42" height="12" rx="1"/><rect x="12" y="19" width="24" height="6" rx="1"/><rect x="18" y="28" width="12" height="4" rx="2"/></svg>',
            content: `
<section class="gjs-contact gjs-contact-map-wrap">
  <div class="gjs-contact-map"><img src="https://placehold.co/1200x360?text=Map" alt="Map"></div>
  <div class="gjs-contact-map-form-inner">
    <h2>Visit or write to us</h2>
    <form class="gjs-contact-form" onsubmit="return false;">
      <input type="text" placeholder="Your name" required>
      <input type="email" placeholder="Your email" required>
      <textarea rows="4" placeholder="Your message" required></textarea>
      <button type="submit">Send Message</button>
    </form>
  </div>
</section>
<style>
.gjs-contact-map-wrap { background: #fff; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-contact-map-wrap .gjs-contact-map img { width: 100%; height: auto; display: block; }
.gjs-contact-map-wrap .gjs-contact-map-form-inner { max-width: 560px; margin: 0 auto; padding: 56px 24px; text-align: center; }
.gjs-contact-map-wrap h2 { color: #111827; font-size: 1.6rem; margin: 0 0 24px; }
.gjs-contact-map-wrap .gjs-contact-form { display: flex; flex-direction: column; gap: 14px; text-align: left; }
.gjs-contact-map-wrap input, .gjs-contact-map-wrap textarea { padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.95rem; font-family: inherit; resize: vertical; }
.gjs-contact-map-wrap button { background: #2563eb; color: #fff; border: 0; font-weight: 700; padding: 13px 0; border-radius: 8px; cursor: pointer; transition: background .15s ease; }
.gjs-contact-map-wrap button:hover { background: #1d4ed8; }
</style>`,
        });

        editor.BlockManager.add('contact-minimal', {
            label: 'Contact (Minimal)',
            category: 'Contact',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="14" x2="36" y2="14"/><line x1="15" y1="22" x2="33" y2="22"/></svg>',
            content: `
<section class="gjs-contact gjs-contact-minimal">
  <h2>Say hello</h2>
  <a href="mailto:hello@brand.com" class="gjs-contact-minimal-link">hello@brand.com</a>
  <a href="tel:+15551234567" class="gjs-contact-minimal-link">+1 (555) 123-4567</a>
</section>
<style>
.gjs-contact-minimal { text-align: center; padding: 72px 24px; background: #111827; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-contact-minimal h2 { color: #fff; font-size: 1.8rem; margin: 0 0 20px; }
.gjs-contact-minimal .gjs-contact-minimal-link { display: block; color: #93c5fd; text-decoration: none; font-size: 1.15rem; margin-bottom: 8px; }
.gjs-contact-minimal .gjs-contact-minimal-link:hover { text-decoration: underline; }
</style>`,
        });
    }

    window.registerContactBlocks = registerContactBlocks;
})();
