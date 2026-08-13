<?php if (user_can('view_activity_log')): ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Activity Log</h4>
    <?php if (user_can('clear_activity_log')): ?>
        <form method="post" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/clear" onsubmit="return confirm('Clear the entire activity log? This cannot be undone.');">
            <input type="hidden" name="_token" value="<?= csrf() ?>">
            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash3"></i> Clear Log</button>
        </form>
    <?php endif; ?>
</div>
<p class="text-muted small">Records who added, edited or deleted content, based on writes to pages, posts, categories, menus, users, roles, redirects, contact submissions and images.</p>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
        <tr class="text-center">
            <th>Date</th>
            <th>User</th>
            <th>Action</th>
            <th>Table</th>
            <th>IP Address</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <tr class="align-middle text-center">
                    <td><?= esc($row->date_created) ?></td>
                    <td><?= esc($row->username ?? 'Guest') ?></td>
                    <td>
                        <?php
                            $badgeClass = match ($row->action) {
                                'INSERT' => 'bg-success',
                                'UPDATE' => 'bg-warning',
                                'DELETE' => 'bg-danger',
                                default  => 'bg-secondary',
                            };
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= esc($row->action) ?></span>
                    </td>
                    <td><?= esc($row->entity_type) ?></td>
                    <td><?= esc($row->ip_address ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="100%" class="text-center text-muted">No activity recorded yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $pager->display() ?>
<?php else: ?>
    <div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;">
        <div class="card-body">
            <h5 class="card-title text-danger fw-bold">Access Denied</h5>
            <p class="card-text text-muted">You don't have permission for this action.</p>
        </div>
    </div>
<?php endif; ?>
