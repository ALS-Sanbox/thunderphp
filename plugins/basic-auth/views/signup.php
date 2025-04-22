<link rel="stylesheet" type="text/css" href="<?=plugin_http_path('assets/css/style.css')?>">

<main class="d-flex justify-content-center align-items-center vh-100 mt-4" id="signupPage">
    <section class="signup-container text-center">
        <div class="signup-header">
            <img src="uploads/TP.png" alt="Form Logo" class="form-logo">
            <h2 class="mb-4">Signup</h2> 
        </div>
        <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= csrf() ?>">
         <?php if ($msg = message()): ?>
            <div class="alert text-center <?= ($msg['type'] === 'success') ? 'alert-success' : 'alert-danger' ?>">
                <?= esc($msg['text']) ?>
            </div>
        <?php endif ?>

        <?php if(!empty($errors['first_name']) && $msg = message()):?>
            <div class="alert alert-danger text-center">
                <?=esc(message('<?=$errors["first_name"]?>',true))?>
            </div>
        <?php endif ?>
            <div class="row">
                <div class="column-left">
                    <img src="<?=ROOT?>/assets/images/no_image.jpg" alt="Profile Picture" id="profilePicPreview">
                    <a href="javascript:void(0);" class="upload-link" onclick="document.getElementById('profilePic').click();">Upload</a>
                    <input value="<?=old_value('profilePic')?>" type="file" id="profilePic" name="profilePic" class="form-control d-none" accept="image/*" onchange="previewImage(event)">
                </div>
                <div class="column-right">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name:</label>
                        <input value="<?=old_value('first_name')?>" type="text" id="first_name" name="first_name" class="form-control" required />
                        
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name:</label>
                        <input value="<?=old_value('last_name')?>" type="text" id="last_name" name="last_name" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input value="<?=old_value('email')?>" type="email" id="email" name="email" class="form-control" required />
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required />
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required />
            </div>
            <div class="d-flex justify-content-between mb-3">
                <a href="<?=ROOT?>/<?=$vars['login_page']?>">Already have an account? Login</a>
            </div>

            <button type="submit" class="btn btn-custom w-100 py-2 mb-3">Sign Up</button>
            <button type="button" class="btn btn-google w-100 py-2">
                <img src="https://img.icons8.com/color/16/000000/google-logo.png" class="me-2" alt="Google Icon"/> Sign up with Google
            </button>
        </form>
    </section>
</main>

<script src="<?=plugin_http_path('assets/js/plugin.js')?>"></script>