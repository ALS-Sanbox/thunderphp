<?php if (user_can('manage_updates')): ?>
<?php $updateAvailable = setting('update_check_latest_version') && version_compare((string) setting('update_check_latest_version'), app_version(), '>'); ?>
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
                    <?php if ($updateAvailable): ?>
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

    <form method="POST" class="mb-3 d-inline-block me-2">
        <input type="hidden" name="_token" value="<?= csrf() ?>">
        <input type="hidden" name="action" value="check_now">
        <button type="submit" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-clockwise"></i> Check Now
        </button>
    </form>

    <?php if ($updateAvailable): ?>
    <form method="POST" class="mb-3 d-inline-block" onsubmit="return confirm('Download and apply v<?= esc(setting('update_check_latest_version')) ?> now? A full backup is taken automatically first.');">
        <input type="hidden" name="_token" value="<?= csrf() ?>">
        <input type="hidden" name="action" value="apply_now">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="bi bi-cloud-arrow-down"></i> Apply Update Now
        </button>
    </form>
    <?php endif; ?>

    <?php if (setting('update_check_last_apply_report')): ?>
        <div class="alert alert-secondary mt-3" style="white-space:pre-wrap;font-family:monospace;font-size:0.85em;max-height:300px;overflow:auto;"><?= esc(setting('update_check_last_apply_report')) ?></div>
    <?php endif; ?>

    <form method="POST" class="mt-3">
        <input type="hidden" name="_token" value="<?= csrf() ?>">
        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" id="update_check_enabled" name="update_check_enabled" value="1" <?= setting('update_check_enabled') ? 'checked' : '' ?>>
            <label class="form-check-label" for="update_check_enabled">Automatically check for new ThunderPHP releases (once a day)</label>
        </div>
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="update_check_auto_apply" name="update_check_auto_apply" value="1" <?= setting('update_check_auto_apply') ? 'checked' : '' ?>>
            <label class="form-check-label" for="update_check_auto_apply">
                Automatically download and apply updates when found
                <span class="badge bg-warning text-dark ms-1">Use with caution</span>
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>

    <div class="text-muted small mt-4">
        <p class="mb-1">Applying an update replaces the framework core and every plugin's own files with the new release — never your database, <code>config.php</code>, <code>uploads/</code>, or anything in <code>site-overrides/</code>. A full file + database backup is taken automatically first, saved under <code>update-backups/</code> (not web-accessible).</p>
        <p class="mb-1">"Automatically" here means applied without a click, triggered the next time an admin visits this dashboard after a check finds something new — this app has no background task scheduler. If your host gives you real cron access, you can point it at <code>php thunder do:migrate all</code> after an update instead of relying on a dashboard visit to catch new migrations.</p>
    </div>
</div>
<?php else: ?>
<div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;">
    <div class="card-body">
        <h5 class="card-title text-danger fw-bold">Access Denied</h5>
        <p class="card-text text-muted">You don't have permission for this action.</p>
    </div>
</div>
<?php endif; ?>
