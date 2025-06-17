<?php if(user_can('view_categories')):?>
<div class="table-responsive">
    <!-- Search Form -->
    <form class="input-group my-3 mx-auto" method="get" action="">
        <input placeholder="Search" type="text" name="search" class="form-control" value="<?= esc($_GET['search'] ?? '') ?>">
        <button class="input-group-text bg-primary text-white" id="basic-addon1">
            Search
        </button>
    </form>
    <table class="table table-striped table-bordered">
        <tr class="text-center">
            <th>#</th>
            <th>Category Name</th>
            <th>Slug</th>
            <th>Active</th>

            <?php if(user_can('add_category')):?>
            <th class="text-start">
                <a href="<?=ROOT?>/<?=$admin_route?>/<?=$plugin_route?>/add">
                    <button class="btn btn-bd-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> New Category
                    </button>
                </a>
            </th>
            <?php endif?>
        </tr>
        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <tr class="align-middle">
                    <td><?= esc($row->id ?? 'N/A') ?></td>
                    <td><?= esc($row->category ?? 'N/A') ?></td>
                    <td><?= esc($row->slug ?? 'N/A') ?></td>
                    <td><?= esc(($row->disabled ?? 0) ? 'No' : 'Yes') ?></td>
                    <td>
                    <?php if(user_can('view_categories')):?>
                        <a href="<?=ROOT?>/<?=$admin_route?>/<?=$plugin_route?>/view/<?=$row->id?>">
                        <button class="btn btn-sm btn-primary">
                        <i class="bi bi-binoculars-fill"></i> View
                        </button></a>
                    <?php endif?>
                    <?php if(user_can('edit_category')):?>
                        <a href="<?=ROOT?>/<?=$admin_route?>/<?=$plugin_route?>/edit/<?=$row->id?>">
                        <button class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil-fill"></i> Edit
                        </button></a>
                    <?php endif?>
                    <?php if(user_can('delete_category')):?>
                        <a href="<?=ROOT?>/<?=$admin_route?>/<?=$plugin_route?>/delete/<?=$row->id?>">
                        <button class="btn btn-sm btn-danger">
                        <i class="bi bi-trash3-fill"></i> Delete
                        </button></a>
                    <?php endif?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

    </table>
</div>
<?=$pager->display()?>
<?php else:?>
    <div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;"> 

        <div class="card-body">
            <h5 class="card-title text-danger fw-bold">Access Denied</h5>
            <p class="card-text text-muted">You don't have permission for this action.</p>
        </div>
    </div>
<?php endif?>