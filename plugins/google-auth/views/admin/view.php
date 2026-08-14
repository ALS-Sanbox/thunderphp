<?php if (user_can('manage_google_auth')): ?>
<div class="container card shadow mt-4 p-4">
    <h4 class="mb-3">Google Sign-In</h4>

    <div class="mb-3">
        <label class="form-label">Callback URL (register this exactly in Google Cloud Console)</label>
        <input type="text" class="form-control" readonly value="<?= esc(google_auth_redirect_uri()) ?>" onclick="this.select()">
    </div>

    <form method="POST">
        <input type="hidden" name="_token" value="<?= csrf() ?>">

        <div class="mb-3">
            <label class="form-label" for="google_oauth_client_id">Client ID</label>
            <input type="text" class="form-control" id="google_oauth_client_id" name="google_oauth_client_id" value="<?= esc(setting('google_oauth_client_id')) ?>" autocomplete="off">
        </div>

        <div class="mb-3">
            <label class="form-label" for="google_oauth_client_secret">Client Secret</label>
            <input type="password" class="form-control" id="google_oauth_client_secret" name="google_oauth_client_secret" value="" autocomplete="new-password" placeholder="<?= setting('google_oauth_client_secret') ? 'Unchanged' : '' ?>">
        </div>

        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="google_oauth_enabled" name="google_oauth_enabled" value="1" <?= setting('google_oauth_enabled') ? 'checked' : '' ?>>
            <label class="form-check-label" for="google_oauth_enabled">Enabled (shows the "Sign in with Google" button on the login page)</label>
        </div>

        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>

    <?php if (!setting('google_oauth_client_id') || !setting('google_oauth_client_secret')): ?>
        <div class="alert alert-info mt-4 mb-0">
            Enter a Client ID and Client Secret from Google Cloud Console to enable Google Sign-In.
        </div>
    <?php endif; ?>
</div>
<?php else: ?>
<div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;">
    <div class="card-body">
        <h5 class="card-title text-danger fw-bold">Access Denied</h5>
        <p class="card-text text-muted">You don't have permission for this action.</p>
    </div>
</div>
<?php endif; ?>
