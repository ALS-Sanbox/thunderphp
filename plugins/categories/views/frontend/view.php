<?php if (user_can('view_category')): ?>
    <script src="<?= plugin_http_path('assets/js/plugin.js') ?>"></script>

    <div class="container card shadow mt-6 p-4">
        <h4 class="mb-4">View Category</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label fw-bold">Category Name</label>
                    <p class="form-control-plaintext"><?= esc($row->category ?? '-') ?></p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Slug</label>
                    <p class="form-control-plaintext"><?= esc($row->slug ?? '-') ?></p>
                </div>

                <?php $is_active = empty($row->disabled); ?>

                <div class="mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <p class="form-control-plaintext">
                        <?= $is_active ? 'Active' : 'Disabled' ?>
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Parent Category</label>
                    <p class="form-control-plaintext">
                        <?php
                            $parent_name = '-';
                            if (!empty($row->parent_id) && !empty($cat->find($row->parent_id))) {
                                $parent_name = esc($cat->find($row->parent_id)->category);
                            }
                            echo $parent_name;
                        ?>
                    </p>
                </div>

                <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div id="denied" class="card text-center shadow-lg border-danger mx-auto" style="max-width: 400px;">
        <div class="card-body">
            <h5 class="card-title text-danger fw-bold">Access Denied</h5>
            <p class="card-text text-muted">You don't have permission for this action.</p>
        </div>
    </div>
<?php endif; ?>
