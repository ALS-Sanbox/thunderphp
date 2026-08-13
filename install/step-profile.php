<?php
defined('INSTALL_ROOT') or die('Direct access not permitted');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!install_csrf_verify($_POST['_token'] ?? null)) {
        $errors['_token'] = 'This form expired, please try again.';
    } elseif (!in_array($_POST['profile'] ?? '', ['standard', 'minimal'], true)) {
        $errors['profile'] = 'Choose a profile to continue.';
    } else {
        $_SESSION['install_profile'] = $_POST['profile'];
        header('Location: install.php?step=requirements');
        exit;
    }
}

$step = 'profile';
require INSTALL_ROOT . 'install/header.php';
$selected = $_SESSION['install_profile'] ?? 'standard';
?>
<h2 class="h5 mb-1">Choose an installation profile</h2>
<p class="text-muted mb-4">Every feature in ThunderPHP is a plugin. Pick how many of them you want active to start with &mdash; you can always change this later.</p>

<?php if (!empty($errors['profile']) || !empty($errors['_token'])): ?>
    <div class="alert alert-danger"><?= install_esc($errors['profile'] ?? $errors['_token']) ?></div>
<?php endif; ?>

<form method="post">
    <?= install_csrf_field() ?>

    <div class="form-check border rounded p-3 mb-3 <?= $selected === 'standard' ? 'border-primary' : '' ?>">
        <input class="form-check-input" type="radio" name="profile" value="standard" id="profile-standard" <?= $selected === 'standard' ? 'checked' : '' ?>>
        <label class="form-check-label w-100" for="profile-standard">
            <strong>Standard</strong> <span class="badge bg-primary">Recommended</span>
            <div class="text-muted small">Every shipped plugin active: pages, posts, menus, categories, images, user roles &mdash; a full site out of the box.</div>
        </label>
    </div>

    <div class="form-check border rounded p-3 mb-4 <?= $selected === 'minimal' ? 'border-primary' : '' ?>">
        <input class="form-check-input" type="radio" name="profile" value="minimal" id="profile-minimal" <?= $selected === 'minimal' ? 'checked' : '' ?>>
        <label class="form-check-label w-100" for="profile-minimal">
            <strong>Minimal</strong>
            <div class="text-muted small">Just the admin shell: login, user &amp; role management, settings. No content types &mdash; add pages/posts/menus later by activating their plugins.</div>
        </label>
    </div>

    <button type="submit" class="btn btn-primary w-100">Continue</button>
</form>
<?php
require INSTALL_ROOT . 'install/footer.php';
