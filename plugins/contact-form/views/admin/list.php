<?php if (user_can('view_contact_submissions')): ?>
<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
        <tr class="text-center">
            <th>#</th>
            <th>Status</th>
            <th>Name</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Received</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <tr class="align-middle text-center">
                    <td><?= esc($row->id) ?></td>
                    <td>
                        <?php if (empty($row->is_read)): ?>
                            <span class="badge bg-warning">Unread</span>
                        <?php else: ?>
                            <span class="badge bg-success">Read</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($row->name) ?></td>
                    <td><?= esc($row->email) ?></td>
                    <td><a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/<?= $row->id ?>"><?= esc($row->subject ?: '(no subject)') ?></a></td>
                    <td><?= esc($row->date_created) ?></td>
                    <td>
                        <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/<?= $row->id ?>">
                            <button class="btn btn-sm btn-primary"><i class="bi bi-eye-fill"></i> View</button>
                        </a>
                        <?php if (user_can('delete_contact_submissions')): ?>
                            <form method="post" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/delete/<?= $row->id ?>" class="d-inline" onsubmit="return confirm('Delete this submission?');">
                                <input type="hidden" name="_token" value="<?= csrf() ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash3-fill"></i> Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="100%" class="text-center text-muted">No submissions yet.</td>
            </tr>
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
