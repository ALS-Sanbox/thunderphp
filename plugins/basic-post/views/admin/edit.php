<?php if(user_can('edit_page')): ?>
  <link rel="stylesheet" href="<?=plugin_http_path('assets/css/style.css')?>">
  <link rel="stylesheet" href="<?=ROOT?>/assets/summernote/summernote-lite.min.css">
  <script src="<?=ROOT?>/assets/summernote/summernote-lite.min.js"></script>
  
<!-- Progress Bar Container -->
<div class="progress my-3 d-none" style="height: 25px;">
  <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
      role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
    0%
  </div>
</div>

<!-- Basic Posts -->
<div class="container-fluid mt-4 border rounded-2 p-2">
  <div id="details">
    <h4>Edit Details</h4>
    <form method="post" action="" id="pageForm" enctype="multipart/form-data">
      <!-- Posts Buttons -->
<div class="d-flex justify-content-between mb-3">
  <button id="showDetailsBtn" class="btn btn-primary" style="display: none;" type="button">Posts Details</button>
  <button id="savePageBtn" class="btn btn-danger">Save Posts</button>
</div>
      <input type="hidden" name="_token" value="<?= csrf() ?>">
      
      <div id="basic-container" class="row mx-auto">
        <!-- Left Column -->
        <div class="col-md-8 border px-0">
            <textarea name="content" class="summernote form-control border-0 w-100 bg-white"><?= esc($row->content ?? '') ?></textarea>
        </div>
        <!-- Right Column -->
        <div class="col-md-4">
          <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= esc($row->title ?? 'N/A') ?>">
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?= esc($row->description ?? '') ?></textarea>
            <small id="descCounter" class="text-muted">0 / 155 characters</small>
          </div>
          <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" class="form-control" id="slug" name="slug" value="<?= esc($row->slug ?? '') ?>">
          </div>
          <div class="mb-3">
            <label for="keywords" class="form-label">Keywords</label>
            <input type="text" class="form-control" id="keywords" name="keywords" value="<?= esc($row->keywords ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Categories</label>
            <div class="categories-container">
            <?php
              $query = "select * from categories where disabled = 0";
              $categories = $pages->query($query);
              $selected_categories = json_decode($row->categories ?? '[]');
              if (!empty($categories)) : $num = 0;
                foreach ($categories as $category): $num++;
            ?>
              <div class="scrollable-permissions form-check">
                <input 
                  type="checkbox" 
                  class="form-check-input" 
                  id="check<?=$num?>" 
                  name="categories[]" 
                  value="<?= esc($category->id) ?>" 
                  <?= in_array($category->id, $selected_categories) ? 'checked' : '' ?>>
                <label class="form-check-label" for="check<?=$num?>" style="cursor:pointer;">
                  <?= esc(str_replace("_", " ", $category->category)) ?>
                </label>
              </div>
            <?php
                endforeach;
              endif;
            ?>
            </div>
          </div>
          <div class="mb-3">
            <label for="views" class="form-label">Views</label>
            <input type="number" class="form-control" id="views" name="views" value="<?= esc($row->views ?? '') ?>">
          </div>
          <div class="form-check form-switch mb-3">
            <input type="hidden" name="active" value="0">
          <input class="form-check-input" type="checkbox" id="active" name="active" <?= ($row->disabled == 0) ? 'checked' : '' ?>>
            <label class="form-check-label" for="active">Active</label>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- JS Loader -->
<script>
  const pluginBasePath = "<?= plugin_http_path('assets/js/') ?>";
  const editImagePath = "<?= plugin_http_path('uploads/') ?>";
  // Character counter for description field
  document.getElementById('description').addEventListener('input', function () {
  const maxLength = 155;
  const currentLength = this.value.length;
  const counter = document.getElementById('descCounter');

  counter.textContent = `${currentLength} / ${maxLength} characters`;

  if (currentLength > maxLength) {
  descCounter.classList.remove('text-muted');
  descCounter.classList.add('text-danger');
  } else {
    descCounter.classList.remove('text-danger');
    descCounter.classList.add('text-muted');
  }
    
  });
</script>
<script src="<?= plugin_http_path('assets/js/plugin.js') ?>"></script>

<?php else: ?>
<div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;">
  <div class="card-body">
    <h5 class="card-title text-danger fw-bold">Access Denied</h5>
    <p class="card-text text-muted">You don't have permission for this action.</p>
  </div>
</div>
<?php endif ?>