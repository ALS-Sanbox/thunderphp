<?php if (user_can('manage_header_footer')): ?>
<div class="container-fluid mt-4">
  <h4 class="mb-1">Header &amp; Footer</h4>
  <p class="text-muted">Visually design the header bar and footer that appear on every page of your site. Drag in the "Site Menu" block to control where your site's navigation appears.</p>

  <div class="row g-3 mt-2">
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-body text-center">
          <h5 class="card-title">Header</h5>
          <p class="card-text text-muted">The bar at the top of every page, including your site's navigation menu.</p>
          <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/header" class="btn btn-primary">Edit Header</a>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-body text-center">
          <h5 class="card-title">Footer</h5>
          <p class="card-text text-muted">The bar at the bottom of every page, such as your copyright notice.</p>
          <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/footer" class="btn btn-primary">Edit Footer</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php else: ?>
<div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;">
  <div class="card-body">
    <h5 class="card-title text-danger fw-bold">Access Denied</h5>
    <p class="card-text text-muted">You don't have permission for this action.</p>
  </div>
</div>
<?php endif ?>
