<div class="container mt-4 mb-5">
    <h1 class="h3 mb-4">Search</h1>

    <form method="get" action="<?= ROOT ?>/search" class="input-group mb-4" style="max-width:500px;">
        <label for="search-q" class="visually-hidden">Search</label>
        <input type="text" id="search-q" name="q" class="form-control" placeholder="Search..." value="<?= esc($q) ?>">
        <button class="btn btn-primary" type="submit">
            <i class="bi bi-search"></i> Search
        </button>
    </form>

    <?php if ($q === ''): ?>
        <p class="text-muted">Enter a search term above.</p>
    <?php elseif (empty($results)): ?>
        <p class="text-muted">No results found for "<?= esc($q) ?>".</p>
    <?php else: ?>
        <p class="text-muted"><?= count($results) ?> result(s) for "<?= esc($q) ?>"</p>
        <div class="list-group">
            <?php foreach ($results as $row): ?>
                <a href="<?= ROOT ?>/<?= esc($row->slug) ?>" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-secondary me-2"><?= esc($row->type) ?></span>
                            <span class="fw-bold"><?= esc($row->title) ?></span>
                        </div>
                    </div>
                    <?php if (!empty($row->description)): ?>
                        <p class="mb-0 text-muted small mt-1"><?= esc($row->description) ?></p>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
