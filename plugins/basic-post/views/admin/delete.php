<?php if(user_can('delete_page')):?>
    <?php if (!empty($row) && !empty($row->id)): ?>
        <div class="container card shadow mt-6 p-4">
            <form action="delete_page.php" method="post" class="text-center">
                <p>Are you sure you want to delete this Page?</p>
                <table class="table text-center">
                    <tr>
                        <td><strong><?= esc($row->title ?? 'N/A') ?></strong></td>
                    </tr>
                </table>
                <br>
                <input type="hidden" name="page_id" value="<?= esc($row->id) ?>">
                <input type="hidden" name="_token" value="<?= csrf() ?>">
                <button type="submit" name="confirm" class="btn btn-danger"><i class="bi bi-trash3-fill"></i> Confirm</button>
                <button type="button" onclick="window.history.back();" class="btn btn-secondary">Cancel</button>
            </form>
        </div>
        <?php else: ?>
            <div class="alert alert-danger text-center"><?= esc($row->id) ?> That Page was not found!</div>
            <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>">
                <button class="btn btn-sm btn-success">
                    <i class="bi bi-arrow-bar-left"></i> Back
                </button>
            </a>
        <?php endif; ?>
<?php else:?>
    <div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;"> 

        <div class="card-body">
            <h5 class="card-title text-danger fw-bold">Access Denied</h5>
            <p class="card-text text-muted">You don't have permission for this action.</p>
        </div>
    </div>
<?php endif?>