(function () {
    function registerCtaBlocks(editor) {
        editor.BlockManager.add('cta-simple', {
            label: 'CTA (Simple Banner)',
            category: 'CTA',
            content: `
<section class="gjs-cta gjs-cta-simple">
  <h2>Ready to get started?</h2>
  <p>Join thousands of teams already using our product.</p>
  <a href="#" class="gjs-cta-btn">Get Started Free</a>
</section>
<style>
.gjs-cta-simple { text-align: center; padding: 72px 32px; background: #2563eb; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-cta-simple h2 { color: #fff; font-size: 2rem; font-weight: 800; margin: 0 0 12px; }
.gjs-cta-simple p { color: #dbeafe; font-size: 1.05rem; margin: 0 0 28px; }
.gjs-cta-simple .gjs-cta-btn { display: inline-block; background: #fff; color: #2563eb; text-decoration: none; font-weight: 700; padding: 14px 32px; border-radius: 8px; transition: opacity .15s ease; }
.gjs-cta-simple .gjs-cta-btn:hover { opacity: .9; }
</style>`,
        });

        editor.BlockManager.add('cta-two-buttons', {
            label: 'CTA (Two Buttons)',
            category: 'CTA',
            content: `
<section class="gjs-cta gjs-cta-two">
  <h2>Start building today</h2>
  <p>No credit card required. Cancel anytime.</p>
  <div class="gjs-cta-btn-row">
    <a href="#" class="gjs-cta-btn gjs-cta-btn-primary">Start Free Trial</a>
    <a href="#" class="gjs-cta-btn gjs-cta-btn-secondary">Talk to Sales</a>
  </div>
</section>
<style>
.gjs-cta-two { text-align: center; padding: 80px 32px; background: #111827; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-cta-two h2 { color: #fff; font-size: 2.1rem; font-weight: 800; margin: 0 0 12px; }
.gjs-cta-two p { color: #9ca3af; font-size: 1.05rem; margin: 0 0 32px; }
.gjs-cta-two .gjs-cta-btn-row { display: flex; justify-content: center; gap: 16px; flex-wrap: wrap; }
.gjs-cta-two .gjs-cta-btn { text-decoration: none; font-weight: 700; padding: 14px 30px; border-radius: 8px; transition: opacity .15s ease; }
.gjs-cta-two .gjs-cta-btn-primary { background: #2563eb; color: #fff; }
.gjs-cta-two .gjs-cta-btn-secondary { background: transparent; color: #fff; border: 1px solid rgba(255,255,255,.3); }
.gjs-cta-two .gjs-cta-btn:hover { opacity: .88; }
</style>`,
        });

        editor.BlockManager.add('cta-split', {
            label: 'CTA (Split with Image)',
            category: 'CTA',
            content: `
<section class="gjs-cta gjs-cta-split">
  <div class="gjs-cta-split-media"><img src="https://placehold.co/480x320" alt=""></div>
  <div class="gjs-cta-split-text">
    <h2>See it in action</h2>
    <p>Book a live walkthrough with our team and see how it fits your workflow.</p>
    <a href="#" class="gjs-cta-btn">Book a Demo</a>
  </div>
</section>
<style>
.gjs-cta-split { display: flex; align-items: center; gap: 48px; padding: 72px 48px; background: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-cta-split .gjs-cta-split-media { flex: 1; min-width: 240px; }
.gjs-cta-split .gjs-cta-split-media img { width: 100%; height: auto; border-radius: 12px; display: block; }
.gjs-cta-split .gjs-cta-split-text { flex: 1; min-width: 240px; }
.gjs-cta-split h2 { color: #111827; font-size: 1.9rem; margin: 0 0 12px; }
.gjs-cta-split p { color: #6b7280; font-size: 1rem; margin: 0 0 24px; max-width: 420px; }
.gjs-cta-split .gjs-cta-btn { display: inline-block; background: #111827; color: #fff; text-decoration: none; font-weight: 700; padding: 13px 28px; border-radius: 8px; transition: background .15s ease; }
.gjs-cta-split .gjs-cta-btn:hover { background: #000; }
@media (max-width: 768px) { .gjs-cta-split { flex-direction: column; padding: 56px 24px; text-align: center; } }
</style>`,
        });

        editor.BlockManager.add('cta-boxed', {
            label: 'CTA (Boxed Card)',
            category: 'CTA',
            content: `
<section class="gjs-cta gjs-cta-boxed-wrap">
  <div class="gjs-cta-boxed">
    <h2>Don't miss out</h2>
    <p>Get instant access to every feature, free for 14 days.</p>
    <a href="#" class="gjs-cta-btn">Claim Your Trial</a>
  </div>
</section>
<style>
.gjs-cta-boxed-wrap { padding: 64px 32px; background: #fff; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-cta-boxed { max-width: 680px; margin: 0 auto; text-align: center; background: linear-gradient(135deg, #4f46e5, #7c3aed); border-radius: 16px; padding: 56px 40px; }
.gjs-cta-boxed h2 { color: #fff; font-size: 1.9rem; margin: 0 0 12px; }
.gjs-cta-boxed p { color: #e0e7ff; font-size: 1rem; margin: 0 0 28px; }
.gjs-cta-boxed .gjs-cta-btn { display: inline-block; background: #fff; color: #4f46e5; text-decoration: none; font-weight: 700; padding: 14px 30px; border-radius: 8px; transition: opacity .15s ease; }
.gjs-cta-boxed .gjs-cta-btn:hover { opacity: .9; }
</style>`,
        });

        editor.BlockManager.add('cta-bar', {
            label: 'CTA (Slim Bar)',
            category: 'CTA',
            content: `
<section class="gjs-cta gjs-cta-bar">
  <span class="gjs-cta-bar-text">New: real-time collaboration is here.</span>
  <a href="#" class="gjs-cta-bar-btn">Try it now &rarr;</a>
</section>
<style>
.gjs-cta-bar { display: flex; align-items: center; justify-content: center; gap: 12px; flex-wrap: wrap; padding: 16px 24px; background: #111827; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-cta-bar .gjs-cta-bar-text { color: #fff; font-size: 0.95rem; }
.gjs-cta-bar .gjs-cta-bar-btn { color: #60a5fa; text-decoration: none; font-weight: 600; font-size: 0.95rem; }
.gjs-cta-bar .gjs-cta-bar-btn:hover { text-decoration: underline; }
</style>`,
        });
    }

    window.registerCtaBlocks = registerCtaBlocks;
})();
