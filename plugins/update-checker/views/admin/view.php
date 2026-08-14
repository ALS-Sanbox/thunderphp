<?php if (user_can('manage_updates')): ?>
<div class="container card shadow mt-4 p-4">
    <h4 class="mb-3">Updates</h4>

    <table class="table table-borderless mb-4" style="max-width:500px;">
        <tr>
            <th>Current version</th>
            <td>v<?= esc(app_version()) ?></td>
        </tr>
        <tr>
            <th>Latest known version</th>
            <td>
                <?php if (setting('update_check_latest_version')): ?>
                    v<?= esc(setting('update_check_latest_version')) ?>
                    <?php if (version_compare((string) setting('update_check_latest_version'), app_version(), '>')): ?>
                        <span class="badge bg-info ms-1">Update available</span>
                    <?php else: ?>
                        <span class="badge bg-success ms-1">Up to date</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="text-muted">Not checked yet</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Last checked</th>
            <td><?= esc(setting('update_check_last_checked_at') ?: 'Never') ?></td>
        </tr>
        <?php if (setting('update_check_latest_url')): ?>
        <tr>
            <th>Release page</th>
            <td><a href="<?= esc(setting('update_check_latest_url')) ?>" target="_blank" rel="noopener"><?= esc(setting('update_check_latest_url')) ?></a></td>
        </tr>
        <?php endif; ?>
    </table>

    <form method="POST" class="mb-3">
        <input type="hidden" name="_token" value="<?= csrf() ?>">
        <input type="hidden" name="action" value="check_now">
        <button type="submit" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-clockwise"></i> Check Now
        </button>
    </form>

    <form method="POST">
        <input type="hidden" name="_token" value="<?= csrf() ?>">
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="update_check_enabled" name="update_check_enabled" value="1" <?= setting('update_check_enabled') ? 'checked' : '' ?>>
            <label class="form-check-label" for="update_check_enabled">Automatically check for new ThunderPHP releases (once a day)</label>
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>

    <p class="text-muted small mt-4 mb-0">This only checks GitHub for a newer release and links to it - it never downloads or applies anything automatically.</p>
</div>
<?php else: ?>
<div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;">
    <div class="card-body">
        <h5 class="card-title text-danger fw-bold">Access Denied</h5>
        <p class="card-text text-muted">You don't have permission for this action.</p>
    </div>
</div>
<?php endif; ?>
