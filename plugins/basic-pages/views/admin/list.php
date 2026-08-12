<?php if(user_can('view_pages')): ?>
<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">Manage your basic or Advanced pages</p>
        </div>
        <div>
            <?php if(user_can('add_page')): ?>
                <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/add" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Create New Page
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3" method="get" action="">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by title or description..." value="<?= esc($_GET['search'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <?php
    $stats = $pages->query("SELECT
        COUNT(*) as total,
        COUNT(CASE WHEN disabled = 0 THEN 1 END) as active,
        COUNT(CASE WHEN disabled = 1 THEN 1 END) as inactive,
        SUM(views) as total_views
        FROM pages
        WHERE date_deleted IS NULL");
    $stat = $stats[0] ?? (object)['total' => 0, 'active' => 0, 'inactive' => 0, 'total_views' => 0];
    ?>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Total Pages</h5>
                            <h2><?= (int) $stat->total ?></h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-files fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Active Pages</h5>
                            <h2><?= (int) $stat->active ?></h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Inactive Pages</h5>
                            <h2><?= (int) $stat->inactive ?></h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-pause-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Total Views</h5>
                            <h2><?= number_format((int) $stat->total_views) ?></h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-eye fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pages Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Pages List</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <?php if(user_can('edit_page') || user_can('delete_page')): ?>
                                <th style="width: 40px;"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                            <?php endif; ?>
                            <th style="width: 50px;">#</th>
                            <th style="width: 200px;">Title</th>
                            <th>Description</th>
                            <th style="width: 120px;">Type</th>
                            <th style="width: 80px;">Views</th>
                            <th style="width: 80px;">Status</th>
                            <th style="width: 120px;">Created</th>
                            <th style="width: 200px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rows)): ?>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <?php if(user_can('edit_page') || user_can('delete_page')): ?>
                                        <td><input type="checkbox" class="form-check-input page-select" value="<?= esc($row->id) ?>"></td>
                                    <?php endif; ?>
                                    <td>
                                        <strong><?= esc($row->id) ?></strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= esc($row->title) ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-link-45deg"></i>
                                                <?= esc($row->slug) ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 250px;" title="<?= esc($row->description ?? '') ?>">
                                            <?= esc($row->description ?: 'No description') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($row->advanced)): ?>
                                            <span class="badge bg-info">Advanced</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Basic</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="text-primary fw-bold">
                                            <?= number_format($row->views ?? 0) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (empty($row->disabled)): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= esc($row->date_created ? date('M j, Y', strtotime($row->date_created)) : '-') ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <!-- View Live -->
                                            <?php if (empty($row->disabled)): ?>
                                                <a href="<?= ROOT ?>/<?= esc($row->slug) ?>" target="_blank" class="btn btn-outline-primary" title="View Live Page">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            <?php endif; ?>

                                            <!-- Edit -->
                                            <?php if(user_can('edit_page')): ?>
                                                <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/edit/<?= $row->id ?>" class="btn btn-outline-warning" title="Edit Page">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            <?php endif; ?>

                                            <!-- Duplicate -->
                                            <?php if(user_can('add_page')): ?>
                                                <button type="button" class="btn btn-outline-info duplicate-btn" data-id="<?= $row->id ?>" data-title="<?= esc($row->title) ?>" title="Duplicate Page">
                                                    <i class="bi bi-files"></i>
                                                </button>
                                            <?php endif; ?>

                                            <!-- Delete -->
                                            <?php if(user_can('delete_page')): ?>
                                                <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/delete/<?= $row->id ?>" class="btn btn-outline-danger" title="Delete Page">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-file-earmark-x fs-1 d-block mb-3 opacity-50"></i>
                                        <h5>No Pages Found</h5>
                                        <p>Create your first page to get started.</p>
                                        <?php if(user_can('add_page')): ?>
                                            <a href="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/add" class="btn btn-primary">
                                                <i class="bi bi-plus-lg"></i> Create First Page
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if (!empty($rows)): ?>
            <div class="card-footer">
                <?= $pager->display() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Duplicate Page Modal -->
<?php if(user_can('add_page')): ?>
<div class="modal fade" id="duplicateModal" tabindex="-1" aria-labelledby="duplicateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="duplicateModalLabel">
                    <i class="bi bi-files"></i> Duplicate Page
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="duplicateForm" method="post" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/duplicate">
                <div class="modal-body">
                    <input type="hidden" id="duplicatePageId" name="source_id">
                    <input type="hidden" name="_token" value="<?= csrf() ?>">

                    <div class="mb-3">
                        <label for="duplicateTitle" class="form-label">New Page Title</label>
                        <input type="text" class="form-control" id="duplicateTitle" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="duplicateSlug" class="form-label">New Slug</label>
                        <input type="text" class="form-control" id="duplicateSlug" name="slug">
                        <small class="text-muted">Will be auto-generated if left empty</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-files"></i> Create Duplicate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Bulk Actions -->
<?php if (!empty($rows) && (user_can('edit_page') || user_can('delete_page'))): ?>
<form id="bulkActionsForm" method="post" action="<?= ROOT ?>/<?= $admin_route ?>/<?= $plugin_route ?>/bulk">
<div class="card mt-4">
    <div class="card-header">
        <h6 class="card-title mb-0">Bulk Actions</h6>
    </div>
    <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="bulkAction" class="form-label">Select Action</label>
                    <select class="form-select" id="bulkAction" name="action">
                        <option value="">Choose action...</option>
                        <?php if(user_can('edit_page')): ?>
                            <option value="activate">Activate Selected</option>
                            <option value="deactivate">Deactivate Selected</option>
                        <?php endif; ?>
                        <?php if(user_can('delete_page')): ?>
                            <option value="delete">Delete Selected</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Selected Pages</label>
                    <div class="form-control-plaintext">
                        <span id="selectedCount">0</span> pages selected
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-warning w-100" disabled id="bulkSubmitBtn">
                        <i class="bi bi-gear"></i> Execute Action
                    </button>
                </div>
            </div>
            <input type="hidden" name="_token" value="<?= csrf() ?>">
            <input type="hidden" name="selected_ids" id="selectedIds">
    </div>
</div>
</form>
<?php endif; ?>

<style>
    .duplicate-btn:hover {
        transform: scale(1.1);
    }

    .btn-group-sm .btn {
        transition: all 0.2s ease;
    }

    .btn-group-sm .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0,0,0,.125);
    }

    .badge {
        font-size: 0.75em;
    }

    @media (max-width: 768px) {
        .btn-group-sm {
            flex-direction: column;
        }

        .btn-group-sm .btn {
            border-radius: 0.375rem !important;
            margin-bottom: 2px;
        }
    }
</style>

<script>
(function () {
    let selectedPages = new Set();
    const selectAll = document.getElementById('selectAll');
    const pageSelects = document.querySelectorAll('.page-select');
    const bulkForm = document.getElementById('bulkActionsForm');

    function updateBulkActions() {
        const selectedCountEl = document.getElementById('selectedCount');
        const selectedIdsEl = document.getElementById('selectedIds');
        const bulkSubmitBtn = document.getElementById('bulkSubmitBtn');
        const bulkAction = document.getElementById('bulkAction');
        if (!selectedCountEl) return;

        selectedCountEl.textContent = selectedPages.size;
        selectedIdsEl.value = Array.from(selectedPages).join(',');
        bulkSubmitBtn.disabled = !(selectedPages.size > 0 && bulkAction.value);
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            pageSelects.forEach((cb) => {
                cb.checked = this.checked;
                this.checked ? selectedPages.add(cb.value) : selectedPages.delete(cb.value);
            });
            updateBulkActions();
        });
    }

    pageSelects.forEach((checkbox) => {
        checkbox.addEventListener('change', function () {
            this.checked ? selectedPages.add(this.value) : selectedPages.delete(this.value);
            updateBulkActions();
            if (selectAll) {
                selectAll.checked = document.querySelectorAll('.page-select:checked').length === pageSelects.length;
            }
        });
    });

    const bulkAction = document.getElementById('bulkAction');
    if (bulkAction) bulkAction.addEventListener('change', updateBulkActions);

    if (bulkForm) {
        bulkForm.addEventListener('submit', function (e) {
            const action = document.getElementById('bulkAction').value;
            const count = selectedPages.size;
            let confirmMessage = `Are you sure you want to ${action} ${count} selected page(s)?`;
            if (action === 'delete') {
                confirmMessage = `This will permanently delete ${count} selected page(s). This action cannot be undone. Continue?`;
            }
            if (!confirm(confirmMessage)) {
                e.preventDefault();
            }
        });
    }

    document.querySelectorAll('.duplicate-btn').forEach((btn) => {
        btn.addEventListener('click', function () {
            const pageId = this.dataset.id;
            const title = this.dataset.title;

            document.getElementById('duplicatePageId').value = pageId;
            document.getElementById('duplicateTitle').value = title + ' (Copy)';
            document.getElementById('duplicateSlug').value = '';

            new bootstrap.Modal(document.getElementById('duplicateModal')).show();
        });
    });

    const duplicateTitle = document.getElementById('duplicateTitle');
    if (duplicateTitle) {
        duplicateTitle.addEventListener('input', function () {
            const slug = this.value.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.getElementById('duplicateSlug').value = slug;
        });
    }
})();
</script>

<?php else: ?>
<!-- Access Denied -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h5 class="card-title text-danger">Access Denied</h5>
                    <p class="card-text">You don't have permission to view pages.</p>
                    <a href="<?= ROOT ?>/<?= $admin_route ?>" class="btn btn-primary">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif ?>
