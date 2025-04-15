<?php if(user_can('add_user')):?>

<script src="<?=plugin_http_path('assets/js/plugin.js')?>"></script>

<div class="container card shadow mt-6 p-4">
    <form method="POST" enctype="multipart/form-data">
    <h4 class="mb-4">Add User</h4> 
    <div class="row">
        <div class="col-md-4 text-center">
            <label for="imageUpload" style="cursor: pointer;">
                <img src="<?= esc(get_image(null)) ?>" alt="User Image" class="img-fluid rounded-circle" id="previewImage" style="max-width: 200px; height: auto;">
            </label>
            <div class="mt-2 fw-bold"> New User </div>
            <input type="file" id="imageUpload" name="image" accept="image/*" class="d-none" onchange="previewFile()">
        </div>
        <div class="col-md-8">
        <input type="hidden" name="_token" value="<?= csrf() ?>">
        <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input value="<?=old_value('first_name')?>" type="text" class="form-control" id="first_name" name="first_name" required>
                </div>
                
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input value="<?=old_value('last_name')?>" type="text" class="form-control" id="last_name" name="last_name" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">E-Mail</label>
                    <input value="<?=old_value('email')?>" type="text" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="roles" class="form-label">Roles</label>
                    <select class="form-select" id="roles" name="roles[]" multiple>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Enter password" autocomplete="new-password">
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Confirm password" autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-primary" onclick="return validatePasswords()">Add User</button>
                <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>

<?php else:?>
    <div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;"> 

        <div class="card-body">
            <h5 class="card-title text-danger fw-bold">Access Denied</h5>
            <p class="card-text text-muted">You don't have permission for this action.</p>
        </div>
    </div>
<?php endif?>