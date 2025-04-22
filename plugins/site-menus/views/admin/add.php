<?php if(user_can('add_menu')):?>

<script src="<?=plugin_http_path('assets/js/plugin.js')?>"></script>

<div class="container card shadow mt-6 p-4">
    <form method="POST" enctype="multipart/form-data">
        <h4 class="mb-4">Add Menu</h4>
        <div class="row">
          <!-- Left Column: Icon and Mega Image -->
        <div class="col-md-4 text-center">
            <!-- Icon Upload -->
            <div>
                <label for="iconUpload" style="cursor: pointer;">
                    <img src="<?= esc(get_image(null)) ?>" alt="Icon" class="img-fluid rounded-circle" id="previewIcon" style="max-width: 200px; height: auto;">
                </label>
                <div class="mt-2 fw-bold">Icon</div>
                <input type="file" id="iconUpload" name="icon" accept="image/*" class="d-none" onchange="previewFile('iconUpload', 'previewIcon')">
            </div>

            <!-- Mega Image Upload -->
            <div class="mt-4">
                <label for="megaImageUpload" style="cursor: pointer;">
                    <img src="<?= esc(get_image(null)) ?>" alt="Mega Image" class="img-fluid rounded-circle" id="previewMegaImage" style="max-width: 200px; height: auto;">
                </label>
                <div class="mt-2 fw-bold">Mega Image</div>
                <input type="file" id="megaImageUpload" name="mega_image" accept="image/*" class="d-none" onchange="previewFile('megaImageUpload', 'previewMegaImage')">
            </div>
        </div>

            <!-- Right Column: Form Fields -->
            <div class="col-md-8">
                <input type="hidden" name="_token" value="<?= csrf() ?>">

                <!-- Title -->
                <div class="mb-3">
                    <label for="title" class="form-label">Menu Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <!-- Parent -->
                <div class="mb-3">
                    <label for="parent" class="form-label">Parent</label>
                    <select class="form-select" id="parent" name="parent">
                        <option value="0">Select Parent</option>
                        <?php if(!empty($all_items)) :?>
                            <?php foreach($all_items as $item) :?>
                                <option <?= old_select('parent',$item->id)?> value="<?=$item->id ?>"><?=$item->title ?></option>
                            <?php endforeach;?>
                        <?php endif;?>
                    </select>
                </div>

                <!-- Is Mega (Toggle) -->
                <div class="mb-3 form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="isMega" name="is_mega">
                    <label class="form-check-label" for="isMega">Is Mega</label>
                </div>

                <!-- Active (Toggle) -->
                <div class="mb-3 form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="active" name="active" checked>
                    <label class="form-check-label" for="active">Active</label>
                </div>

                <!-- Permission (Scorable Box) -->
                <div class="mb-3">
                    <label for="permissions" class="form-label">Permissions</label>
                    <input type="text" class="form-control" id="permission" name="permission">
                </div>

                <!-- Slug -->
                <div class="mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control" id="slug" name="slug" required>
                </div>

                <!-- Submit and Cancel Buttons -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Add Menu</button>
                    <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>" class="btn btn-secondary">Cancel</a>
                </div>
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