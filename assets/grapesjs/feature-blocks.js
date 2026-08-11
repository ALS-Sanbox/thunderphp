(function () {
    function registerFeatureBlocks(editor) {
        editor.BlockManager.add('feature-grid', {
            label: 'Feature Grid (Icons)',
            category: 'Features',
            content: `
<section class="gjs-feat gjs-feat-grid">
  <div class="gjs-feat-item">
    <div class="gjs-feat-icon">&#9889;</div>
    <h3>Fast</h3>
    <p>Built for speed from the ground up, so nothing feels like it's waiting on you.</p>
  </div>
  <div class="gjs-feat-item">
    <div class="gjs-feat-icon">&#128274;</div>
    <h3>Secure</h3>
    <p>Your data stays protected with encryption at rest and in transit.</p>
  </div>
  <div class="gjs-feat-item">
    <div class="gjs-feat-icon">&#128200;</div>
    <h3>Scalable</h3>
    <p>Grows with you, from a first prototype to millions of users.</p>
  </div>
</section>
<style>
.gjs-feat-grid { display: flex; gap: 40px; padding: 80px 48px; background: #fff; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-feat-grid .gjs-feat-item { flex: 1; min-width: 180px; text-align: center; }
.gjs-feat-grid .gjs-feat-icon { font-size: 2rem; margin-bottom: 16px; }
.gjs-feat-grid h3 { color: #111827; font-size: 1.2rem; margin: 0 0 10px; }
.gjs-feat-grid p { color: #6b7280; font-size: 0.95rem; line-height: 1.55; margin: 0; }
@media (max-width: 768px) { .gjs-feat-grid { flex-direction: column; padding: 56px 24px; } }
</style>`,
        });

        editor.BlockManager.add('feature-cards', {
            label: 'Feature Cards (Image)',
            category: 'Features',
            content: `
<section class="gjs-feat gjs-feat-cards">
  <div class="gjs-feat-card">
    <img src="https://placehold.co/400x220" alt="">
    <div class="gjs-feat-card-body">
      <h3>Analytics</h3>
      <p>Track everything that matters with dashboards built for clarity.</p>
    </div>
  </div>
  <div class="gjs-feat-card">
    <img src="https://placehold.co/400x220" alt="">
    <div class="gjs-feat-card-body">
      <h3>Automation</h3>
      <p>Let the busywork run itself while you focus on what's next.</p>
    </div>
  </div>
  <div class="gjs-feat-card">
    <img src="https://placehold.co/400x220" alt="">
    <div class="gjs-feat-card-body">
      <h3>Collaboration</h3>
      <p>Bring the whole team into one shared, always up-to-date view.</p>
    </div>
  </div>
</section>
<style>
.gjs-feat-cards { display: flex; gap: 28px; padding: 80px 48px; background: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-feat-cards .gjs-feat-card { flex: 1; min-width: 220px; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
.gjs-feat-cards .gjs-feat-card img { width: 100%; height: auto; display: block; }
.gjs-feat-cards .gjs-feat-card-body { padding: 22px; }
.gjs-feat-cards h3 { color: #111827; font-size: 1.1rem; margin: 0 0 8px; }
.gjs-feat-cards p { color: #6b7280; font-size: 0.9rem; line-height: 1.5; margin: 0; }
@media (max-width: 768px) { .gjs-feat-cards { flex-direction: column; padding: 56px 24px; } }
</style>`,
        });

        editor.BlockManager.add('feature-list', {
            label: 'Feature List (Checkmarks)',
            category: 'Features',
            content: `
<section class="gjs-feat gjs-feat-list">
  <h2>Everything you need</h2>
  <ul class="gjs-feat-list-items">
    <li><span class="gjs-feat-check">&#10003;</span> Unlimited projects and workspaces</li>
    <li><span class="gjs-feat-check">&#10003;</span> Real-time collaboration with your team</li>
    <li><span class="gjs-feat-check">&#10003;</span> Advanced permissions and audit logs</li>
    <li><span class="gjs-feat-check">&#10003;</span> Priority support, 24/7</li>
  </ul>
</section>
<style>
.gjs-feat-list { padding: 72px 48px; background: #fff; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; max-width: 640px; margin: 0 auto; }
.gjs-feat-list h2 { color: #111827; font-size: 1.8rem; margin: 0 0 28px; text-align: center; }
.gjs-feat-list .gjs-feat-list-items { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 16px; }
.gjs-feat-list .gjs-feat-list-items li { display: flex; align-items: flex-start; gap: 12px; color: #374151; font-size: 1rem; }
.gjs-feat-list .gjs-feat-check { width: 24px; height: 24px; flex: none; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; }
</style>`,
        });

        editor.BlockManager.add('feature-alternating', {
            label: 'Feature (Alternating Rows)',
            category: 'Features',
            content: `
<section class="gjs-feat gjs-feat-alt">
  <div class="gjs-feat-alt-row">
    <div class="gjs-feat-alt-media"><img src="https://placehold.co/480x320" alt=""></div>
    <div class="gjs-feat-alt-text">
      <h3>Plan it out</h3>
      <p>Map every step before you start, so nothing gets lost between idea and launch.</p>
    </div>
  </div>
  <div class="gjs-feat-alt-row gjs-feat-alt-reverse">
    <div class="gjs-feat-alt-media"><img src="https://placehold.co/480x320" alt=""></div>
    <div class="gjs-feat-alt-text">
      <h3>Ship it fast</h3>
      <p>Move from draft to live in minutes, not sprints.</p>
    </div>
  </div>
</section>
<style>
.gjs-feat-alt { padding: 72px 48px; display: flex; flex-direction: column; gap: 64px; background: #fff; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-feat-alt .gjs-feat-alt-row { display: flex; align-items: center; gap: 48px; }
.gjs-feat-alt .gjs-feat-alt-reverse { flex-direction: row-reverse; }
.gjs-feat-alt .gjs-feat-alt-media { flex: 1; min-width: 240px; }
.gjs-feat-alt .gjs-feat-alt-media img { width: 100%; height: auto; border-radius: 12px; display: block; }
.gjs-feat-alt .gjs-feat-alt-text { flex: 1; min-width: 240px; }
.gjs-feat-alt h3 { color: #111827; font-size: 1.6rem; margin: 0 0 12px; }
.gjs-feat-alt p { color: #6b7280; font-size: 1rem; line-height: 1.6; margin: 0; max-width: 420px; }
@media (max-width: 768px) {
  .gjs-feat-alt { padding: 56px 24px; gap: 40px; }
  .gjs-feat-alt .gjs-feat-alt-row, .gjs-feat-alt .gjs-feat-alt-reverse { flex-direction: column; text-align: center; }
}
</style>`,
        });

        editor.BlockManager.add('feature-steps', {
            label: 'Feature (Numbered Steps)',
            category: 'Features',
            content: `
<section class="gjs-feat gjs-feat-steps">
  <h2>How it works</h2>
  <div class="gjs-feat-steps-row">
    <div class="gjs-feat-step">
      <div class="gjs-feat-step-num">1</div>
      <h3>Sign up</h3>
      <p>Create your account in under a minute, no credit card required.</p>
    </div>
    <div class="gjs-feat-step">
      <div class="gjs-feat-step-num">2</div>
      <h3>Set up</h3>
      <p>Import your data or start fresh with one of our templates.</p>
    </div>
    <div class="gjs-feat-step">
      <div class="gjs-feat-step-num">3</div>
      <h3>Go live</h3>
      <p>Publish and share, changes go out instantly.</p>
    </div>
  </div>
</section>
<style>
.gjs-feat-steps { padding: 80px 48px; background: #f9fafb; text-align: center; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-feat-steps h2 { color: #111827; font-size: 1.8rem; margin: 0 0 40px; }
.gjs-feat-steps .gjs-feat-steps-row { display: flex; gap: 40px; justify-content: center; }
.gjs-feat-steps .gjs-feat-step { flex: 1; max-width: 260px; }
.gjs-feat-steps .gjs-feat-step-num { width: 40px; height: 40px; border-radius: 50%; background: #2563eb; color: #fff; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
.gjs-feat-steps h3 { color: #111827; font-size: 1.1rem; margin: 0 0 8px; }
.gjs-feat-steps p { color: #6b7280; font-size: 0.9rem; line-height: 1.5; margin: 0; }
@media (max-width: 768px) { .gjs-feat-steps .gjs-feat-steps-row { flex-direction: column; align-items: center; } }
</style>`,
        });
    }

    window.registerFeatureBlocks = registerFeatureBlocks;
})();
