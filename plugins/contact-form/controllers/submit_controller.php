<?php

$postdata = $req->post();

if (!empty($postdata['website'])) {
    // Honeypot field - real visitors never see or fill it (hidden via CSS),
    // so a filled-in value means a bot submitted the form. Pretend success
    // without saving anything or sending mail.
    message("Thanks! Your message has been sent.", "success");
    redirect($plugin_route);
}

if (!csrf_verify($postdata['_token'] ?? null)) {
    message("Form expired! Please refresh the page and try again.", "fail");
    redirect($plugin_route);
}

$submissions = new \ContactForm\ContactSubmissions;
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

if ($ip !== '' && $submissions->recentCountForIp($ip) >= 5) {
    message("Too many messages sent recently. Please try again later.", "fail");
    redirect($plugin_route);
}

$data = [
    'name'         => trim((string) ($postdata['name'] ?? '')),
    'email'        => trim((string) ($postdata['email'] ?? '')),
    'subject'      => trim((string) ($postdata['subject'] ?? '')),
    'message'      => trim((string) ($postdata['message'] ?? '')),
    'ip_address'   => $ip,
    'date_created' => date('Y-m-d H:i:s'),
];

if (!$submissions->validate_insert($data)) {
    set_value('errors', $submissions->errors);
    set_value('old', $data);
    message(implode(' ', $submissions->errors), 'fail');
    redirect($plugin_route);
}

$submissions->create($data);

$recipient = (string) setting('contact_form_recipient_email', '') ?: (string) setting('admin_email', '');

if ($recipient !== '') {
    $mailer = new \Core\Mailer;
    $subject = 'New contact form submission' . ($data['subject'] !== '' ? ': ' . $data['subject'] : '');
    $body = '<p><strong>Name:</strong> ' . esc($data['name']) . '</p>'
        . '<p><strong>Email:</strong> ' . esc($data['email']) . '</p>'
        . '<p><strong>Message:</strong><br>' . nl2br(esc($data['message'])) . '</p>';
    $mailer->send($recipient, $subject, $body);
}

message("Thanks! Your message has been sent.", "success");
redirect($plugin_route);
