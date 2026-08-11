(function () {
    function registerHeroBlocks(editor) {
        editor.BlockManager.add('hero-basic', {
            label: 'Hero (Centered)',
            category: 'Hero',
            content: `
<section class="gjs-hero gjs-hero-basic">
  <h1 class="gjs-hero-title">Build something people love</h1>
  <p class="gjs-hero-sub">A short line about the product or service, enough to hook a visitor in one breath.</p>
  <a href="#" class="gjs-hero-btn">Get Started</a>
</section>
<style>
.gjs-hero-basic { text-align: center; padding: 100px 32px; background: #111827; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-hero-basic .gjs-hero-title { color: #fff; font-size: 2.75rem; font-weight: 800; margin: 0 0 18px; line-height: 1.15; }
.gjs-hero-basic .gjs-hero-sub { color: #9ca3af; font-size: 1.15rem; max-width: 560px; margin: 0 auto 32px; }
.gjs-hero-basic .gjs-hero-btn { display: inline-block; background: #2563eb; color: #fff; text-decoration: none; font-weight: 600; padding: 14px 32px; border-radius: 8px; transition: background .15s ease; }
.gjs-hero-basic .gjs-hero-btn:hover { background: #1d4ed8; }
</style>`,
        });

        editor.BlockManager.add('hero-split', {
            label: 'Hero (Split Image)',
            category: 'Hero',
            content: `
<section class="gjs-hero gjs-hero-split">
  <div class="gjs-hero-split-text">
    <h1 class="gjs-hero-title">Grow faster with less effort</h1>
    <p class="gjs-hero-sub">Explain the value in a sentence or two, then let the button do the rest of the work.</p>
    <a href="#" class="gjs-hero-btn">Start Free Trial</a>
  </div>
  <div class="gjs-hero-split-media">
    <img src="https://placehold.co/560x420" alt="">
  </div>
</section>
<style>
.gjs-hero-split { display: flex; align-items: center; gap: 56px; padding: 90px 60px; background: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-hero-split .gjs-hero-split-text { flex: 1; min-width: 280px; }
.gjs-hero-split .gjs-hero-split-media { flex: 1; min-width: 280px; }
.gjs-hero-split .gjs-hero-split-media img { width: 100%; height: auto; border-radius: 12px; display: block; }
.gjs-hero-split .gjs-hero-title { font-size: 2.4rem; font-weight: 800; color: #111827; margin: 0 0 16px; line-height: 1.15; }
.gjs-hero-split .gjs-hero-sub { color: #4b5563; font-size: 1.05rem; margin: 0 0 28px; max-width: 440px; }
.gjs-hero-split .gjs-hero-btn { display: inline-block; background: #111827; color: #fff; text-decoration: none; font-weight: 600; padding: 13px 28px; border-radius: 8px; transition: background .15s ease; }
.gjs-hero-split .gjs-hero-btn:hover { background: #000; }
@media (max-width: 768px) {
  .gjs-hero-split { flex-direction: column; padding: 60px 24px; text-align: center; }
  .gjs-hero-split .gjs-hero-sub { margin-left: auto; margin-right: auto; }
}
</style>`,
        });

        editor.BlockManager.add('hero-bg-image', {
            label: 'Hero (Background Image)',
            category: 'Hero',
            content: `
<section class="gjs-hero gjs-hero-bg" style="background-image: url('https://placehold.co/1600x800');">
  <div class="gjs-hero-bg-overlay">
    <h1 class="gjs-hero-title">Your next chapter starts here</h1>
    <p class="gjs-hero-sub">Full-bleed background image with a dark overlay so text stays readable over any photo.</p>
    <a href="#" class="gjs-hero-btn">Learn More</a>
  </div>
</section>
<style>
.gjs-hero-bg { position: relative; background-size: cover; background-position: center; padding: 140px 32px; text-align: center; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-hero-bg::before { content: ""; position: absolute; inset: 0; background: rgba(0,0,0,.5); }
.gjs-hero-bg .gjs-hero-bg-overlay { position: relative; z-index: 1; max-width: 640px; margin: 0 auto; }
.gjs-hero-bg .gjs-hero-title { color: #fff; font-size: 2.75rem; font-weight: 800; margin: 0 0 18px; line-height: 1.15; }
.gjs-hero-bg .gjs-hero-sub { color: #e5e7eb; font-size: 1.15rem; margin: 0 0 32px; }
.gjs-hero-bg .gjs-hero-btn { display: inline-block; background: #fff; color: #111827; text-decoration: none; font-weight: 600; padding: 14px 32px; border-radius: 8px; transition: opacity .15s ease; }
.gjs-hero-bg .gjs-hero-btn:hover { opacity: .88; }
</style>`,
        });

        editor.BlockManager.add('hero-signup', {
            label: 'Hero (Email Signup)',
            category: 'Hero',
            content: `
<section class="gjs-hero gjs-hero-signup">
  <h1 class="gjs-hero-title">Get early access</h1>
  <p class="gjs-hero-sub">Join the waitlist and we'll let you know the moment we launch.</p>
  <form class="gjs-hero-form" onsubmit="return false;">
    <input type="email" class="gjs-hero-input" placeholder="you@example.com" required>
    <button type="submit" class="gjs-hero-btn">Notify Me</button>
  </form>
</section>
<style>
.gjs-hero-signup { text-align: center; padding: 100px 32px; background: linear-gradient(135deg, #4f46e5, #7c3aed); font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-hero-signup .gjs-hero-title { color: #fff; font-size: 2.6rem; font-weight: 800; margin: 0 0 16px; }
.gjs-hero-signup .gjs-hero-sub { color: #e0e7ff; font-size: 1.1rem; margin: 0 0 32px; }
.gjs-hero-signup .gjs-hero-form { display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; max-width: 460px; margin: 0 auto; }
.gjs-hero-signup .gjs-hero-input { flex: 1; min-width: 220px; padding: 13px 18px; border-radius: 8px; border: 0; font-size: 1rem; }
.gjs-hero-signup .gjs-hero-btn { background: #111827; color: #fff; border: 0; font-weight: 600; padding: 13px 26px; border-radius: 8px; cursor: pointer; transition: background .15s ease; }
.gjs-hero-signup .gjs-hero-btn:hover { background: #000; }
</style>`,
        });

        editor.BlockManager.add('hero-stats', {
            label: 'Hero (With Stats)',
            category: 'Hero',
            content: `
<section class="gjs-hero gjs-hero-stats">
  <h1 class="gjs-hero-title">Trusted by teams everywhere</h1>
  <p class="gjs-hero-sub">A quick line of credibility before visitors scroll any further.</p>
  <a href="#" class="gjs-hero-btn">See Why</a>
  <div class="gjs-hero-stats-row">
    <div class="gjs-hero-stat"><span class="gjs-hero-stat-num">12k+</span><span class="gjs-hero-stat-label">Active Users</span></div>
    <div class="gjs-hero-stat"><span class="gjs-hero-stat-num">98%</span><span class="gjs-hero-stat-label">Satisfaction</span></div>
    <div class="gjs-hero-stat"><span class="gjs-hero-stat-num">40+</span><span class="gjs-hero-stat-label">Countries</span></div>
  </div>
</section>
<style>
.gjs-hero-stats { text-align: center; padding: 90px 32px 70px; background: #0f172a; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-hero-stats .gjs-hero-title { color: #fff; font-size: 2.6rem; font-weight: 800; margin: 0 0 16px; }
.gjs-hero-stats .gjs-hero-sub { color: #94a3b8; font-size: 1.1rem; margin: 0 0 28px; }
.gjs-hero-stats .gjs-hero-btn { display: inline-block; background: #2563eb; color: #fff; text-decoration: none; font-weight: 600; padding: 13px 30px; border-radius: 8px; margin-bottom: 56px; transition: background .15s ease; }
.gjs-hero-stats .gjs-hero-btn:hover { background: #1d4ed8; }
.gjs-hero-stats .gjs-hero-stats-row { display: flex; justify-content: center; gap: 64px; flex-wrap: wrap; }
.gjs-hero-stats .gjs-hero-stat { display: flex; flex-direction: column; }
.gjs-hero-stats .gjs-hero-stat-num { color: #fff; font-size: 2.1rem; font-weight: 800; }
.gjs-hero-stats .gjs-hero-stat-label { color: #64748b; font-size: .9rem; margin-top: 4px; }
</style>`,
        });
    }

    window.registerHeroBlocks = registerHeroBlocks;
})();
