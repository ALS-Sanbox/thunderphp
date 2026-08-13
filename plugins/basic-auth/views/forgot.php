<link rel="stylesheet" type="text/css" href="<?=plugin_http_path('assets/css/style.css')?>">

<main class="d-flex justify-content-center align-items-center vh-100" id="forgot">
    <section class="login-container text-center">
        <img src="uploads/TP.png" alt="Form Logo" class="form-logo">
        <h2 class="mb-4">Forgot Password</h2>

        <form method="POST">
            <input type="hidden" name="_token" value="<?= csrf() ?>">
            <?php $flash = message(); ?>
            <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> text-center">
                <?= esc($flash['text']) ?>
            </div>
            <?php endif; ?>
            <div class="column-right">
            <div class="mb-3">
              <label for="email" class="form-label">Email:</label>
              <input value="<?=old_value('email')?>" type="email" id="email" name="email" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-custom w-100 py-2 mb-3">Reset Password</button>
            </div>
        </form>

        <div class="mt-3">
            <p>Remember your password? <a href="<?=ROOT?>/<?=$vars['login_page']?>">Login</a></p>
        </div>
    </section>
</main>

<script src="<?=plugin_http_path('assets/js/plugin.js')?>"></script>