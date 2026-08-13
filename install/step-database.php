<?php
defined('INSTALL_ROOT') or die('Direct access not permitted');

if (empty($_SESSION['install_profile'])) {
    header('Location: install.php?step=profile');
    exit;
}

$errors = [];
$values = $_SESSION['install_db'] ?? ['host' => 'localhost', 'name' => '', 'user' => '', 'password' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!install_csrf_verify($_POST['_token'] ?? null)) {
        $errors['_token'] = 'This form expired, please try again.';
    } else {
        $values = [
            'host'     => trim($_POST['host'] ?? ''),
            'name'     => trim($_POST['name'] ?? ''),
            'user'     => trim($_POST['user'] ?? ''),
            'password' => (string) ($_POST['password'] ?? ''),
        ];

        foreach (['host', 'name', 'user'] as $field) {
            if ($values[$field] === '') {
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }

        if (empty($errors)) {
            $connectionError = install_test_db_connection($values['host'], $values['name'], $values['user'], $values['password']);
            if ($connectionError !== null) {
                $errors['connection'] = $connectionError;
            }
        }

        if (empty($errors)) {
            $_SESSION['install_db'] = $values;
            header('Location: install.php?step=installing');
            exit;
        }
    }
}

$step = 'database';
require INSTALL_ROOT . 'install/header.php';
?>
<h2 class="h5 mb-1">Database configuration</h2>
<p class="text-muted mb-4">The database and user need to already exist &mdash; ThunderPHP will create the tables in it next.</p>

<?php if (!empty($errors['connection'])): ?>
    <div class="alert alert-danger">Couldn't connect: <?= install_esc($errors['connection']) ?></div>
<?php elseif (!empty($errors['_token'])): ?>
    <div class="alert alert-danger"><?= install_esc($errors['_token']) ?></div>
<?php endif; ?>

<form method="post">
    <?= install_csrf_field() ?>

    <div class="mb-3">
        <label class="form-label" for="db-host">Database host</label>
        <input type="text" class="form-control <?= isset($errors['host']) ? 'is-invalid' : '' ?>" id="db-host" name="host" value="<?= install_esc($values['host']) ?>">
        <?php if (isset($errors['host'])): ?><div class="invalid-feedback"><?= install_esc($errors['host']) ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label" for="db-name">Database name</label>
        <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="db-name" name="name" value="<?= install_esc($values['name']) ?>">
        <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= install_esc($errors['name']) ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label" for="db-user">Database username</label>
        <input type="text" class="form-control <?= isset($errors['user']) ? 'is-invalid' : '' ?>" id="db-user" name="user" value="<?= install_esc($values['user']) ?>">
        <?php if (isset($errors['user'])): ?><div class="invalid-feedback"><?= install_esc($errors['user']) ?></div><?php endif; ?>
    </div>

    <div class="mb-4">
        <label class="form-label" for="db-password">Database password</label>
        <input type="password" class="form-control" id="db-password" name="password" value="<?= install_esc($values['password']) ?>">
    </div>

    <button type="submit" class="btn btn-primary w-100">Test connection &amp; continue</button>
</form>
<?php
require INSTALL_ROOT . 'install/footer.php';
