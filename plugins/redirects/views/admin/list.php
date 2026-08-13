<?php if (user_can('view_redirects')): ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Redirects</h4>
    <?php if (user_can('add_redirect')): ?>
        <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/add">
            <button class="btn btn-bd-primary btn-sm"><i class="bi bi-plus-lg"></i> New Redirect</button>
        </a>
    <?php endif; ?>
</div>

<div class="table-responsive mb-4">
    <table class="table table-striped table-bordered">
        <thead>
        <tr class="text-center">
            <th>#</th>
            <th>From</th>
            <th>To</th>
            <th>Type</th>
            <th>Status</th>
            <th>Hits</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <tr class="align-middle text-center">
                    <td><?= esc($row->id) ?></td>
                    <td>/<?= esc($row->from_path) ?></td>
                    <td><?= esc($row->to_path) ?></td>
                    <td><?= esc($row->redirect_type) ?></td>
                    <td>
                        <?php if (empty($row->disabled)): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-warning">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($row->hits) ?></td>
                    <td>
                        <?php if (user_can('edit_redirect')): ?>
                            <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/edit/<?= $row->id ?>">
                                <button class="btn btn-sm btn-warning"><i class="bi bi-pencil-fill"></i> Edit</button>
                            </a>
                        <?php endif; ?>
                        <?php if (user_can('delete_redirect')): ?>
                            <form method="post" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/delete/<?= $row->id ?>" class="d-inline" onsubmit="return confirm('Delete this redirect?');">
                                <input type="hidden" name="_token" value="<?= csrf() ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash3-fill"></i> Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="100%" class="text-center text-muted">No redirects yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $pager->display() ?>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">Recent 404s</h5>
    <form method="post" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/clear-log" onsubmit="return confirm('Clear the 404 log?');">
        <input type="hidden" name="_token" value="<?= csrf() ?>">
        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-trash3"></i> Clear Log</button>
    </form>
</div>
<p class="text-muted small">Unmatched URLs visitors have actually hit - use these to decide what to redirect next.</p>
<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
        <tr class="text-center">
            <th>URL</th>
            <th>Hits</th>
            <th>Last Seen</th>
            <?php if (user_can('add_redirect')): ?><th>Actions</th><?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($recentNotFound)): ?>
            <?php foreach ($recentNotFound as $log): ?>
                <tr class="align-middle text-center">
                    <td>/<?= esc($log->url) ?></td>
                    <td><?= esc($log->hits) ?></td>
                    <td><?= esc($log->last_seen) ?></td>
                    <?php if (user_can('add_redirect')): ?>
                        <td>
                            <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/add?from=<?= urlencode($log->url) ?>">
                                <button class="btn btn-sm btn-outline-primary">Create Redirect</button>
                            </a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="100%" class="text-center text-muted">No 404s logged yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
    <div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;">
        <div class="card-body">
            <h5 class="card-title text-danger fw-bold">Access Denied</h5>
            <p class="card-text text-muted">You don't have permission for this action.</p>
        </div>
    </div>
<?php endif; ?>
