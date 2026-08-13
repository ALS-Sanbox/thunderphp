<?php
defined('INSTALL_ROOT') or die('Direct access not permitted');

if (!install_already_completed()) {
    header('Location: install.php?step=profile');
    exit;
}

unset($_SESSION['install_profile'], $_SESSION['install_db'], $_SESSION['install_csrf']);

$step = 'done';
require INSTALL_ROOT . 'install/header.php';
?>
<div class="text-center">
    <div class="display-6 mb-3">&#127881;</div>
    <h2 class="h4 mb-3">Congratulations, ThunderPHP is installed!</h2>
    <p class="text-muted mb-4">Log in with the admin account you just created to start building your site. If you haven't created a homepage yet, the front page will 404 until you add one and set it under Settings &rarr; Homepage.</p>
    <a href="login" class="btn btn-primary w-100">Go to login</a>
</div>
<?php
require INSTALL_ROOT . 'install/footer.php';
