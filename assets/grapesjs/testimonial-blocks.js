(function () {
    function registerTestimonialBlocks(editor) {
        editor.BlockManager.add('testimonial-single', {
            label: 'Testimonial (Single Quote)',
            category: 'Testimonials',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 8c-3 0-5 2-5 5s2 4 4 4c0-3 1-5 3-6z" fill="currentColor" stroke="none"/><path d="M26 8c-3 0-5 2-5 5s2 4 4 4c0-3 1-5 3-6z" fill="currentColor" stroke="none"/><line x1="10" y1="24" x2="38" y2="24"/><circle cx="24" cy="31" r="3"/></svg>',
            content: `
<section class="gjs-test gjs-test-single">
  <p class="gjs-test-quote">&ldquo;This completely changed how our team works. We shipped in weeks what used to take months.&rdquo;</p>
  <div class="gjs-test-author">
    <img src="https://placehold.co/56x56" alt="">
    <div>
      <div class="gjs-test-name">Jordan Lee</div>
      <div class="gjs-test-role">VP Engineering, Acme Co.</div>
    </div>
  </div>
</section>
<style>
.gjs-test-single { text-align: center; padding: 80px 32px; background: #fff; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-test-single .gjs-test-quote { font-size: 1.5rem; color: #111827; font-weight: 500; line-height: 1.5; max-width: 680px; margin: 0 auto 28px; }
.gjs-test-single .gjs-test-author { display: flex; align-items: center; justify-content: center; gap: 12px; }
.gjs-test-single .gjs-test-author img { width: 48px; height: 48px; border-radius: 50%; }
.gjs-test-single .gjs-test-name { color: #111827; font-weight: 700; font-size: 0.95rem; text-align: left; }
.gjs-test-single .gjs-test-role { color: #6b7280; font-size: 0.85rem; text-align: left; }
</style>`,
        });

        editor.BlockManager.add('testimonial-grid', {
            label: 'Testimonial (Card Grid)',
            category: 'Testimonials',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="5" width="12" height="24" rx="1"/><rect x="18" y="5" width="12" height="24" rx="1"/><rect x="33" y="5" width="12" height="24" rx="1"/><circle cx="9" cy="24" r="2"/><circle cx="24" cy="24" r="2"/><circle cx="39" cy="24" r="2"/></svg>',
            content: `
<section class="gjs-test gjs-test-grid">
  <div class="gjs-test-card">
    <p>&ldquo;Setup took ten minutes and it just worked. Support answered in minutes too.&rdquo;</p>
    <div class="gjs-test-author"><img src="https://placehold.co/44x44" alt=""><div><div class="gjs-test-name">Maria Chen</div><div class="gjs-test-role">Product Lead</div></div></div>
  </div>
  <div class="gjs-test-card">
    <p>&ldquo;The best investment we made this year. It paid for itself in the first month.&rdquo;</p>
    <div class="gjs-test-author"><img src="https://placehold.co/44x44" alt=""><div><div class="gjs-test-name">David Kim</div><div class="gjs-test-role">Founder, Loopwork</div></div></div>
  </div>
  <div class="gjs-test-card">
    <p>&ldquo;Our whole team switched over in a day. No looking back.&rdquo;</p>
    <div class="gjs-test-author"><img src="https://placehold.co/44x44" alt=""><div><div class="gjs-test-name">Priya Nair</div><div class="gjs-test-role">Ops Manager</div></div></div>
  </div>
</section>
<style>
.gjs-test-grid { display: flex; gap: 24px; padding: 80px 48px; background: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-test-grid .gjs-test-card { flex: 1; min-width: 220px; background: #fff; border-radius: 12px; padding: 26px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
.gjs-test-grid .gjs-test-card p { color: #374151; font-size: 0.95rem; line-height: 1.6; margin: 0 0 20px; }
.gjs-test-grid .gjs-test-author { display: flex; align-items: center; gap: 10px; }
.gjs-test-grid .gjs-test-author img { width: 40px; height: 40px; border-radius: 50%; }
.gjs-test-grid .gjs-test-name { color: #111827; font-weight: 700; font-size: 0.88rem; }
.gjs-test-grid .gjs-test-role { color: #6b7280; font-size: 0.8rem; }
@media (max-width: 768px) { .gjs-test-grid { flex-direction: column; padding: 56px 24px; } }
</style>`,
        });

        editor.BlockManager.add('testimonial-rating', {
            label: 'Testimonial (With Rating)',
            category: 'Testimonials',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" stroke="currentColor" stroke-width="2" stroke-linecap="round"><g fill="currentColor" stroke="none"><circle cx="8" cy="10" r="2"/><circle cx="16" cy="10" r="2"/><circle cx="24" cy="10" r="2"/><circle cx="32" cy="10" r="2"/><circle cx="40" cy="10" r="2"/></g><g fill="none"><line x1="10" y1="20" x2="38" y2="20"/><line x1="14" y1="26" x2="34" y2="26"/></g></svg>',
            content: `
<section class="gjs-test gjs-test-rating">
  <div class="gjs-test-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
  <p class="gjs-test-quote">&ldquo;Five stars isn't enough. This tool is the backbone of how we ship product now.&rdquo;</p>
  <div class="gjs-test-author">
    <img src="https://placehold.co/56x56" alt="">
    <div>
      <div class="gjs-test-name">Alex Rivera</div>
      <div class="gjs-test-role">CTO, Northwind</div>
    </div>
  </div>
</section>
<style>
.gjs-test-rating { text-align: center; padding: 80px 32px; background: #0f172a; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-test-rating .gjs-test-stars { color: #facc15; font-size: 1.3rem; letter-spacing: 4px; margin-bottom: 20px; }
.gjs-test-rating .gjs-test-quote { color: #fff; font-size: 1.4rem; font-weight: 500; line-height: 1.5; max-width: 640px; margin: 0 auto 28px; }
.gjs-test-rating .gjs-test-author { display: flex; align-items: center; justify-content: center; gap: 12px; }
.gjs-test-rating .gjs-test-author img { width: 48px; height: 48px; border-radius: 50%; }
.gjs-test-rating .gjs-test-name { color: #fff; font-weight: 700; font-size: 0.95rem; text-align: left; }
.gjs-test-rating .gjs-test-role { color: #94a3b8; font-size: 0.85rem; text-align: left; }
</style>`,
        });

        editor.BlockManager.add('testimonial-logos', {
            label: 'Testimonial (Trusted By / Logo Wall)',
            category: 'Testimonials',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="14" width="8" height="8" rx="1"/><rect x="14" y="14" width="8" height="8" rx="1"/><rect x="25" y="14" width="8" height="8" rx="1"/><rect x="36" y="14" width="9" height="8" rx="1"/></svg>',
            content: `
<section class="gjs-test gjs-test-logos">
  <p class="gjs-test-logos-label">Trusted by teams at</p>
  <div class="gjs-test-logos-row">
    <div class="gjs-test-logo">Acme</div>
    <div class="gjs-test-logo">Northwind</div>
    <div class="gjs-test-logo">Loopwork</div>
    <div class="gjs-test-logo">Vertex</div>
    <div class="gjs-test-logo">Halcyon</div>
  </div>
</section>
<style>
.gjs-test-logos { text-align: center; padding: 56px 32px; background: #fff; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-test-logos .gjs-test-logos-label { color: #9ca3af; font-size: 0.85rem; text-transform: uppercase; letter-spacing: .06em; margin: 0 0 28px; }
.gjs-test-logos .gjs-test-logos-row { display: flex; justify-content: center; gap: 48px; flex-wrap: wrap; }
.gjs-test-logos .gjs-test-logo { color: #9ca3af; font-weight: 700; font-size: 1.15rem; opacity: .7; }
</style>`,
        });
    }

    window.registerTestimonialBlocks = registerTestimonialBlocks;
})();
