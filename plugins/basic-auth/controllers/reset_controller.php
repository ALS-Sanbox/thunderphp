<?php
// controllers/reset_controller.php

$resets = new PasswordResets\PasswordResets();
$user = new Siteusers\Siteusers();

csrf_verify($req->post('_token'));

$token = (string) $req->post('token');
$tokenRow = $token !== '' ? $resets->validateToken($token) : false;

if (!$tokenRow) {
    message('This password reset link is invalid or has expired.', 'fail');
    redirect($vars['forgot_page']);
}

$password = (string) $req->post('password');
$confirm = (string) $req->post('confirmPassword');

if ($password !== $confirm) {
    message('Passwords do not match.', 'fail');
} elseif (!$user->validate_update(['password' => $password])) {
    message(implode(' ', $user->errors), 'fail');
} else {
    $user->update($tokenRow->user_id, [
        'password'     => password_hash($password, PASSWORD_DEFAULT),
        'date_updated' => date('Y-m-d H:i:s'),
    ]);
    $resets->markUsed($tokenRow->id);

    message('Your password has been reset. Please log in.', 'success');
    redirect($vars['login_page']);
}
