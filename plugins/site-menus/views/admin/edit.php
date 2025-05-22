<?php if(user_can('edit_menu')):?>

<script src="<?=plugin_http_path('assets/js/plugin.js')?>"></script>

<?php if (!empty($row) && !empty($row->id)): ?>
    <div class="container card shadow mt-6 p-4">
        <form method="POST" enctype="multipart/form-data">
            <h4 class="mb-4">Edit Menu</h4>
            <div class="row">
            <!-- Left Column: Icon and Mega Image -->
            <div class="col-md-4 text-center">
                        <!-- Icon Upload -->
                        <div>
                            <label for="iconUpload" style="cursor: pointer;">
                                <img src="<?= esc(get_image($row->image ?? '')) ?>" alt="Icon" class="img-fluid rounded-circle" id="previewIcon" style="max-width: 200px; height: auto;">
                            </label>
                            <div class="mt-2 fw-bold">Icon</div>
                            <input type="file" id="iconUpload" name="icon" accept="image/*" class="d-none" onchange="previewFile('iconUpload', 'previewIcon')">
                        </div>

                        <!-- Mega Image Upload -->
                        <div class="mt-4">
                            <label for="megaImageUpload" style="cursor: pointer;">
                                <img src="<?= esc(get_image($row->mega_image ?? '')) ?>" alt="Mega Image" class="img-fluid rounded-circle" id="previewMegaImage" style="max-width: 200px; height: auto;">
                            </label>
                            <div class="mt-2 fw-bold">Mega Image</div>
                            <input type="file" id="megaImageUpload" name="mega_image" accept="image/*" class="d-none" onchange="previewFile('megaImageUpload', 'previewMegaImage')">
                        </div>
                    </div>
                <div class="col-md-8">
                    <input type="hidden" name="_token" value="<?= csrf() ?>">
                    <div class="mb-3">
                        <label for="title" class="form-label">Menu Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?= esc($row->title ?? 'N/A') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="parent" class="form-label">Parent</label>
                        <select class="form-select" id="parent" name="parent">
                            <option value="0" <?= $row->parent == 0 ? 'selected' : '' ?>>Select Parent</option>
                            <?php if (!empty($all_items)) : ?>
                                <?php foreach ($all_items as $item) : ?>
                                    <option value="<?= $item->id ?>" <?= $row->parent == $item->id ? 'selected' : '' ?>>
                                        <?= $item->title ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="isMega" name="is_mega" 
                            value="1" <?= isset($row->is_mega) && $row->is_mega == 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isMega">Is Mega</label>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="active" name="active" 
                            value="1" <?= isset($row->disabled) && $row->disabled == 0 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="active">Active</label>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Permissions</label>
                        <input type="text" class="form-control" id="permission" name="permission" value="<?= esc($row->permission ?? 'N/A') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?= esc($row->slug ?? 'N/A') ?>" required>
                    </div>
					<div class="mb-3">
                        <label for="list_order" class="form-label">Order</label>
                        <input type="number" class="form-control" id="list_order" name="list_order" value="<?= esc($row->list_order ?? 'N/A') ?>" required>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">Update Menu</button>
                        <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>" class="btn btn-secondary">Cancel</a>
                    </div>
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