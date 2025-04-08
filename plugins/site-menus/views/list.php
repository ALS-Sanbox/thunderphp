<?php if(user_can('view_menus')):?>
<div class="table-responsive">
    <form class="input-group my-3 mx-auto">
        <input placeholder="Search by title" type="text" class="form-control" value="">
        <button class="input-group-text bg-primary text-white" id="basic-addon1">
            Search
        </button>
    </form>

    <table class="table table-striped table-bordered">
        <tr class="text-center">
            <th>#</th>
            <th>Title</th>
            <th>Parent</th>
            <th>Image</th>
            <th>Mega Image</th>
            <th>Active</th>
            <th>Is Mega</th>
            <th>Permission</th>
            <th>Slug</th>
            <?php if(user_can('add_menu')): ?>
                <th class="text-start">
                    <a href="<?=ROOT?>/<?=$admin_route?>/<?=$plugin_route?>/add">
                        <button class="btn btn-bd-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> New Item
                        </button>
                    </a>
                </th>
            <?php endif; ?>
        </tr>
        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <tr class="align-middle">
                    <td><?= esc($row->id ?? 'N/A') ?></td>
                    <td><a href="<?=ROOT?>/<?=$admin_route?>/<?=$plugin_route?>/view/<?=$row->id?>"><?= esc($row->title ?? 'N/A') ?></a></td>
                    <td>
                        <?php 
                            $parentName = 'None';
                            if (!empty($rows) && $row->parent) {
                                foreach ($rows as $item) {
                                    if ($item->id == $row->parent) {
                                        $parentName = $item->title;
                                        break;
                                    }
                                }
                            }
                            echo esc($parentName);
                        ?>
                    </td>
                    <td>
                        <center><img src="<?= esc(get_image($row->image)) ?>" class="img-thumbnail" alt="Item Image" style="width:50px;height:50px;object-fit: cover;"></center>
                    </td>
                    <td>
                        <center><img src="<?= esc(get_image($row->mega_image)) ?>" class="img-thumbnail" alt="Mega Image" style="width:50px;height:50px;object-fit: cover;"></center>
                    </td>
                    <td><?= esc($row->disabled ? 'no' : 'yes') ?></td>
                    <td><?= esc($row->is_mega ? 'yes' : 'no') ?></td>
                    <td><?= esc($row->permission ?? 'N/A') ?></td>
                    <td><?= esc($row->slug ?? 'N/A') ?></td>
                    <td>
                        <?php if(user_can('edit_menu')): ?>
                            <a href="<?=ROOT?>/<?=$admin_route?>/<?=$plugin_route?>/edit/<?=$row->id?>">
                                <button class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-fill"></i> Edit Item
                                </button>
                            </a>
                        <?php endif; ?>
                        <?php if(user_can('delete_menu')): ?>
                            <a href="<?=ROOT?>/<?=$admin_route?>/<?=$plugin_route?>/delete/<?=$row->id?>">
                                <button class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash3-fill"></i> Delete Item
                                </button>
                            </a>
                        <?php endif; ?>
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