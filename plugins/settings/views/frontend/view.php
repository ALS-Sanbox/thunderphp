<?php if (user_can('view_settings')): ?>
    <script src="<?= plugin_http_path('assets/js/plugin.js') ?>"></script>
    <link rel="stylesheet" href="<?= plugin_http_path('assets/css/style.css') ?>">

    <div class="container card shadow mt-6 p-4">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?= csrf() ?>">

            <div class="mb-3">
                <label class="form-label" for="site_name">Site Title</label>
                <input type="text" class="form-control" id="site_name" name="site_name" value="<?= esc(setting('site_name')) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="site_description">Description</label>
                <input type="text" class="form-control" id="site_description" name="site_description" value="<?= esc(setting('site_description')) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="site_url">Site URL</label>
                <input type="url" class="form-control" id="site_url" name="site_url" value="<?= esc(setting('site_url')) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="site_logo">Site Logo</label>
                <input type="file" class="form-control" id="site_logo" name="site_logo" accept="image/*">
                <?php if (setting('site_logo') && file_exists(setting('site_logo'))): ?>
                    <div><img src="<?= get_image(setting('site_logo')) ?>" alt="" style="max-height:60px;margin-top:8px;"></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label" for="site_homepage">Home Page</label>
                <select class="form-select" id="site_homepage" name="site_homepage">
                    <?php foreach ($pages as $page): ?>
                        <option value="<?= esc($page->slug) ?>" <?= setting('site_homepage') === $page->slug ? 'selected' : '' ?>>
                            <?= esc($page->title) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label" for="admin_email">Admin Email</label>
                <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?= esc(setting('admin_email')) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="max_upload_size">Max Upload Size</label>
                <input type="number" class="form-control" id="max_upload_size" name="max_upload_size" value="<?= (int) setting('max_upload_size') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="pagination_limit">Pagination Limit</label>
                <input type="number" class="form-control" id="pagination_limit" name="pagination_limit" value="<?= (int) setting('pagination_limit') ?>">
            </div>

            <div class="mb-3 form-check form-switch">
                <input type="checkbox" class="form-check-input" id="debug_mode" name="debug_mode" value="1" <?= (bool) setting('debug_mode') ? 'checked' : '' ?>>
                <label class="form-check-label" for="debug_mode">Debug Mode</label>
            </div>

            <h3 class="h5 mt-4">Outgoing Mail (SMTP)</h3>
            <p class="text-muted">Leave the host blank to send mail with the server's local mail() function. Fill these in to send through a real SMTP provider (Gmail, SendGrid, etc.) instead.</p>

            <div class="mb-3">
                <label class="form-label" for="smtp_host">SMTP Host</label>
                <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="<?= esc(setting('smtp_host')) ?>" placeholder="smtp.example.com">
            </div>

            <div class="mb-3">
                <label class="form-label" for="smtp_port">SMTP Port</label>
                <input type="number" class="form-control" id="smtp_port" name="smtp_port" value="<?= (int) setting('smtp_port') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="smtp_encryption">Encryption</label>
                <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                    <?php foreach (['tls' => 'STARTTLS (587)', 'ssl' => 'Implicit TLS (465)', 'none' => 'None (25)'] as $value => $label): ?>
                        <option value="<?= $value ?>" <?= setting('smtp_encryption') === $value ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label" for="smtp_username">SMTP Username</label>
                <input type="text" class="form-control" id="smtp_username" name="smtp_username" value="<?= esc(setting('smtp_username')) ?>" autocomplete="off">
            </div>

            <div class="mb-3">
                <label class="form-label" for="smtp_password">SMTP Password</label>
                <input type="password" class="form-control" id="smtp_password" name="smtp_password" value="" autocomplete="new-password" placeholder="<?= setting('smtp_password') ? 'Unchanged' : '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="smtp_from_email">From Email</label>
                <input type="email" class="form-control" id="smtp_from_email" name="smtp_from_email" value="<?= esc(setting('smtp_from_email')) ?>" placeholder="<?= esc(setting('admin_email')) ?>">
            </div>

            <div class="mb-4">
                <label class="form-label" for="smtp_from_name">From Name</label>
                <input type="text" class="form-control" id="smtp_from_name" name="smtp_from_name" value="<?= esc(setting('smtp_from_name')) ?>" placeholder="<?= esc(setting('site_name')) ?>">
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
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
