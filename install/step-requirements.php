<?php
defined('INSTALL_ROOT') or die('Direct access not permitted');

$checks = install_check_requirements();
$allPass = install_requirements_pass($checks);

$step = 'requirements';
require INSTALL_ROOT . 'install/header.php';
?>
<h2 class="h5 mb-1">Requirements check</h2>
<p class="text-muted mb-4">ThunderPHP needs the following to run.</p>

<ul class="list-group mb-4">
    <?php foreach ($checks as $check): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <?= install_esc($check['label']) ?>
                <div class="text-muted small"><?= install_esc($check['detail']) ?></div>
            </div>
            <?php if ($check['pass']): ?>
                <span class="badge bg-success">OK</span>
            <?php else: ?>
                <span class="badge bg-danger">Missing</span>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>

<?php if (!$allPass): ?>
    <div class="alert alert-danger">Fix the items above, then reload this page to check again.</div>
    <a href="install.php?step=requirements" class="btn btn-outline-secondary w-100">Check again</a>
<?php else: ?>
    <a href="install.php?step=database" class="btn btn-primary w-100">Continue</a>
<?php endif; ?>
<?php
require INSTALL_ROOT . 'install/footer.php';
