<?php if (user_can('view_settings')): ?>
    <script src="<?= plugin_http_path('assets/js/plugin.js') ?>"></script>
    <link rel="stylesheet" href="<?= plugin_http_path('assets/css/style.css') ?>">

    <div class="container card shadow mt-6 p-4">
        <form method="POST" enctype="multipart/form-data">
			<input type="hidden" name="_token" value="<?= csrf() ?>">
            <label>
                <span>Site Title</span>
                <input type="text" name="site_name" value="<?= setting('site_name') ?>">
            </label>

            <label>
                <span>Description</span>
                <input type="text" name="site_description" value="<?= setting('site_description') ?>">
            </label>

            <label>
                <span>Site URL</span>
                <input type="url" name="site_url" value="<?= setting('site_url') ?>">
            </label>

<label>
    <span>Home Page</span>
    <select name="site_homepage">
        <?php foreach ($pages as $page): ?>
            <option value="<?= esc($page->slug) ?>" <?= setting('site_homepage') === $page->slug ? 'selected' : '' ?>>
                <?= esc($page->title) ?>
            </option>
        <?php endforeach; ?>
    </select>
</label>
			
            <label>
                <span>Admin Email</span>
                <input type="email" name="admin_email" value="<?= setting('admin_email') ?>">
            </label>

            <label>
                <span>Max Upload Size</span>
				<input type="number" name="max_upload_size" value="<?= (int) setting('max_upload_size') ?>">
            </label>

            <label>
                <span>Pagination Limit</span>
				<input type="number" name="pagination_limit" value="<?= (int) setting('pagination_limit') ?>">
            </label>

			<label>
				<span>Debug Mode</span>
				<input type="checkbox" name="debug_mode" value="1" <?= (bool) setting('debug_mode') ? 'checked' : '' ?>>
			</label>

            <button type="submit">Save Settings</button>
        </form>
    </div>

<?php else: ?>
    <div id="denied" class="card text-center shadow-lg border-danger mx-auto" style="max-width: 400px;">
        <div class="card-body">
            <h5 class="card-title text-danger fw-bold">Access Denied</h5>
            <p class="card-text text-muted">You don't have permission for this action.</p>
        </div>
    </div>
<?php endif; ?>
