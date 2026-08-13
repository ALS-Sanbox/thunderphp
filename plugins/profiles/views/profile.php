<?php
$vars = get_value();
$ses = new \Core\Session();
$profileId = (int) URL(1);

if (!$ses->is_logged_in()) {
    redirect($vars['login_page']);
}

if ($ses->user('id') != $profileId) {
    ?>
    <div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px; margin-top: 80px;">
      <div class="card-body">
        <h5 class="card-title text-danger fw-bold">Access Denied</h5>
        <p class="card-text text-muted">You can only view your own profile.</p>
      </div>
    </div>
    <?php
    return;
}

$db = new \Core\Database();
$user = $db->fetch("SELECT id, first_name, last_name, email, image FROM siteusers WHERE id = ? AND deleted = 0", [$profileId]);

if (!$user) {
    ?>
    <div id="denied" class="card text-center shadow-lg border-danger d-flex justify-content-center align-items-center mx-auto" style="max-width: 400px; margin-top: 80px;">
      <div class="card-body">
        <h5 class="card-title text-danger fw-bold">Not Found</h5>
        <p class="card-text text-muted">This account no longer exists.</p>
      </div>
    </div>
    <?php
    return;
}

$profile = $db->fetch("SELECT bio, website FROM user_profiles WHERE user_id = ?", [$profileId]);
$bio = $profile->bio ?? '';
$website = $profile->website ?? '';
$flash = message();
?>
<main class="container" style="max-width:600px;margin-top:60px;margin-bottom:60px;">
  <h2 class="mb-4">My Profile</h2>

  <?php if ($flash): ?>
  <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
    <?= esc($flash['text']) ?>
  </div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="<?= csrf() ?>">

    <div class="mb-3 text-center">
      <img src="<?= esc(get_image($user->image)) ?>" alt="" style="width:96px;height:96px;border-radius:50%;object-fit:cover;">
    </div>
    <div class="mb-3">
      <label class="form-label">Avatar</label>
      <input type="file" name="image" class="form-control" accept="image/*">
    </div>
    <div class="mb-3">
      <label class="form-label">First Name</label>
      <input type="text" name="first_name" class="form-control" value="<?= esc($user->first_name) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Last Name</label>
      <input type="text" name="last_name" class="form-control" value="<?= esc($user->last_name) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" class="form-control" value="<?= esc($user->email) ?>" disabled>
      <div class="form-text">Email can't be changed here.</div>
    </div>
    <div class="mb-3">
      <label class="form-label">Bio</label>
      <textarea name="bio" class="form-control" rows="4" maxlength="500"><?= esc($bio) ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Website</label>
      <input type="url" name="website" class="form-control" value="<?= esc($website) ?>" placeholder="https://example.com">
    </div>

    <button type="submit" class="btn btn-primary">Save Profile</button>
  </form>
</main>
