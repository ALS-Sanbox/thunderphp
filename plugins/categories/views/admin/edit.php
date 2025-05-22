<?php if (user_can('edit_category')): ?>

<script src="<?= plugin_http_path('assets/js/plugin.js') ?>"></script>

<?php if (!empty($row) && !empty($row->id)): ?>
    <div class="container card shadow mt-6 p-4">
        <form method="POST" enctype="multipart/form-data">
            <h4 class="mb-4">Edit Category</h4>
            <div class="row">
                <div class="col-md-8">
                    <input type="hidden" name="_token" value="<?= csrf() ?>">
                    <input type="hidden" name="disabled" value="1">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="category" name="category" value="<?= esc($row->category ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?= esc($row->slug ?? '') ?>">
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="activeSwitch" name="disabled" value="0" <?= empty($row->disabled) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="activeSwitch">Active</label>
                    </div>
                    <?php $selected_parent_id = $_POST['parent_id'] ?? $row->parent_id ?? '';?>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Parent Category</label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">-- None --</option>
                            <?php foreach ($cat->findAll() as $kitty): ?>
                                <option value="<?= $kitty->id ?>" <?= ($selected_parent_id == $kitty->id) ? 'selected' : '' ?>>
                                    <?= esc($kitty->category) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
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

<?php else: ?>
    <div class="card text-center shadow-lg border-danger mx-auto" style="max-width: 400px;"> 
        <div class="card-body">
            <h5 class="card-title text-danger fw-bold">Access Denied</h5>
            <p class="card-text text-muted">You don't have permission for this action.</p>
        </div>
    </div>
<?php endif; ?>