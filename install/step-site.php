<?php
defined('INSTALL_ROOT') or die('Direct access not permitted');

if (!file_exists(INSTALL_ROOT . 'config.php')) {
    header('Location: install.php?step=database');
    exit;
}

require_once INSTALL_ROOT . 'config.php';

if (install_already_completed()) {
    header('Location: install.php?step=done');
    exit;
}

$errors = [];
$values = [
    'site_name'  => 'ThunderPHP',
    'admin_email'=> '',
    'first_name' => '',
    'last_name'  => '',
    'email'      => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!install_csrf_verify($_POST['_token'] ?? null)) {
        $errors['_token'] = 'This form expired, please try again.';
    } else {
        $values['site_name'] = trim($_POST['site_name'] ?? '') ?: 'ThunderPHP';
        $values['admin_email'] = trim($_POST['admin_email'] ?? '');
        $values['first_name'] = trim($_POST['first_name'] ?? '');
        $values['last_name'] = trim($_POST['last_name'] ?? '');
        $values['email'] = trim($_POST['email'] ?? '');

        $errors = install_validate_admin_account([
            'first_name'       => $values['first_name'],
            'last_name'        => $values['last_name'],
            'email'            => $values['email'],
            'password'         => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
        ]);

        if (empty($values['admin_email']) || !filter_var($values['admin_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['admin_email'] = 'A valid site contact email is required.';
        }

        if (empty($errors)) {
            try {
                $pdo = new PDO(DB_DRIVER . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASSWORD, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);

                $existing = $pdo->prepare("SELECT id FROM siteusers WHERE email = ?");
                $existing->execute([$values['email']]);
                if ($existing->fetch()) {
                    $errors['email'] = 'That email is already in use.';
                }

                if (empty($errors)) {
                    $pdo->beginTransaction();

                    $stmt = $pdo->prepare(
                        "INSERT INTO siteusers (first_name, last_name, email, password, date_created) VALUES (?, ?, ?, ?, ?)"
                    );
                    $stmt->execute([
                        $values['first_name'],
                        $values['last_name'],
                        $values['email'],
                        password_hash($_POST['password'], PASSWORD_DEFAULT),
                        date('Y-m-d H:i:s'),
                    ]);
                    $newUserId = (int) $pdo->lastInsertId();

                    $roleStmt = $pdo->prepare("SELECT id FROM user_roles WHERE role = 'admin' LIMIT 1");
                    $roleStmt->execute();
                    $adminRoleId = $roleStmt->fetchColumn();

                    if ($adminRoleId !== false) {
                        $mapStmt = $pdo->prepare(
                            "INSERT INTO user_roles_map (role_id, user_id, disabled) VALUES (?, ?, 0)"
                        );
                        $mapStmt->execute([(int) $adminRoleId, $newUserId]);
                    }

                    $settingsStmt = $pdo->prepare("UPDATE settings SET value = ? WHERE `key` = ?");
                    $settingsStmt->execute([$values['site_name'], 'site_name']);
                    $settingsStmt->execute([$values['admin_email'], 'admin_email']);
                    $settingsStmt->execute([install_detect_root_url(), 'site_url']);

                    $pdo->commit();

                    header('Location: install.php?step=done');
                    exit;
                }
            } catch (\Throwable $e) {
                if (isset($pdo) && $pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $errors['_general'] = 'Could not create the admin account: ' . $e->getMessage();
            }
        }
    }
}

$step = 'site';
require INSTALL_ROOT . 'install/header.php';
?>
<h2 class="h5 mb-1">Site &amp; admin account</h2>
<p class="text-muted mb-4">This creates the one real admin account for this install &mdash; there's no default account shipped with ThunderPHP.</p>

<?php if (!empty($errors['_token']) || !empty($errors['_general'])): ?>
    <div class="alert alert-danger"><?= install_esc($errors['_token'] ?? $errors['_general']) ?></div>
<?php endif; ?>

<form method="post">
    <?= install_csrf_field() ?>

    <h3 class="h6 mt-2">Site</h3>
    <div class="mb-3">
        <label class="form-label" for="site-name">Site name</label>
        <input type="text" class="form-control" id="site-name" name="site_name" value="<?= install_esc($values['site_name']) ?>">
    </div>
    <div class="mb-4">
        <label class="form-label" for="admin-email">Site contact email</label>
        <input type="email" class="form-control <?= isset($errors['admin_email']) ? 'is-invalid' : '' ?>" id="admin-email" name="admin_email" value="<?= install_esc($values['admin_email']) ?>">
        <?php if (isset($errors['admin_email'])): ?><div class="invalid-feedback"><?= install_esc($errors['admin_email']) ?></div><?php endif; ?>
    </div>

    <h3 class="h6">Admin account</h3>
    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label" for="first-name">First name</label>
            <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" id="first-name" name="first_name" value="<?= install_esc($values['first_name']) ?>">
            <?php if (isset($errors['first_name'])): ?><div class="invalid-feedback"><?= install_esc($errors['first_name']) ?></div><?php endif; ?>
        </div>
        <div class="col-6 mb-3">
            <label class="form-label" for="last-name">Last name</label>
            <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" id="last-name" name="last_name" value="<?= install_esc($values['last_name']) ?>">
            <?php if (isset($errors['last_name'])): ?><div class="invalid-feedback"><?= install_esc($errors['last_name']) ?></div><?php endif; ?>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label" for="admin-account-email">Email (used to log in)</label>
        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="admin-account-email" name="email" value="<?= install_esc($values['email']) ?>">
        <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= install_esc($errors['email']) ?></div><?php endif; ?>
    </div>
    <div class="mb-3">
        <label class="form-label" for="password">Password</label>
        <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password">
        <?php if (isset($errors['password'])): ?><div class="invalid-feedback"><?= install_esc($errors['password']) ?></div><?php else: ?><div class="form-text">At least 8 characters, with one special character.</div><?php endif; ?>
    </div>
    <div class="mb-4">
        <label class="form-label" for="confirm-password">Confirm password</label>
        <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" id="confirm-password" name="confirm_password">
        <?php if (isset($errors['confirm_password'])): ?><div class="invalid-feedback"><?= install_esc($errors['confirm_password']) ?></div><?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary w-100">Create account &amp; finish</button>
</form>
<?php
require INSTALL_ROOT . 'install/footer.php';
