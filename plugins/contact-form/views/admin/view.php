<?php if (user_can('view_contact_submissions')): ?>
<div class="container-fluid mt-3">
    <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left"></i> Back to Submissions
    </a>

    <?php if (empty($row)): ?>
        <div class="alert alert-danger">Submission not found.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-header">
                <strong><?= esc($row->subject ?: '(no subject)') ?></strong>
                <span class="text-muted float-end"><?= esc($row->date_created) ?></span>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?= esc($row->name) ?></p>
                <p><strong>Email:</strong> <a href="mailto:<?= esc($row->email) ?>"><?= esc($row->email) ?></a></p>
                <?php if (!empty($row->ip_address)): ?>
                    <p><strong>IP Address:</strong> <?= esc($row->ip_address) ?></p>
                <?php endif; ?>
                <hr>
                <p style="white-space:pre-wrap;"><?= esc($row->message) ?></p>
            </div>
            <?php if (user_can('delete_contact_submissions')): ?>
                <div class="card-footer">
                    <form method="post" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/delete/<?= $row->id ?>" onsubmit="return confirm('Delete this submission?');">
                        <input type="hidden" name="_token" value="<?= csrf() ?>">
                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash3-fill"></i> Delete</button>
                    </form>
                </div>
            <?php endif; ?>
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
