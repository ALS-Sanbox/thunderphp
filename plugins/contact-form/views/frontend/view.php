<?php $errors = get_value('errors') ?: []; ?>
<div class="container mt-4 mb-5" style="max-width:600px;">
    <h1 class="h3 mb-4">Contact Us</h1>

    <form method="POST" action="">
        <input type="hidden" name="_token" value="<?= csrf() ?>">

        <!-- Honeypot: hidden from real visitors via CSS, left blank by them; bots that auto-fill every field trip it. -->
        <div style="position:absolute;left:-9999px;" aria-hidden="true">
            <label for="website">Website</label>
            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control<?= !empty($errors['name']) ? ' is-invalid' : '' ?>" id="name" name="name" value="<?= esc(old_value('name')) ?>" required>
            <?php if (!empty($errors['name'])): ?><div class="invalid-feedback"><?= esc($errors['name']) ?></div><?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control<?= !empty($errors['email']) ? ' is-invalid' : '' ?>" id="email" name="email" value="<?= esc(old_value('email')) ?>" required>
            <?php if (!empty($errors['email'])): ?><div class="invalid-feedback"><?= esc($errors['email']) ?></div><?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" class="form-control" id="subject" name="subject" value="<?= esc(old_value('subject')) ?>">
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control<?= !empty($errors['message']) ? ' is-invalid' : '' ?>" id="message" name="message" rows="5" required><?= esc(old_value('message')) ?></textarea>
            <?php if (!empty($errors['message'])): ?><div class="invalid-feedback"><?= esc($errors['message']) ?></div><?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>
