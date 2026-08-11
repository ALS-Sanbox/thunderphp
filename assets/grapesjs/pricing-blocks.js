(function () {
    function registerPricingBlocks(editor) {
        editor.BlockManager.add('pricing-tiers', {
            label: 'Pricing (3 Tiers)',
            category: 'Pricing',
            content: `
<section class="gjs-price gjs-price-tiers">
  <div class="gjs-price-card">
    <h3>Basic</h3>
    <div class="gjs-price-amount">$9<span>/mo</span></div>
    <ul>
      <li>1 project</li>
      <li>Community support</li>
      <li>1GB storage</li>
    </ul>
    <a href="#" class="gjs-price-btn">Choose Basic</a>
  </div>
  <div class="gjs-price-card gjs-price-featured">
    <div class="gjs-price-badge">Most Popular</div>
    <h3>Pro</h3>
    <div class="gjs-price-amount">$29<span>/mo</span></div>
    <ul>
      <li>Unlimited projects</li>
      <li>Priority support</li>
      <li>50GB storage</li>
      <li>Team collaboration</li>
    </ul>
    <a href="#" class="gjs-price-btn gjs-price-btn-primary">Choose Pro</a>
  </div>
  <div class="gjs-price-card">
    <h3>Enterprise</h3>
    <div class="gjs-price-amount">$99<span>/mo</span></div>
    <ul>
      <li>Everything in Pro</li>
      <li>Dedicated support</li>
      <li>Unlimited storage</li>
      <li>SSO &amp; audit logs</li>
    </ul>
    <a href="#" class="gjs-price-btn">Choose Enterprise</a>
  </div>
</section>
<style>
.gjs-price-tiers { display: flex; gap: 24px; align-items: stretch; padding: 80px 48px; background: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-price-tiers .gjs-price-card { flex: 1; min-width: 220px; background: #fff; border-radius: 14px; padding: 32px 28px; box-shadow: 0 1px 3px rgba(0,0,0,.08); position: relative; }
.gjs-price-tiers .gjs-price-featured { background: #111827; transform: translateY(-8px); }
.gjs-price-tiers .gjs-price-badge { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: #2563eb; color: #fff; font-size: 0.75rem; font-weight: 700; padding: 4px 14px; border-radius: 999px; white-space: nowrap; }
.gjs-price-tiers h3 { color: #111827; font-size: 1.15rem; margin: 0 0 12px; }
.gjs-price-tiers .gjs-price-featured h3 { color: #fff; }
.gjs-price-tiers .gjs-price-amount { color: #111827; font-size: 2.2rem; font-weight: 800; margin-bottom: 20px; }
.gjs-price-tiers .gjs-price-amount span { font-size: 1rem; font-weight: 500; color: #6b7280; }
.gjs-price-tiers .gjs-price-featured .gjs-price-amount { color: #fff; }
.gjs-price-tiers .gjs-price-featured .gjs-price-amount span { color: #9ca3af; }
.gjs-price-tiers ul { list-style: none; margin: 0 0 28px; padding: 0; display: flex; flex-direction: column; gap: 10px; }
.gjs-price-tiers ul li { color: #4b5563; font-size: 0.92rem; padding-left: 22px; position: relative; }
.gjs-price-tiers ul li::before { content: "\\2713"; position: absolute; left: 0; color: #16a34a; font-weight: 700; }
.gjs-price-tiers .gjs-price-featured ul li { color: #d1d5db; }
.gjs-price-tiers .gjs-price-btn { display: block; text-align: center; background: #f3f4f6; color: #111827; text-decoration: none; font-weight: 700; padding: 12px 0; border-radius: 8px; transition: background .15s ease; }
.gjs-price-tiers .gjs-price-btn:hover { background: #e5e7eb; }
.gjs-price-tiers .gjs-price-btn-primary { background: #2563eb; color: #fff; }
.gjs-price-tiers .gjs-price-btn-primary:hover { background: #1d4ed8; }
@media (max-width: 768px) { .gjs-price-tiers { flex-direction: column; padding: 56px 24px; } .gjs-price-tiers .gjs-price-featured { transform: none; } }
</style>`,
        });

        editor.BlockManager.add('pricing-single', {
            label: 'Pricing (Single Card)',
            category: 'Pricing',
            content: `
<section class="gjs-price gjs-price-single-wrap">
  <div class="gjs-price-single">
    <h3>Lifetime Access</h3>
    <div class="gjs-price-amount">$249<span> one-time</span></div>
    <p>Pay once, use forever. Every future update included.</p>
    <a href="#" class="gjs-price-btn">Get Lifetime Access</a>
  </div>
</section>
<style>
.gjs-price-single-wrap { padding: 72px 32px; background: #fff; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-price-single { max-width: 380px; margin: 0 auto; text-align: center; border: 2px solid #e5e7eb; border-radius: 16px; padding: 40px 32px; }
.gjs-price-single h3 { color: #111827; font-size: 1.2rem; margin: 0 0 16px; }
.gjs-price-single .gjs-price-amount { color: #111827; font-size: 2.5rem; font-weight: 800; margin-bottom: 12px; }
.gjs-price-single .gjs-price-amount span { font-size: 1rem; font-weight: 500; color: #6b7280; }
.gjs-price-single p { color: #6b7280; font-size: 0.95rem; margin: 0 0 28px; }
.gjs-price-single .gjs-price-btn { display: inline-block; background: #111827; color: #fff; text-decoration: none; font-weight: 700; padding: 13px 32px; border-radius: 8px; transition: background .15s ease; }
.gjs-price-single .gjs-price-btn:hover { background: #000; }
</style>`,
        });

        editor.BlockManager.add('pricing-comparison', {
            label: 'Pricing (Comparison Table)',
            category: 'Pricing',
            content: `
<section class="gjs-price gjs-price-compare">
  <table class="gjs-price-table">
    <thead>
      <tr><th>Feature</th><th>Basic</th><th>Pro</th><th>Enterprise</th></tr>
    </thead>
    <tbody>
      <tr><td>Projects</td><td>1</td><td>Unlimited</td><td>Unlimited</td></tr>
      <tr><td>Storage</td><td>1GB</td><td>50GB</td><td>Unlimited</td></tr>
      <tr><td>Support</td><td>Community</td><td>Priority</td><td>Dedicated</td></tr>
      <tr><td>SSO</td><td>&#8212;</td><td>&#8212;</td><td>&#10003;</td></tr>
      <tr><td>Price</td><td>$9/mo</td><td>$29/mo</td><td>$99/mo</td></tr>
    </tbody>
  </table>
</section>
<style>
.gjs-price-compare { padding: 72px 48px; background: #f9fafb; overflow-x: auto; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-price-compare .gjs-price-table { width: 100%; max-width: 800px; margin: 0 auto; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
.gjs-price-compare th, .gjs-price-compare td { padding: 14px 20px; text-align: center; font-size: 0.9rem; }
.gjs-price-compare th:first-child, .gjs-price-compare td:first-child { text-align: left; color: #111827; font-weight: 600; }
.gjs-price-compare thead th { background: #111827; color: #fff; font-weight: 700; }
.gjs-price-compare tbody tr:nth-child(even) { background: #f9fafb; }
.gjs-price-compare td { color: #4b5563; }
</style>`,
        });

        editor.BlockManager.add('pricing-toggle', {
            label: 'Pricing (Monthly / Yearly Toggle)',
            category: 'Pricing',
            content: `
<section class="gjs-price gjs-price-toggle-wrap">
  <input type="checkbox" id="gjs-price-yearly-toggle" class="gjs-price-toggle-input">
  <div class="gjs-price-toggle-label">
    <span>Monthly</span>
    <label for="gjs-price-yearly-toggle" class="gjs-price-switch"><span class="gjs-price-switch-dot"></span></label>
    <span>Yearly <em>(save 20%)</em></span>
  </div>
  <div class="gjs-price-toggle-card">
    <h3>Pro Plan</h3>
    <div class="gjs-price-amount gjs-price-monthly-amount">$29<span>/mo</span></div>
    <div class="gjs-price-amount gjs-price-yearly-amount">$23<span>/mo, billed yearly</span></div>
    <a href="#" class="gjs-price-btn">Start Free Trial</a>
  </div>
</section>
<style>
.gjs-price-toggle-wrap { text-align: center; padding: 72px 32px; background: #fff; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-price-toggle-wrap .gjs-price-toggle-input { display: none; }
.gjs-price-toggle-wrap .gjs-price-toggle-label { display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 32px; color: #374151; font-size: 0.95rem; }
.gjs-price-toggle-wrap .gjs-price-toggle-label em { color: #16a34a; font-style: normal; font-weight: 600; }
.gjs-price-toggle-wrap .gjs-price-switch { width: 46px; height: 26px; background: #e5e7eb; border-radius: 999px; position: relative; display: inline-block; cursor: pointer; transition: background .15s ease; }
.gjs-price-toggle-wrap .gjs-price-switch-dot { position: absolute; top: 3px; left: 3px; width: 20px; height: 20px; background: #fff; border-radius: 50%; transition: left .15s ease; box-shadow: 0 1px 2px rgba(0,0,0,.2); }
.gjs-price-toggle-wrap .gjs-price-toggle-input:checked + .gjs-price-toggle-label .gjs-price-switch { background: #2563eb; }
.gjs-price-toggle-wrap .gjs-price-toggle-input:checked + .gjs-price-toggle-label .gjs-price-switch-dot { left: 23px; }
.gjs-price-toggle-wrap .gjs-price-toggle-card { max-width: 320px; margin: 0 auto; border: 2px solid #e5e7eb; border-radius: 16px; padding: 36px 28px; }
.gjs-price-toggle-wrap h3 { color: #111827; font-size: 1.1rem; margin: 0 0 16px; }
.gjs-price-toggle-wrap .gjs-price-amount { color: #111827; font-size: 2.2rem; font-weight: 800; margin-bottom: 24px; }
.gjs-price-toggle-wrap .gjs-price-amount span { font-size: 0.9rem; font-weight: 500; color: #6b7280; }
.gjs-price-toggle-wrap .gjs-price-yearly-amount { display: none; }
.gjs-price-toggle-wrap .gjs-price-toggle-input:checked ~ .gjs-price-toggle-card .gjs-price-monthly-amount { display: none; }
.gjs-price-toggle-wrap .gjs-price-toggle-input:checked ~ .gjs-price-toggle-card .gjs-price-yearly-amount { display: block; }
.gjs-price-toggle-wrap .gjs-price-btn { display: inline-block; background: #2563eb; color: #fff; text-decoration: none; font-weight: 700; padding: 13px 30px; border-radius: 8px; transition: background .15s ease; }
.gjs-price-toggle-wrap .gjs-price-btn:hover { background: #1d4ed8; }
</style>`,
        });

        editor.BlockManager.add('pricing-minimal', {
            label: 'Pricing (Minimal List)',
            category: 'Pricing',
            content: `
<section class="gjs-price gjs-price-minimal">
  <div class="gjs-price-row"><span>Web Design</span><span class="gjs-price-dots"></span><span>$1,200</span></div>
  <div class="gjs-price-row"><span>Brand Identity</span><span class="gjs-price-dots"></span><span>$800</span></div>
  <div class="gjs-price-row"><span>Full Website Build</span><span class="gjs-price-dots"></span><span>$3,500</span></div>
</section>
<style>
.gjs-price-minimal { max-width: 560px; margin: 0 auto; padding: 72px 24px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-price-minimal .gjs-price-row { display: flex; align-items: baseline; gap: 8px; padding: 14px 0; border-bottom: 1px solid #e5e7eb; }
.gjs-price-minimal .gjs-price-row span:first-child { color: #111827; font-weight: 600; white-space: nowrap; }
.gjs-price-minimal .gjs-price-row span:last-child { color: #111827; font-weight: 700; white-space: nowrap; }
.gjs-price-minimal .gjs-price-dots { flex: 1; border-bottom: 1px dotted #d1d5db; margin-bottom: 5px; }
</style>`,
        });
    }

    window.registerPricingBlocks = registerPricingBlocks;
})();
