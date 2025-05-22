<?php if(user_can('view_pages')): ?>
<div class="table-responsive">
    <!-- Search Form -->
    <form class="input-group my-3 mx-auto" method="get" action="">
        <input placeholder="Search by title" type="text" name="search" class="form-control" value="<?= esc($_GET['search'] ?? '') ?>">
        <button class="input-group-text bg-primary text-white" id="basic-addon1">
            Search
        </button>
    </form>

    <!-- Data Table -->
    <table class="table table-striped table-bordered">
        <thead>
            <tr class="text-center">
                <th>#</th>
                <th>Title</th>
                <th>Description</th>
                <th>Slug</th>
                <th>Views</th>
                <th>Active</th>
                <th>Date Created</th>
                <?php if(user_can('add_page')): ?>
                    <th class="text-start">
                        <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/add">
                            <button class="btn btn-bd-primary btn-sm">
                                <i class="bi bi-plus-lg"></i> New Page
                            </button>
                        </a>
                    </th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rows)): ?>
                <?php foreach ($rows as $row): ?>
                    <tr class="align-middle text-center">
                        <td><?= esc($row->id ?? 'N/A') ?></td>
                        <td class="text-start">
                            <a target="_blank" href="<?= ROOT ?>/<?= $row->slug ?>">
                                <?= esc($row->title ?? 'N/A') ?>
                            </a>
                        </td>
                        <td><?= esc($row->description ?? '-') ?></td>
                        <td><?= esc($row->slug ?? '-') ?></td>
                        <td><?= esc($row->views ?? 0) ?></td>
                        <td><?= esc($row->disabled ? 'no' : 'yes') ?></td>
                        <td><?= esc($row->date_created ?? '-') ?></td>
                        <td>
                            <?php if(user_can('view_page')): ?>
                                <a target="_blank" href="<?= ROOT ?>/<?= $row->slug ?>">
                                    <button class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye-fill"></i> View
                                    </button>
                                </a>
                            <?php endif; ?>
                            <?php if(user_can('edit_page')): ?>
                                <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/edit/<?= $row->id ?>">
                                    <button class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-fill"></i> Edit
                                    </button>
                                </a>
                            <?php endif; ?>
                            <?php if(user_can('delete_page')): ?>
                                <a href="<?=ROOT?>/<?=$admin_route?>/<?=$plugin_route?>/delete/<?=$row->id?>">
                                <button class="btn btn-sm btn-danger">
                                <i class="bi bi-trash3-fill"></i> Delete
                                </button></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="100%" class="text-center text-muted">No pages found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?= $pager->display() ?>

<?php else: ?>
<!-- Access Denied -->
<div id="denied" class="card text-center shadow-lg border-danger mx-auto" style="max-width: 400px;">
    <div class="card-body">
        <h5 class="card-title text-danger fw-bold">Access Denied</h5>
        <p class="card-text text-muted">You don't have permission for this action.</p>
    </div>
</div>
<?php endif ?>
