<?php
// controllers/forgot_controller.php

$user = new Siteusers\Siteusers();

csrf_verify($req->post('_token'));

$email = trim((string) $req->post('email'));
$row = $email !== '' ? $user->first(['email' => $email, 'deleted' => 0]) : false;

if ($row) {
    $resets = new PasswordResets\PasswordResets();
    $resets->invalidateForUser($row->id);

    $rawToken = bin2hex(random_bytes(32));
    $resets->createToken($row->id, $rawToken, 60);

    $resetLink = ROOT . '/' . $vars['reset_page'] . '?token=' . $rawToken;
    $siteName = setting('site_name') ?: 'ThunderPHP';

    $htmlBody = '<p>Someone requested a password reset for your ' . esc($siteName) . ' account.</p>'
        . '<p><a href="' . esc($resetLink) . '">Reset your password</a></p>'
        . '<p>This link expires in 60 minutes. If you did not request this, you can safely ignore this email.</p>';

    $mailer = new \Core\Mailer();
    if (!$mailer->send($row->email, 'Reset your password', $htmlBody)) {
        error_log('Password reset email failed: ' . $mailer->error);
    }
}

// Same message whether or not the email matched an account, so a requester
// can't use this form to discover which emails have accounts here.
message("If that email address is in our system, we've sent a password reset link.", "success");
redirect($vars['forgot_page']);
