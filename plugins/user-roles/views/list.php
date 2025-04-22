<?php if(user_can('view_users')): ?>

<?php 
if(!isset($_POST['role_id'])){
    $_POST['role_id']= 1;
}

$selectedRoleId = $_GET['role_id'] ?? $_POST['role_id'];

// Get role details
$roleData = $user_roles->find($selectedRoleId);
$roleName = $roleData->role ?? '';
$roleActive = $roleData->disabled ?? 1;
$permissions->limit = 10000;
$permData = $permissions->where(['role_id' => $selectedRoleId, 'disabled' => 0]);
$activePermissionsArray = array_column($permData, 'permission');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Role Editor</title>
</head>
<body>

<link rel="stylesheet" type="text/css" href="<?=plugin_http_path('assets/css/style.css')?>">

<div class="container mt-4">
    <form id="roleData" method="POST">
        <input type="hidden" name="_token" value="<?= csrf() ?>">
        <input type="hidden" name="action" id="action">
        <input type="hidden" id="role" name="role" value="<?= esc($roleName) ?>">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">User Role Editor</h5>
                <?php if (user_can('add_role')): ?>
                    <button type="button" class="btn btn-bd-primary mb-2 ms-auto" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                        <i class="bi bi-person-plus-fill"></i> Add Role
                    </button>
                <?php endif ?>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Select Role</h6>
                        <label for="roleSelect" class="form-label">Role</label>
                        <div style="max-height: 200px; overflow-y: auto;">
                            <select class="form-select" id="roleSelect" name="role_id" size="5">
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= esc($role->id) ?>" <?= $role->id == $selectedRoleId ? 'selected' : '' ?>>
                                        <?= esc($role->role) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Active</h6>
                        <label for="activeSelect" class="form-label">Select Active Status</label>
                        <select class="form-select" id="activeSelect" name="active">
                            <option value="0" <?= $roleActive == 0 ? 'selected' : '' ?>>Yes</option>
                            <option value="1" <?= $roleActive == 1 ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-10">
                        <div class="scrollable-permissions border p-2" id="permissionsList" 
                            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 20px; align-items: center;">
                            <?php foreach ($allPermissions as $key => $perm): ?>
                                <div class="permission-item">
                                    <h6 class="d-flex align-items-center">
                                        <input type="checkbox" class="permission-input me-2" name="permissions[]" value="<?= esc($perm) ?>" 
                                            <?= in_array($perm, $activePermissionsArray) ? 'checked' : '' ?> id="perm_<?= esc($perm) ?>">
                                        <label for="perm_<?= esc($perm) ?>"><?= esc($perm) ?></label>
                                    </h6>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="ms-3" id="buttons">
                            <?php if (user_can('add_role')): ?>
                                <button type="button" class="btn btn-primary mb-2 w-100" data-bs-toggle="modal" data-bs-target="#updateRoleModal">
                                    <i class="bi bi-person-plus-fill"></i> Update
                                </button>
                            <?php endif ?>

                            <?php if (user_can('edit_role')): ?>
                                <button type="button" class="btn btn-warning mb-2 w-100" data-bs-toggle="modal" data-bs-target="#renameRoleModal">
                                    <i class="bi bi-pencil-fill"></i> Rename
                                </button>
                            <?php endif ?>

                            <?php if (user_can('delete_role')): ?>
                                <button type="button" class="btn btn-danger mb-2 w-100" data-bs-toggle="modal" data-bs-target="#deleteRoleModal">
                                    <i class="bi bi-trash3-fill"></i> Delete
                                </button>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="addRoleModalLabel">Add Role</h5>
        </div>

        <div class="modal-body">
            <div class="row mb-3">
                <div class="col-12">
                    <label for="add_role" class="form-label">Role</label>
                    <input name="add_role" value="" type="text" class="form-control" id="add_role" placeholder="New Role Name">
                    <?php if(!empty($errors['role'])) :?>
                    <small class="text-danger"><?=$errors['role']?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                <i class="fa-solid fa-chevron-left"></i> Back
            </button>
            <button type="button" id="confirmAddBtn" class="btn btn-warning">
                <i class="fa-solid fa-pencil"></i> Confirm
            </button>
        </div>
    </div>
  </div>
</div>

<!-- Update Role Modal -->
<div class="modal fade" id="updateRoleModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Role</h5>
      </div>
      <div class="modal-body">Are you sure you want to update the <span class="fw-bolder text-success"><?= esc($roleName) ?></span> role?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmUpdateBtn" class="btn btn-primary">Update</button>
      </div>
    </div>
  </div>
</div>

<!-- Rename Role Modal -->
<div class="modal fade" id="renameRoleModal" tabindex="-1" aria-labelledby="renameRoleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="renameRoleModalLabel">Rename Role</h5>
        </div>

        <div class="modal-body">
            <div class="row mb-3">
                <div class="col-12">
                    <label for="rename_role" class="form-label">Role</label>
                    <input name="rename_role" value="<?= esc($roleName) ?>" type="text" class="form-control" id="rename_role" placeholder="New Role Name">
                    <?php if(!empty($errors['role'])) :?>
                    <small class="text-danger"><?=$errors['role']?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                <i class="fa-solid fa-chevron-left"></i> Back
            </button>
            <button type="button" id="confirmRenameBtn" class="btn btn-warning">
                <i class="fa-solid fa-pencil"></i> Rename
            </button>
        </div>
    </div>
  </div>
</div>

<!-- Delete Role Modal -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger">Delete Role</h5>
      </div>
      <div class="modal-body">Are you sure you want to delete <span class="fw-bolder text-danger"><?= esc($roleName) ?></span>? This action cannot be undone.</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?= plugin_http_path('assets/js/plugin.js') ?>"></script>

</body>
</html>

<?php else: ?>
<div class="alert alert-danger">Access Denied</div>
<?php endif ?>
