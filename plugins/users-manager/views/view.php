<?php if(user_can('view_user_detail')):?>
    <?php if(!empty($row)): ?>
        <div class="container card shadow mt-6 p-4">
            <h4 class="mb-4">View Record</h4>
            <div class="row">
                <div class="col-md-4 text-center">
                    <label for="imageUpload" style="cursor: pointer;">
                    <img src="<?= esc( get_image($row->image)) ?>" 
                            alt="User Image" class="img-fluid rounded-circle" id="previewImage" 
                            style="max-width: 200px; height: auto;">
                    </label>
                    <div class="mt-2 fw-bold"> <?= esc($row->first_name ?? 'N/A') ?> <?= esc($row->last_name ?? 'N/A') ?> </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <strong>First Name:  </strong> <?= esc($row->first_name ?? 'N/A') ?>
                    </div>
                    <div class="mb-3">
                        <strong>Last Name:  </strong> <?= esc($row->last_name ?? 'N/A') ?>
                    </div>
                    <div class="mb-3">
                        <strong>E-Mail:  </strong> <?= esc($row->email ?? 'N/A') ?>
                    </div>
                    <div class="mb-3">
                    <strong>Roles:  </strong>
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
                                <?php if (in_array($role->id, $user_role_ids)): ?>
                                    <label class="form-check-label" for="check-<?= $num ?>" style="cursor: pointer;">
                                        <?= esc($role->role) ?>
                                    </label>
                                <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>">
                        <button class="btn btn-sm btn-success">
                            <i class="bi bi-arrow-bar-left"></i> Back
                        </button>
                    </a>
                </div>
            </div>
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