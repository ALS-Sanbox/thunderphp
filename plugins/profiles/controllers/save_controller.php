<?php

$vars = get_value();
$ses = new \Core\Session();
$req = new \Core\Request();
$profileId = (int) URL(1);

if (!$ses->is_logged_in() || $ses->user('id') != $profileId) {
    redirect($vars['plugin_route'] . '/' . $profileId);
}

if (!csrf_verify($req->post('_token'))) {
    message('Form expired! Please refresh.', 'fail');
    redirect($vars['plugin_route'] . '/' . $profileId);
}

$postdata = $req->post();
$filedata = $req->files();
$errors = [];

$firstName = trim((string) ($postdata['first_name'] ?? ''));
$lastName = trim((string) ($postdata['last_name'] ?? ''));
$bio = trim((string) ($postdata['bio'] ?? ''));
$website = trim((string) ($postdata['website'] ?? ''));

if ($firstName === '' || !preg_match('/^[a-zA-Z]+$/', $firstName)) {
    $errors[] = 'First name can only have letters with no spaces.';
}
if ($lastName === '' || !preg_match('/^[a-zA-Z]+$/', $lastName)) {
    $errors[] = 'Last name can only have letters with no spaces.';
}
if ($website !== '' && !filter_var($website, FILTER_VALIDATE_URL)) {
    $errors[] = 'Website must be a valid URL.';
}

$db = new \Core\Database();
$user = $db->fetch("SELECT id, image FROM siteusers WHERE id = ? AND deleted = 0", [$profileId]);

if (!$user) {
    message('Account not found.', 'fail');
    redirect($vars['plugin_route'] . '/' . $profileId);
}

$newImage = null;
if (!empty($filedata['image']) && $filedata['image']['error'] != UPLOAD_ERR_NO_FILE) {
    $uploaded = $req->upload_files('image');

    if (!is_array($uploaded)) {
        $newImage = $uploaded;
    } else {
        $errors = array_merge($errors, $req->upload_errors);
    }
}

if (!empty($errors)) {
    message(implode(' ', $errors), 'fail');
    redirect($vars['plugin_route'] . '/' . $profileId);
}

$image = $newImage ?: $user->image;

$db->query(
    "UPDATE siteusers SET first_name = ?, last_name = ?, image = ?, date_updated = ? WHERE id = ?",
    [$firstName, $lastName, $image, date('Y-m-d H:i:s'), $profileId]
);

if ($newImage && !empty($user->image) && $user->image !== 'assets/images/no_image.jpg') {
    $req->delete_file($user->image);
}

$existingProfile = $db->fetch("SELECT id FROM user_profiles WHERE user_id = ?", [$profileId]);

if ($existingProfile) {
    $db->query(
        "UPDATE user_profiles SET bio = ?, website = ?, date_updated = ? WHERE id = ?",
        [$bio, $website, date('Y-m-d H:i:s'), $existingProfile->id]
    );
} else {
    $db->query(
        "INSERT INTO user_profiles (user_id, bio, website, date_updated) VALUES (?, ?, ?, ?)",
        [$profileId, $bio, $website, date('Y-m-d H:i:s')]
    );
}

$freshUser = $db->fetch("SELECT * FROM siteusers WHERE id = ?", [$profileId]);
if ($freshUser) {
    $ses->auth($freshUser);
}

message('Profile updated successfully!', 'success');
redirect($vars['plugin_route'] . '/' . $profileId);
