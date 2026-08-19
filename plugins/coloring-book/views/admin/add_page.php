<?php if (user_can('manage_coloring_pages')): ?>
<div class="container card shadow mt-4 p-4" style="max-width:600px;">
    <h4 class="mb-1">Add Coloring Page</h4>
    <p class="text-muted mb-3">to <?= esc($book->title) ?></p>
    <form method="POST" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/pages/<?= $book->id ?>/add" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= csrf() ?>">

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Packing for Grandma's" required>
        </div>

        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" class="form-control" id="slug" name="slug" placeholder="auto-generated from title if left blank">
        </div>

        <div class="mb-3">
            <label for="svg_file" class="form-label">Coloring Page SVG</label>
            <input type="file" class="form-control" id="svg_file" name="svg_file" accept=".svg,image/svg+xml" required>
            <small class="text-muted">The uploaded file is sanitized on the server before it's saved - scripts, event handlers, and other active content are stripped automatically.</small>
        </div>

        <div class="mb-3">
            <label for="thumbnail" class="form-label">Thumbnail (optional)</label>
            <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
            <small class="text-muted">Shown in the page picker. You can add or change this later if you skip it now.</small>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Save Coloring Page</button>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    titleInput.addEventListener('input', function () {
        if (!slugInput.dataset.manualEdit) {
            slugInput.value = this.value.toString().toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w-]+/g, '')
                .replace(/--+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        }
    });
    slugInput.addEventListener('input', function () { this.dataset.manualEdit = 'true'; });
});
</script>
<?php else: ?>
<div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px;">
    <div class="card-body">
        <h5 class="card-title text-danger fw-bold">Access Denied</h5>
        <p class="card-text text-muted">You don't have permission for this action.</p>
    </div>
</div>
<?php endif; ?>
