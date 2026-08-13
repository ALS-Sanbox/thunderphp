<?php if (user_can('edit_redirect') && !empty($row)): ?>
<div class="container card shadow mt-4 p-4" style="max-width:600px;">
    <h4 class="mb-3">Edit Redirect</h4>
    <form method="POST" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/edit/<?= $row->id ?>">
        <input type="hidden" name="_token" value="<?= csrf() ?>">

        <div class="mb-3">
            <label for="from_path" class="form-label">From Path</label>
            <div class="input-group">
                <span class="input-group-text">/</span>
                <input type="text" class="form-control" id="from_path" name="from_path" value="<?= esc($row->from_path) ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="to_path" class="form-label">To Path (or full URL)</label>
            <input type="text" class="form-control" id="to_path" name="to_path" value="<?= esc($row->to_path) ?>" required>
        </div>

        <div class="mb-3">
            <label for="redirect_type" class="form-label">Redirect Type</label>
            <select class="form-select" id="redirect_type" name="redirect_type">
                <option value="301" <?= $row->redirect_type === '301' ? 'selected' : '' ?>>301 (Permanent)</option>
                <option value="302" <?= $row->redirect_type === '302' ? 'selected' : '' ?>>302 (Temporary)</option>
            </select>
        </div>

        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="active" name="active" value="1" <?= empty($row->disabled) ? 'checked' : '' ?>>
            <label class="form-check-label" for="active">Active</label>
        </div>

        <button type="submit" class="btn btn-primary">Update Redirect</button>
    </form>
</div>
<?php else: ?>
<div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;">
    <div class="card-body">
        <h5 class="card-title text-danger fw-bold">Access Denied</h5>
        <p class="card-text text-muted">You don't have permission for this action, or the redirect no longer exists.</p>
    </div>
</div>
<?php endif; ?>
