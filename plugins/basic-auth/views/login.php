<link rel="stylesheet" type="text/css" href="<?=plugin_http_path('assets/css/style.css')?>">

<main class="d-flex justify-content-center align-items-center vh-100" id="loginPage">
    <section class="login-container text-center">
    <img src="uploads/TP.png" alt="Form Logo" class="form-logo">
      <h2 class="mb-4">Login</h2>
      <form method="POST">
      <input type="hidden" name="_token" value="<?= csrf() ?>">
        <?php if(message()):?>
        <div class="alert alert-danger text-center ">
            <?=esc(message('',true))?>
        </div>
        <?php endif?>
        <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input value="<?=old_value('email')?>" type="email" id="email" name="email" class="form-control" required />
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password:</label>
          <input value="<?=old_value('password')?>" type="password" id="password" name="password" class="form-control" required />
        </div>
        <div class="d-flex justify-content-between mb-3">
          <a href="<?=ROOT?>/<?=$vars['forgot_page']?>">Forgot Password?</a>
        </div>
        <button type="submit" class="btn btn-custom w-100 py-2 mb-3">Login</button>
        <button type="button" class="btn btn-google w-100 py-2">
          <img src="https://img.icons8.com/color/16/000000/google-logo.png" class="me-2" alt="Google Icon"/> Sign in with Google
        </button>
      </form>
      <div class="mt-3">
        <p>Don't have an account? <a href="<?=ROOT?>/<?=$vars['signup_page']?>">Sign up</a></p>
      </div>
    </section>
</main>

<script src="<?=plugin_http_path('assets/js/plugin.js')?>"></script>