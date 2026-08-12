<div class="row mb-4">
    <?php foreach ($stat_cards as $card): ?>
        <div class="col-md-3 col-sm-6 mb-3">
            <a href="<?= esc($card['link']) ?>" class="text-decoration-none">
                <div class="card bg-<?= esc($card['color']) ?> text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title"><?= esc($card['label']) ?></h5>
                                <h2><?= number_format($card['count']) ?></h2>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-<?= esc($card['icon']) ?> fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Recent Activity</h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recent_activity)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Created</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_activity as $item): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary"><?= esc($item['type']) ?></span></td>
                                        <td><?= esc($item['title']) ?></td>
                                        <td><small class="text-muted"><?= esc($item['date'] ? date('M j, Y', strtotime($item['date'])) : '-') ?></small></td>
                                        <td class="text-end">
                                            <?php if (!empty($item['link'])): ?>
                                                <a href="<?= esc($item['link']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4 mb-0">Nothing created yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-lightning-charge"></i> Quick Actions</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <?php foreach ($quick_actions as $action): ?>
                    <a href="<?= esc($action['link']) ?>" class="btn btn-outline-primary text-start">
                        <i class="bi bi-<?= esc($action['icon']) ?>"></i> <?= esc($action['label']) ?>
                    </a>
                <?php endforeach; ?>
                <?php if (empty($quick_actions)): ?>
                    <p class="text-muted mb-0">No actions available for your permissions.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-info-circle"></i> Site Info</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>App:</strong> <?= esc(APP_NAME) ?></p>
                <p class="mb-0"><strong>PHP:</strong> <?= esc(PHP_VERSION) ?></p>
            </div>
        </div>
    </div>
</div>
