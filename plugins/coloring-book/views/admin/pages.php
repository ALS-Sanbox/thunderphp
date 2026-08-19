<?php if (user_can('manage_coloring_pages')): ?>
<div class="d-flex justify-content-between align-items-center mb-1">
    <h4 class="mb-0"><?= esc($book->title) ?></h4>
    <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>" class="btn btn-outline-secondary btn-sm">&larr; All Coloring Books</a>
</div>
<p class="text-muted mb-3">Coloring Pages</p>

<div class="d-flex justify-content-end mb-3">
    <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/pages/<?= $book->id ?>/add">
        <button class="btn btn-bd-primary btn-sm"><i class="bi bi-plus-lg"></i> Add Coloring Page</button>
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
        <tr class="text-center">
            <th>Order</th>
            <th>Thumbnail</th>
            <th>Title</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $index => $row): ?>
                <tr class="align-middle text-center">
                    <td>
                        <div class="d-flex flex-column align-items-center gap-1">
                            <form method="post" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/pages/<?= $book->id ?>/move-up/<?= $row->id ?>">
                                <input type="hidden" name="_token" value="<?= csrf() ?>">
                                <button type="submit" class="btn btn-sm btn-outline-secondary" <?= $index === 0 ? 'disabled' : '' ?> title="Move up"><i class="bi bi-arrow-up"></i></button>
                            </form>
                            <form method="post" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/pages/<?= $book->id ?>/move-down/<?= $row->id ?>">
                                <input type="hidden" name="_token" value="<?= csrf() ?>">
                                <button type="submit" class="btn btn-sm btn-outline-secondary" <?= $index === count($rows) - 1 ? 'disabled' : '' ?> title="Move down"><i class="bi bi-arrow-down"></i></button>
                            </form>
                        </div>
                    </td>
                    <td>
                        <?php if (!empty($row->thumbnail_path) && file_exists($row->thumbnail_path)): ?>
                            <img src="<?= get_image($row->thumbnail_path) ?>" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:4px;">
                        <?php else: ?>
                            <span class="text-muted small">No thumbnail</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-start">
                        <?= $index + 1 ?>. <strong><?= esc($row->title) ?></strong><br>
                        <small class="text-muted">/<?= esc($row->slug) ?></small>
                    </td>
                    <td>
                        <?php if ($row->status === 'published'): ?>
                            <span class="badge bg-success">Published</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Draft</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/pages/<?= $book->id ?>/edit/<?= $row->id ?>">
                            <button class="btn btn-sm btn-warning"><i class="bi bi-pencil-fill"></i> Edit</button>
                        </a>
                        <form method="post" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/pages/<?= $book->id ?>/delete/<?= $row->id ?>" class="d-inline" onsubmit="return confirm('Delete this coloring page?');">
                            <input type="hidden" name="_token" value="<?= csrf() ?>">
                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash3-fill"></i> Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="100%" class="text-center text-muted">No pages in this coloring book yet.</td></tr>
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
