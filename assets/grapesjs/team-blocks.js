(function () {
    function registerTeamBlocks(editor) {
        editor.BlockManager.add('team-grid', {
            label: 'Team (Photo Grid)',
            category: 'Team',
            content: `
<section class="gjs-team gjs-team-grid">
  <div class="gjs-team-member"><img src="https://placehold.co/220x220" alt=""><h3>Jamie Ford</h3><p>CEO &amp; Co-founder</p></div>
  <div class="gjs-team-member"><img src="https://placehold.co/220x220" alt=""><h3>Sam Ortiz</h3><p>Head of Product</p></div>
  <div class="gjs-team-member"><img src="https://placehold.co/220x220" alt=""><h3>Riley Chen</h3><p>Lead Engineer</p></div>
  <div class="gjs-team-member"><img src="https://placehold.co/220x220" alt=""><h3>Taylor Wong</h3><p>Design Lead</p></div>
</section>
<style>
.gjs-team-grid { display: flex; gap: 32px; padding: 80px 48px; flex-wrap: wrap; justify-content: center; background: #fff; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-team-grid .gjs-team-member { flex: 1; min-width: 180px; max-width: 220px; text-align: center; }
.gjs-team-grid img { width: 100%; aspect-ratio: 1 / 1; object-fit: cover; border-radius: 50%; margin-bottom: 16px; }
.gjs-team-grid h3 { color: #111827; font-size: 1.05rem; margin: 0 0 4px; }
.gjs-team-grid p { color: #6b7280; font-size: 0.88rem; margin: 0; }
</style>`,
        });

        editor.BlockManager.add('team-bio-cards', {
            label: 'Team (With Bio)',
            category: 'Team',
            content: `
<section class="gjs-team gjs-team-bio">
  <div class="gjs-team-card">
    <img src="https://placehold.co/300x260" alt="">
    <div class="gjs-team-card-body">
      <h3>Morgan Blake</h3>
      <p class="gjs-team-role">Founder &amp; CEO</p>
      <p class="gjs-team-desc">Ten years building developer tools, previously led platform at a Series C startup.</p>
    </div>
  </div>
  <div class="gjs-team-card">
    <img src="https://placehold.co/300x260" alt="">
    <div class="gjs-team-card-body">
      <h3>Casey Nguyen</h3>
      <p class="gjs-team-role">Head of Design</p>
      <p class="gjs-team-desc">Obsessed with the details that make software feel effortless to use.</p>
    </div>
  </div>
  <div class="gjs-team-card">
    <img src="https://placehold.co/300x260" alt="">
    <div class="gjs-team-card-body">
      <h3>Drew Patel</h3>
      <p class="gjs-team-role">Lead Engineer</p>
      <p class="gjs-team-desc">Keeps the whole system fast, reliable, and boring in the best way.</p>
    </div>
  </div>
</section>
<style>
.gjs-team-bio { display: flex; gap: 28px; padding: 80px 48px; background: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-team-bio .gjs-team-card { flex: 1; min-width: 220px; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
.gjs-team-bio img { width: 100%; height: auto; display: block; }
.gjs-team-bio .gjs-team-card-body { padding: 22px; }
.gjs-team-bio h3 { color: #111827; font-size: 1.1rem; margin: 0 0 4px; }
.gjs-team-bio .gjs-team-role { color: #2563eb; font-size: 0.85rem; font-weight: 600; margin: 0 0 12px; }
.gjs-team-bio .gjs-team-desc { color: #6b7280; font-size: 0.9rem; line-height: 1.5; margin: 0; }
@media (max-width: 768px) { .gjs-team-bio { flex-direction: column; padding: 56px 24px; } }
</style>`,
        });

        editor.BlockManager.add('team-social', {
            label: 'Team (With Social Icons)',
            category: 'Team',
            content: `
<section class="gjs-team gjs-team-social-grid">
  <div class="gjs-team-member">
    <img src="https://placehold.co/200x200" alt="">
    <h3>Avery Kim</h3>
    <p>Marketing Lead</p>
    <div class="gjs-team-social-row"><a href="#">X</a><a href="#">In</a></div>
  </div>
  <div class="gjs-team-member">
    <img src="https://placehold.co/200x200" alt="">
    <h3>Jordan Ellis</h3>
    <p>Sales Director</p>
    <div class="gjs-team-social-row"><a href="#">X</a><a href="#">In</a></div>
  </div>
  <div class="gjs-team-member">
    <img src="https://placehold.co/200x200" alt="">
    <h3>Sky Rivera</h3>
    <p>Customer Success</p>
    <div class="gjs-team-social-row"><a href="#">X</a><a href="#">In</a></div>
  </div>
</section>
<style>
.gjs-team-social-grid { display: flex; gap: 32px; flex-wrap: wrap; justify-content: center; padding: 80px 48px; background: #fff; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-team-social-grid .gjs-team-member { text-align: center; min-width: 180px; max-width: 200px; }
.gjs-team-social-grid img { width: 100%; aspect-ratio: 1 / 1; object-fit: cover; border-radius: 50%; margin-bottom: 14px; }
.gjs-team-social-grid h3 { color: #111827; font-size: 1rem; margin: 0 0 4px; }
.gjs-team-social-grid p { color: #6b7280; font-size: 0.85rem; margin: 0 0 12px; }
.gjs-team-social-grid .gjs-team-social-row { display: flex; justify-content: center; gap: 8px; }
.gjs-team-social-grid .gjs-team-social-row a { width: 30px; height: 30px; border-radius: 50%; background: #f3f4f6; color: #374151; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 0.75rem; font-weight: 700; transition: background .15s ease; }
.gjs-team-social-grid .gjs-team-social-row a:hover { background: #2563eb; color: #fff; }
</style>`,
        });

        editor.BlockManager.add('team-list', {
            label: 'Team (Compact List)',
            category: 'Team',
            content: `
<section class="gjs-team gjs-team-list">
  <div class="gjs-team-row"><img src="https://placehold.co/56x56" alt=""><div class="gjs-team-row-info"><h3>Noah Sullivan</h3><p>Backend Engineer</p></div><div class="gjs-team-row-social"><a href="#">In</a></div></div>
  <div class="gjs-team-row"><img src="https://placehold.co/56x56" alt=""><div class="gjs-team-row-info"><h3>Layla Hassan</h3><p>Frontend Engineer</p></div><div class="gjs-team-row-social"><a href="#">In</a></div></div>
  <div class="gjs-team-row"><img src="https://placehold.co/56x56" alt=""><div class="gjs-team-row-info"><h3>Owen Brooks</h3><p>QA Lead</p></div><div class="gjs-team-row-social"><a href="#">In</a></div></div>
</section>
<style>
.gjs-team-list { max-width: 560px; margin: 0 auto; padding: 72px 24px; display: flex; flex-direction: column; gap: 4px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-team-list .gjs-team-row { display: flex; align-items: center; gap: 16px; padding: 14px 0; border-bottom: 1px solid #e5e7eb; }
.gjs-team-list img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }
.gjs-team-list .gjs-team-row-info { flex: 1; }
.gjs-team-list h3 { color: #111827; font-size: 0.98rem; margin: 0; }
.gjs-team-list p { color: #6b7280; font-size: 0.85rem; margin: 2px 0 0; }
.gjs-team-list .gjs-team-row-social a { color: #2563eb; text-decoration: none; font-weight: 600; font-size: 0.85rem; }
</style>`,
        });

        editor.BlockManager.add('team-featured', {
            label: 'Team (Featured Single)',
            category: 'Team',
            content: `
<section class="gjs-team gjs-team-featured">
  <img src="https://placehold.co/280x280" alt="">
  <div class="gjs-team-featured-text">
    <h3>Elena Marsh</h3>
    <p class="gjs-team-role">Founder &amp; CEO</p>
    <p class="gjs-team-desc">Elena started the company after a decade in enterprise software, frustrated by how hard the basics still were. She leads product and strategy today.</p>
    <div class="gjs-team-social-row"><a href="#">X</a><a href="#">In</a><a href="#">Website</a></div>
  </div>
</section>
<style>
.gjs-team-featured { display: flex; align-items: center; gap: 48px; padding: 80px 48px; background: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.gjs-team-featured img { width: 220px; height: 220px; border-radius: 50%; object-fit: cover; flex: none; }
.gjs-team-featured h3 { color: #111827; font-size: 1.6rem; margin: 0 0 4px; }
.gjs-team-featured .gjs-team-role { color: #2563eb; font-weight: 600; margin: 0 0 16px; }
.gjs-team-featured .gjs-team-desc { color: #4b5563; font-size: 1rem; line-height: 1.6; margin: 0 0 20px; max-width: 480px; }
.gjs-team-featured .gjs-team-social-row { display: flex; gap: 20px; }
.gjs-team-featured .gjs-team-social-row a { color: #111827; font-weight: 600; text-decoration: none; font-size: 0.9rem; }
.gjs-team-featured .gjs-team-social-row a:hover { color: #2563eb; }
@media (max-width: 768px) { .gjs-team-featured { flex-direction: column; text-align: center; padding: 56px 24px; } .gjs-team-featured .gjs-team-social-row { justify-content: center; } }
</style>`,
        });
    }

    window.registerTeamBlocks = registerTeamBlocks;
})();
