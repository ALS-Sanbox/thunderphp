(function () {
    function registerFaqBlocks(editor) {
        editor.BlockManager.add('faq-accordion', {
            label: 'FAQ (Accordion)',
            category: 'FAQ',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="42" height="8" rx="1"/><path d="M36 6 L39 9 L42 6"/><rect x="3" y="15" width="42" height="8" rx="1"/><path d="M36 17 L39 20 L42 17"/><rect x="3" y="26" width="42" height="8" rx="1"/><path d="M36 28 L39 31 L42 28"/></svg>',
            content: `
<section class="gjs-faq gjs-faq-accordion">
  <h2>Frequently Asked Questions</h2>
  <details class="gjs-faq-item" open>
    <summary>What's included in the free plan?</summary>
    <p>The free plan includes one project, community support, and 1GB of storage, no credit card required.</p>
  </details>
  <details class="gjs-faq-item">
    <summary>Can I cancel anytime?</summary>
    <p>Yes, you can cancel your subscription at any time from your account settings with no penalty.</p>
  </details>
  <details class="gjs-faq-item">
    <summary>Do you offer refunds?</summary>
    <p>We offer a full refund within 14 days of purchase, no questions asked.</p>
  </details>
  <details class="gjs-faq-item">
    <summary>Is my data secure?</summary>
    <p>All data is encrypted in transit and at rest, and we run regular third-party security audits.</p>
  </details>
</section>
<style>
.gjs-faq-accordion { max-width: 720px; margin: 0 auto; padding: 72px 24px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-faq-accordion h2 { color: #111827; font-size: 1.8rem; text-align: center; margin: 0 0 32px; }
.gjs-faq-accordion .gjs-faq-item { border-bottom: 1px solid #e5e7eb; padding: 18px 0; }
.gjs-faq-accordion summary { color: #111827; font-weight: 600; font-size: 1.02rem; cursor: pointer; list-style: none; }
.gjs-faq-accordion summary::-webkit-details-marker { display: none; }
.gjs-faq-accordion summary::after { content: "+"; float: right; color: #6b7280; font-weight: 400; }
.gjs-faq-accordion details[open] summary::after { content: "\\2212"; }
.gjs-faq-accordion p { color: #6b7280; font-size: 0.95rem; line-height: 1.6; margin: 12px 0 0; }
</style>`,
        });

        editor.BlockManager.add('faq-two-column', {
            label: 'FAQ (Two Column)',
            category: 'FAQ',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="8" x2="20" y2="8" stroke-width="3"/><line x1="4" y1="14" x2="20" y2="14"/><line x1="4" y1="22" x2="20" y2="22" stroke-width="3"/><line x1="4" y1="28" x2="20" y2="28"/><line x1="28" y1="8" x2="44" y2="8" stroke-width="3"/><line x1="28" y1="14" x2="44" y2="14"/><line x1="28" y1="22" x2="44" y2="22" stroke-width="3"/><line x1="28" y1="28" x2="44" y2="28"/></svg>',
            content: `
<section class="gjs-faq gjs-faq-two-col">
  <h2>Common Questions</h2>
  <div class="gjs-faq-cols">
    <div class="gjs-faq-qa"><h4>How does billing work?</h4><p>You're billed monthly or yearly depending on the plan you choose, prorated for upgrades.</p></div>
    <div class="gjs-faq-qa"><h4>Can I invite my team?</h4><p>Yes, every plan supports unlimited team members with role-based permissions.</p></div>
    <div class="gjs-faq-qa"><h4>What integrations exist?</h4><p>We connect with all the major tools out of the box, plus a public API for the rest.</p></div>
    <div class="gjs-faq-qa"><h4>Is there a mobile app?</h4><p>Yes, native apps are available for both iOS and Android.</p></div>
  </div>
</section>
<style>
.gjs-faq-two-col { padding: 72px 48px; background: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-faq-two-col h2 { color: #111827; font-size: 1.8rem; text-align: center; margin: 0 0 40px; }
.gjs-faq-two-col .gjs-faq-cols { display: flex; flex-wrap: wrap; gap: 32px 48px; max-width: 900px; margin: 0 auto; }
.gjs-faq-two-col .gjs-faq-qa { flex: 1; min-width: 300px; }
.gjs-faq-two-col h4 { color: #111827; font-size: 1.02rem; margin: 0 0 8px; }
.gjs-faq-two-col p { color: #6b7280; font-size: 0.9rem; line-height: 1.55; margin: 0; }
</style>`,
        });

        editor.BlockManager.add('faq-cards', {
            label: 'FAQ (Card Grid)',
            category: 'FAQ',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="8" width="12" height="20" rx="1"/><rect x="18" y="8" width="12" height="20" rx="1"/><rect x="33" y="8" width="12" height="20" rx="1"/></svg>',
            content: `
<section class="gjs-faq gjs-faq-cards">
  <div class="gjs-faq-card"><h4>Do you offer discounts?</h4><p>Yes, students and nonprofits get 30% off any plan.</p></div>
  <div class="gjs-faq-card"><h4>Can I export my data?</h4><p>Export to CSV or JSON at any time from your dashboard.</p></div>
  <div class="gjs-faq-card"><h4>Is there an API?</h4><p>A full REST API is available on the Pro plan and above.</p></div>
</section>
<style>
.gjs-faq-cards { display: flex; gap: 24px; padding: 72px 48px; background: #fff; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-faq-cards .gjs-faq-card { flex: 1; min-width: 200px; background: #f9fafb; border-radius: 12px; padding: 24px; }
.gjs-faq-cards h4 { color: #111827; font-size: 1rem; margin: 0 0 10px; }
.gjs-faq-cards p { color: #6b7280; font-size: 0.9rem; line-height: 1.55; margin: 0; }
@media (max-width: 768px) { .gjs-faq-cards { flex-direction: column; padding: 56px 24px; } }
</style>`,
        });

        editor.BlockManager.add('faq-with-cta', {
            label: 'FAQ (With Contact CTA)',
            category: 'FAQ',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="42" height="7" rx="1"/><rect x="3" y="13" width="42" height="7" rx="1"/><rect x="16" y="26" width="16" height="6" rx="3"/></svg>',
            content: `
<section class="gjs-faq gjs-faq-cta-wrap">
  <h2>Questions &amp; Answers</h2>
  <details class="gjs-faq-item" open>
    <summary>How long is the free trial?</summary>
    <p>14 days, full access to every feature, no card needed to start.</p>
  </details>
  <details class="gjs-faq-item">
    <summary>What happens after the trial ends?</summary>
    <p>You'll be prompted to choose a plan, your data stays safe either way.</p>
  </details>
  <div class="gjs-faq-cta">
    <p>Still have questions?</p>
    <a href="#" class="gjs-faq-cta-btn">Contact Support</a>
  </div>
</section>
<style>
.gjs-faq-cta-wrap { max-width: 680px; margin: 0 auto; padding: 72px 24px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-faq-cta-wrap h2 { color: #111827; font-size: 1.8rem; text-align: center; margin: 0 0 32px; }
.gjs-faq-cta-wrap .gjs-faq-item { border-bottom: 1px solid #e5e7eb; padding: 16px 0; }
.gjs-faq-cta-wrap summary { color: #111827; font-weight: 600; cursor: pointer; list-style: none; }
.gjs-faq-cta-wrap summary::-webkit-details-marker { display: none; }
.gjs-faq-cta-wrap p { color: #6b7280; font-size: 0.92rem; line-height: 1.55; margin: 10px 0 0; }
.gjs-faq-cta-wrap .gjs-faq-cta { text-align: center; margin-top: 40px; padding-top: 32px; border-top: 1px solid #e5e7eb; }
.gjs-faq-cta-wrap .gjs-faq-cta p { color: #111827; font-weight: 600; margin-bottom: 16px; }
.gjs-faq-cta-wrap .gjs-faq-cta-btn { display: inline-block; background: #2563eb; color: #fff; text-decoration: none; font-weight: 700; padding: 12px 28px; border-radius: 8px; }
.gjs-faq-cta-wrap .gjs-faq-cta-btn:hover { background: #1d4ed8; }
</style>`,
        });

        editor.BlockManager.add('faq-simple-list', {
            label: 'FAQ (Simple List)',
            category: 'FAQ',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="7" x2="30" y2="7" stroke-width="3"/><line x1="4" y1="13" x2="44" y2="13"/><line x1="4" y1="18" x2="30" y2="18" stroke-width="3"/><line x1="4" y1="24" x2="44" y2="24"/><line x1="4" y1="29" x2="30" y2="29" stroke-width="3"/></svg>',
            content: `
<section class="gjs-faq gjs-faq-simple">
  <div class="gjs-faq-simple-item"><h4>Where can I see pricing?</h4><p>Full pricing details are on the pricing page, updated live.</p></div>
  <div class="gjs-faq-simple-item"><h4>Do you support SSO?</h4><p>SSO via SAML is available on Enterprise plans.</p></div>
  <div class="gjs-faq-simple-item"><h4>What's your uptime record?</h4><p>99.98% over the last 12 months, tracked publicly on our status page.</p></div>
</section>
<style>
.gjs-faq-simple { max-width: 640px; margin: 0 auto; padding: 72px 24px; display: flex; flex-direction: column; gap: 28px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-faq-simple h4 { color: #111827; font-size: 1.05rem; margin: 0 0 8px; }
.gjs-faq-simple p { color: #6b7280; font-size: 0.92rem; line-height: 1.55; margin: 0; }
</style>`,
        });
    }

    window.registerFaqBlocks = registerFaqBlocks;
})();
