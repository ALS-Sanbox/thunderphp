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

            <h3>Outgoing Mail (SMTP)</h3>
            <p>Leave the host blank to send mail with the server's local mail() function. Fill these in to send through a real SMTP provider (Gmail, SendGrid, etc.) instead.</p>

            <label>
                <span>SMTP Host</span>
                <input type="text" name="smtp_host" value="<?= esc(setting('smtp_host')) ?>" placeholder="smtp.example.com">
            </label>

            <label>
                <span>SMTP Port</span>
                <input type="number" name="smtp_port" value="<?= (int) setting('smtp_port') ?>">
            </label>

            <label>
                <span>Encryption</span>
                <select name="smtp_encryption">
                    <?php foreach (['tls' => 'STARTTLS (587)', 'ssl' => 'Implicit TLS (465)', 'none' => 'None (25)'] as $value => $label): ?>
                        <option value="<?= $value ?>" <?= setting('smtp_encryption') === $value ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                <span>SMTP Username</span>
                <input type="text" name="smtp_username" value="<?= esc(setting('smtp_username')) ?>" autocomplete="off">
            </label>

            <label>
                <span>SMTP Password</span>
                <input type="password" name="smtp_password" value="" autocomplete="new-password" placeholder="<?= setting('smtp_password') ? 'Unchanged' : '' ?>">
            </label>

            <label>
                <span>From Email</span>
                <input type="email" name="smtp_from_email" value="<?= esc(setting('smtp_from_email')) ?>" placeholder="<?= esc(setting('admin_email')) ?>">
            </label>

            <label>
                <span>From Name</span>
                <input type="text" name="smtp_from_name" value="<?= esc(setting('smtp_from_name')) ?>" placeholder="<?= esc(setting('site_name')) ?>">
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
