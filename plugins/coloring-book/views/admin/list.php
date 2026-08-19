<?php if (user_can('view_coloring_books')): ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Coloring Books</h4>
    <?php if (user_can('create_coloring_books')): ?>
        <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/add">
            <button class="btn btn-bd-primary btn-sm"><i class="bi bi-plus-lg"></i> New Coloring Book</button>
        </a>
    <?php endif; ?>
</div>

<div class="table-responsive mb-4">
    <table class="table table-striped table-bordered">
        <thead>
        <tr class="text-center">
            <th>#</th>
            <th>Title</th>
            <th>Pages</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <tr class="align-middle text-center">
                    <td><?= esc($row->id) ?></td>
                    <td class="text-start">
                        <strong><?= esc($row->title) ?></strong><br>
                        <small class="text-muted">/<?= esc($row->slug) ?></small>
                    </td>
                    <td><?= (int) $books->pageCount($row->id) ?> Pages</td>
                    <td>
                        <?php if ($row->status === 'published'): ?>
                            <span class="badge bg-success">Published</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Draft</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (user_can('edit_coloring_books')): ?>
                            <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/edit/<?= $row->id ?>">
                                <button class="btn btn-sm btn-warning"><i class="bi bi-pencil-fill"></i> Edit</button>
                            </a>
                        <?php endif; ?>
                        <?php if (user_can('manage_coloring_pages')): ?>
                            <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/pages/<?= $row->id ?>">
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-images"></i> Manage Pages</button>
                            </a>
                        <?php endif; ?>
                        <?php if ($row->status === 'published'): ?>
                            <a href="<?= ROOT ?>/coloring-book/api/<?= esc($row->slug) ?>" target="_blank" rel="noopener">
                                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> Preview</button>
                            </a>
                        <?php endif; ?>
                        <?php if (user_can('delete_coloring_books')): ?>
                            <form method="post" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/delete/<?= $row->id ?>" class="d-inline" onsubmit="return confirm('Delete this coloring book and all of its pages? This cannot be undone.');">
                                <input type="hidden" name="_token" value="<?= csrf() ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash3-fill"></i> Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="100%" class="text-center text-muted">No coloring books yet.</td></tr>
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
