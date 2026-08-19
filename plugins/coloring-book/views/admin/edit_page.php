<?php if (user_can('manage_coloring_pages')): ?>
<div class="container card shadow mt-4 p-4" style="max-width:600px;">
    <h4 class="mb-1">Edit Coloring Page</h4>
    <p class="text-muted mb-3">in <?= esc($book->title) ?></p>
    <form method="POST" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/pages/<?= $book->id ?>/edit/<?= $row->id ?>" enctype="multipart/form-data">
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
            <label class="form-label">Current Picture</label>
            <?php if (!empty($row->svg_path) && file_exists($row->svg_path)): ?>
                <div class="border rounded p-2 mb-2" style="max-width:200px;"><img src="<?= ROOT ?>/<?= esc($row->svg_path) ?>" alt="" style="max-width:100%;"></div>
            <?php else: ?>
                <p class="text-danger small">No SVG on file - please upload one.</p>
            <?php endif; ?>
            <label for="svg_file" class="form-label">Replace SVG (optional)</label>
            <input type="file" class="form-control" id="svg_file" name="svg_file" accept=".svg,image/svg+xml">
            <small class="text-muted">Leave blank to keep the current picture. Any new file is sanitized on the server before it's saved.</small>
        </div>

        <div class="mb-3">
            <label for="thumbnail" class="form-label">Thumbnail</label>
            <?php if (!empty($row->thumbnail_path) && file_exists($row->thumbnail_path)): ?>
                <div class="mb-2"><img src="<?= get_image($row->thumbnail_path) ?>" alt="" style="max-height:80px;"></div>
            <?php endif; ?>
            <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
            <small class="text-muted">Leave blank to keep the current thumbnail.</small>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="draft" <?= $row->status === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= $row->status === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/pages/<?= $book->id ?>" class="btn btn-outline-secondary">&larr; Back to Pages</a>
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
