<?php if(user_can('view_users')):?>
<div class="table-responsive">
    <!-- Search Form -->
    <form class="input-group my-3 mx-auto" method="get" action="">
        <label for="users-search" class="visually-hidden">Search</label>
        <input placeholder="Search" type="text" id="users-search" name="find" class="form-control" value="<?= esc($_GET['find'] ?? '') ?>">
        <button class="input-group-text bg-primary text-white" id="basic-addon1">
            Search
        </button>
    </form>

    <table class="table table-striped table-bordered">
        <thead>
        <tr class="text-center">
            <th>#</th>
            <th>Image</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>E-Mail</th>
            <th>Roles</th>
            <?php if(user_can('add_user')):?>
            <th class="text-start">
                <a href="<?=ROOT?>/<?=$admin_route?>/<?=$plugin_route?>/add">
                    <button class="btn btn-bd-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> New User
                    </button>
                </a>
            </th>
            <?php endif?>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <tr class="align-middle text-center">
                    <td><?= esc($row->id ?? 'N/A') ?></td>
                    <td class="text-center">
                        <img src="<?= esc( get_image($row->image)) ?>" class="img-thumbnail" alt="User Image" style="width:50px;height:50px;object-fit: cover;">
                    </td>
                    <td><?= esc($row->first_name ?? 'N/A') ?></td>
                    <td><?= esc($row->last_name ?? 'N/A') ?></td>
                    <td><?= esc($row->email ?? 'N/A') ?></td>
                    <td>
                        <?php if(!empty($row->roles)) :?>
                            <?php foreach($row->roles as $role) :?>
                                <div>
                                    <i> <?= esc($role) ?> </i>
                                </div>
                            <?php endforeach;?>
                        <?php endif;?>
                    </td>
                    <td>
                    <?php if(user_can('view_user_detail')):?>
                        <a href="<?=ROOT?>/<?=$admin_route?>/<?=$plugin_route?>/view/<?=$row->id?>">
                        <button class="btn btn-sm btn-primary">
                        <i class="bi bi-binoculars-fill"></i> View User
                        </button></a>
                    <?php endif?>
                    <?php if(user_can('edit_user')):?>
                        <a href="<?=ROOT?>/<?=$admin_route?>/<?=$plugin_route?>/edit/<?=$row->id?>">
                        <button class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil-fill"></i> Edit User
                        </button></a>
                    <?php endif?>
                    <?php if(user_can('delete_user')):?>
                        <a href="<?=ROOT?>/<?=$admin_route?>/<?=$plugin_route?>/delete/<?=$row->id?>">
                        <button class="btn btn-sm btn-danger">
                        <i class="bi bi-trash3-fill"></i> Delete User
                        </button></a>
                    <?php endif?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="100%" class="text-center text-muted">No users found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
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