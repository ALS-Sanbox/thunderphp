<link rel="stylesheet" type="text/css" href="<?=plugin_http_path('assets/css/style.css')?>">

<main class="d-flex justify-content-center align-items-center vh-100" id="reset">
    <section class="login-container text-center">
        <img src="uploads/TP.png" alt="Form Logo" class="form-logo">
        <h2 class="mb-4">Reset Password</h2>

        <?php $flash = message(); ?>
        <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> text-center">
            <?= esc($flash['text']) ?>
        </div>
        <?php endif; ?>

        <?php if ($tokenRow): ?>
        <form method="POST">
            <input type="hidden" name="_token" value="<?= csrf() ?>">
            <input type="hidden" name="token" value="<?= esc($token) ?>">
            <div class="mb-3">
              <label for="password" class="form-label">New Password:</label>
              <input type="password" id="password" name="password" class="form-control" required />
            </div>
            <div class="mb-3">
              <label for="confirmPassword" class="form-label">Confirm Password:</label>
              <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-custom w-100 py-2 mb-3">Set New Password</button>
        </form>
        <?php else: ?>
        <p>This password reset link is invalid or has expired.</p>
        <div class="mt-3">
            <p><a href="<?=ROOT?>/<?=$vars['forgot_page']?>">Request a new link</a></p>
        </div>
        <?php endif; ?>

        <div class="mt-3">
            <p>Remember your password? <a href="<?=ROOT?>/<?=$vars['login_page']?>">Login</a></p>
        </div>
    </section>
</main>

<script src="<?=plugin_http_path('assets/js/plugin.js')?>"></script>
