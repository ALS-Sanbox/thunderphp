<?php if(user_can('edit_user')):?>

<script src="<?=plugin_http_path('assets/js/plugin.js')?>"></script>

<?php if (!empty($row) && !empty($row->id)): ?>
    <div class="container card shadow mt-6 p-4">
        <form method="POST"  enctype="multipart/form-data">
        <h4 class="mb-4">Edit Record</h4>
        <div class="row">
        <div class="col-md-4 text-center">
            <label for="imageUpload" style="cursor: pointer;">
                <img id="previewImage" 
                    src="<?= esc(get_image($row->image)) ?>" 
                    alt="User Image" class="img-fluid rounded-circle" 
                    style="max-width: 200px; height: auto;">
            </label>
            <div class="mt-2 fw-bold"> 
                <?= esc($row->first_name ?? 'N/A') ?> <?= esc($row->last_name ?? 'N/A') ?> 
            </div>
            <input type="hidden" id="currentImage" name="currentImage" value="<?= esc(get_image($row->image)) ?>">
            <input type="file" id="imageUpload" name="image" accept="image/*" class="d-none" onchange="previewFile()">
        </div>

            <div class="col-md-8">
            <input type="hidden" name="_token" value="<?= csrf() ?>">
            <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?= esc($row->first_name ?? 'N/A') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?= esc($row->last_name ?? 'N/A') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">E-Mail</label>
                        <input type="text" class="form-control" id="email" name="email" value="<?= esc($row->email ?? 'N/A') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="roles" class="form-label">Roles</label>
                        <?php
                            $query = "SELECT * FROM user_roles WHERE disabled = 0";
                            $roles = $user_roles->query($query);
                            $u_id = URL(3);

                            $user_role_ids = array_map(function ($item) {
                                return $item->role_id;
                            }, array_filter($user_roles_map->findAll(), function ($item) use ($u_id) {
                                return $item->user_id == $u_id;
                            }));
                        ?>

                        <?php if (!empty($roles)) : $num = 0 ?>
                            <?php foreach ($roles as $role) : $num++ ?>
                                <div class="form-check col-md-6">
                                    <input 
                                        <?= in_array($role->id, $user_role_ids) ? 'checked' : '' ?> 
                                        name="role_<?= $num ?>" 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        value="<?= $role->id ?>" 
                                        id="check-<?= $num ?>">
                                    <label class="form-check-label" for="check-<?= $num ?>" style="cursor: pointer;">
                                        <?= esc($role->role) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                   
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="New password" autocomplete="new-password">
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn btn-primary" onclick="return validatePasswords()">Save Changes</button>
                    <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <?php else: ?>
        <div class="alert alert-danger text-center">That record was not found!</div>
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