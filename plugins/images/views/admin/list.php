<?php if(user_can('view_images')): ?>
<link rel="stylesheet" href="<?=plugin_http_path('assets/css/style.css')?>">

<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Images</h4>
  </div>

  <?php if(user_can('add_image')): ?>
  <form method="post" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/add" enctype="multipart/form-data" class="card p-3 mb-4">
    <input type="hidden" name="_token" value="<?= csrf() ?>">
    <div class="d-flex gap-2 flex-wrap align-items-center">
      <label for="images-upload" class="visually-hidden">Upload images</label>
      <input type="file" id="images-upload" name="images[]" accept="image/*" multiple class="form-control" style="max-width:400px;">
      <button type="submit" class="btn btn-bd-primary">
        <i class="bi bi-upload"></i> Upload
      </button>
    </div>
  </form>
  <?php endif; ?>

  <div class="row g-3">
    <?php if (!empty($rows)): ?>
      <?php foreach ($rows as $row): ?>
        <div class="col-6 col-md-4 col-lg-3">
          <div class="card h-100 image-card">
            <img src="<?= esc(ROOT . '/' . $row->path) ?>" class="card-img-top image-thumb image-preview-trigger" alt="<?= esc($row->original_name ?? $row->filename) ?>" role="button" data-bs-toggle="modal" data-bs-target="#imagePreviewModal" data-full-src="<?= esc(ROOT . '/' . $row->path) ?>" data-name="<?= esc($row->original_name ?? $row->filename) ?>">
            <div class="card-body">
              <p class="small text-truncate mb-1" title="<?= esc($row->original_name ?? $row->filename) ?>"><?= esc($row->original_name ?? $row->filename) ?></p>
              <div class="input-group input-group-sm mb-2">
                <input type="text" class="form-control image-url" readonly value="<?= esc(ROOT . '/' . $row->path) ?>" onclick="this.select()">
                <button type="button" class="btn btn-outline-secondary copy-url-btn" data-url="<?= esc(ROOT . '/' . $row->path) ?>">
                  <i class="bi bi-clipboard"></i>
                </button>
              </div>
              <?php if(user_can('delete_image')): ?>
              <form method="post" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/delete/<?= $row->id ?>" onsubmit="return confirm('Delete this image?');">
                <input type="hidden" name="_token" value="<?= csrf() ?>">
                <input type="hidden" name="image_id" value="<?= $row->id ?>">
                <button type="submit" class="btn btn-sm btn-danger w-100">
                  <i class="bi bi-trash3-fill"></i> Delete
                </button>
              </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <p class="text-center text-muted">No images uploaded yet.</p>
      </div>
    <?php endif; ?>
  </div>

  <?= $pager->display() ?>
</div>

<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered image-preview-dialog">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h6 class="modal-title" id="imagePreviewLabel">Preview</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-2">
        <img src="" id="imagePreviewImg" class="image-preview-full" alt="">
      </div>
    </div>
  </div>
</div>

<script src="<?=plugin_http_path('assets/js/plugin.js')?>"></script>

<?php else: ?>
<div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;">
  <div class="card-body">
    <h5 class="card-title text-danger fw-bold">Access Denied</h5>
    <p class="card-text text-muted">You don't have permission for this action.</p>
  </div>
</div>
<?php endif ?>
