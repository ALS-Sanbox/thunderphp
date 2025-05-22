<?php if(user_can('add_category')): ?>
<script src="<?= plugin_http_path('assets/js/plugin.js') ?>"></script>

<div class="container card shadow mt-6 p-4">
    <form method="POST" enctype="multipart/form-data">
        <h4 class="mb-4">Add Category</h4> 
        <div class="row">
            <div class="col-md-12">
                <input type="hidden" name="_token" value="<?= csrf() ?>">
                <input type="hidden" name="disabled" value="1">
                <div class="mb-3">
                    <label for="category" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="category" name="category" placeholder="Enter category">
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control" id="slug" name="slug" placeholder="Enter slug">
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="activeSwitch" name="disabled" value="0" <?= empty($row->disabled) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="activeSwitch">Active</label>
                </div>
                <?php $selected_parent_id = $_POST['parent_id'] ?? '';?>

                <div class="mb-3">
                    <label for="parent_id" class="form-label">Parent Category</label>
                    <select class="form-select" id="parent_id" name="parent_id">
                        <option value="0" <?= ($selected_parent_id == '0' || $selected_parent_id === '') ? 'selected' : '' ?>>-- None --</option>
                        <?php foreach ($cat->findAll() as $kitty): ?>
                            <option value="<?= $kitty->id ?>" <?= ($selected_parent_id == $kitty->id) ? 'selected' : '' ?>>
                                <?= esc($kitty->category) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Add Category</button>
                <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>

<?php else: ?>
<div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;"> 
    <div class="card-body">
        <h5 class="card-title text-danger fw-bold">Access Denied</h5>
        <p class="card-text text-muted">You don't have permission for this action.</p>
    </div>
</div>
<?php endif ?>