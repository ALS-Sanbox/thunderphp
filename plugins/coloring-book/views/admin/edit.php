<?php if (user_can('edit_coloring_books')): ?>
<div class="container card shadow mt-4 p-4" style="max-width:600px;">
    <h4 class="mb-3">Edit Coloring Book</h4>
    <form method="POST" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/edit/<?= $row->id ?>" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= csrf() ?>">

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= esc($row->title) ?>" required>
        </div>

        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" class="form-control" id="slug" name="slug" value="<?= esc($row->slug) ?>">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?= esc($row->description ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="cover_image" class="form-label">Cover Image</label>
            <?php if (!empty($row->cover_image) && file_exists($row->cover_image)): ?>
                <div class="mb-2"><img src="<?= get_image($row->cover_image) ?>" alt="" style="max-height:100px;"></div>
            <?php endif; ?>
            <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
            <small class="text-muted">Leave blank to keep the current cover.</small>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="draft" <?= $row->status === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= $row->status === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= esc($row->sort_order) ?>">
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/pages/<?= $row->id ?>" class="btn btn-outline-primary">Manage Pages</a>
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
<?php endif; ?>
