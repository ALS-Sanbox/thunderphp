<?php if(user_can('add_page')): ?>

<!-- Page Buttons -->
<div class="d-flex justify-content-between mb-3">
  <button id="showEditorBtn" class="btn btn-primary">Show Editor</button>
  <button id="savePageBtn" class="btn btn-danger">Save Page</button>
</div>

<!-- Basic Page -->
<div class="container-fluid mt-4 border rounded-2 p-2">
  <div id="details">
    <h4>Edit Details</h4>
    <form onsubmit="page.submit(event)" method="post" action="" id="pageForm" enctype="multipart/form-data">
      <input type="hidden" name="_token" value="<?= csrf() ?>">
      
      <div id="basic-container" class="row mx-auto">
        <!-- Left Column -->
        <div class="col-md-8 border px-0">
          <textarea name="column1_content" class="form-control border-0 w-100" rows="20">
        Content goes here...
          </textarea>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
          <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Enter title">
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter description"></textarea>
          </div>
          <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" class="form-control" id="slug" name="slug" placeholder="Enter slug">
          </div>
          <div class="mb-3">
            <label for="views" class="form-label">Views</label>
            <input type="number" class="form-control" id="views" name="views" placeholder="Enter views">
          </div>
          <div class="mb-3">
            <label for="image" class="form-label">Image (optional)</label>
            <input type="file" class="form-control" id="image" name="image">
          </div>
          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="active" name="active">
            <label class="form-check-label" for="active">Active</label>
          </div>
        </div>
      </div>
      
      <!-- GrapesJS Editor -->
      <div id="gjs-container" style="display: none;">
        <div id="gjs"></div>
      </div>
      
      <!-- Progress Bar Container -->
      <div class="progress my-3 d-none" style="height: 25px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
            role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
          0%
        </div>
      </div>

    </form>
  </div>
</div>

<!-- JS Loader -->
<script>
  const pluginBasePath = "<?= plugin_http_path('assets/js/') ?>";
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