<?php if (user_can('manage_seo')): ?>
<div class="container card shadow mt-4 p-4">
    <h4 class="mb-3">SEO</h4>
    <p class="text-muted">Page titles, descriptions and Open Graph tags are pulled automatically from each page/post's own Title, Description and Keywords fields. The defaults below are used as a fallback wherever those are left blank.</p>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= csrf() ?>">

        <div class="mb-3">
            <label class="form-label" for="seo_default_description">Default Meta Description</label>
            <textarea class="form-control" id="seo_default_description" name="seo_default_description" rows="3"><?= esc(setting('seo_default_description')) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label" for="seo_default_keywords">Default Keywords</label>
            <input type="text" class="form-control" id="seo_default_keywords" name="seo_default_keywords" value="<?= esc(setting('seo_default_keywords')) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label" for="og_image">Default Social Share Image (Open Graph)</label>
            <input type="file" class="form-control" id="og_image" name="og_image" accept="image/*">
            <?php if (setting('seo_default_og_image') && file_exists((string) setting('seo_default_og_image'))): ?>
                <div><img src="<?= get_image((string) setting('seo_default_og_image')) ?>" alt="" style="max-height:80px;margin-top:8px;"></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="seo_robots_txt">Custom robots.txt (leave blank to use the auto-generated default)</label>
            <textarea class="form-control font-monospace" id="seo_robots_txt" name="seo_robots_txt" rows="6" placeholder="User-agent: *&#10;Disallow: /admin"><?= esc(setting('seo_robots_txt')) ?></textarea>
        </div>

        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" id="seo_include_pages" name="seo_include_pages" value="1" <?= setting('seo_include_pages') ? 'checked' : '' ?>>
            <label class="form-check-label" for="seo_include_pages">Include Pages in sitemap.xml</label>
        </div>
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="seo_include_posts" name="seo_include_posts" value="1" <?= setting('seo_include_posts') ? 'checked' : '' ?>>
            <label class="form-check-label" for="seo_include_posts">Include Posts in sitemap.xml</label>
        </div>

        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>

    <hr class="my-4">

    <h5>Sitemap &amp; robots.txt</h5>
    <p class="text-muted mb-2">
        Regenerated automatically whenever a page or post is added, edited or deleted.
        <?php if (setting('seo_sitemap_generated_at')): ?>
            Last generated: <?= esc(setting('seo_sitemap_generated_at')) ?>.
        <?php endif; ?>
    </p>
    <p>
        <a href="<?= ROOT ?>/sitemap.xml" target="_blank" rel="noopener">View sitemap.xml</a>
        &nbsp;|&nbsp;
        <a href="<?= ROOT ?>/robots.txt" target="_blank" rel="noopener">View robots.txt</a>
    </p>
    <form method="POST">
        <input type="hidden" name="_token" value="<?= csrf() ?>">
        <input type="hidden" name="action" value="regenerate">
        <button type="submit" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-counterclockwise"></i> Regenerate Now
        </button>
    </form>
</div>
<?php else: ?>
<div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;">
    <div class="card-body">
        <h5 class="card-title text-danger fw-bold">Access Denied</h5>
        <p class="card-text text-muted">You don't have permission for this action.</p>
    </div>
</div>
<?php endif; ?>
