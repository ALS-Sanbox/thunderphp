<?php
// Login attempts are tracked per IP+email pair in the database (not in the
// PHP session) so the lockout can't be bypassed by simply clearing cookies
// or opening a private window.

$user = new Siteusers\Siteusers();

$lockout_duration = 15 * 60; // seconds
$max_attempts = 5;

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$email = trim((string) ($_POST['email'] ?? ''));

$db = new \Core\Database();
$existing = $db->fetch(
    "SELECT * FROM login_attempts WHERE ip_address = ? AND email = ?",
    [$ip, $email]
);

$attempts = 0;
if ($existing) {
    $elapsed = time() - strtotime($existing->first_attempt_at);

    if ($elapsed > $lockout_duration) {
        // Lockout window has passed - start counting fresh.
        $db->query("DELETE FROM login_attempts WHERE id = ?", [$existing->id]);
        $existing = null;
    } else {
        $attempts = (int) $existing->attempts;
    }
}

if ($attempts < $max_attempts) {
    if (csrf_verify($req->post('_token'))) {
        $postdata = $_POST;

        $row = $user->first(['email' => $email]);

        if ($row && password_verify($postdata['password'] ?? '', $row->password)) {
            $ses->auth($row);

            if ($existing) {
                $db->query("DELETE FROM login_attempts WHERE id = ?", [$existing->id]);
            }

            redirect('home');
        }

        message('Wrong email or password!', 'fail');
    } else {
        message('Form expired! Please refresh', 'fail');
    }

    if ($existing) {
        $db->query("UPDATE login_attempts SET attempts = attempts + 1 WHERE id = ?", [$existing->id]);
    } else {
        $db->query(
            "INSERT INTO login_attempts (ip_address, email, attempts, first_attempt_at) VALUES (?, ?, 1, ?)",
            [$ip, $email, date('Y-m-d H:i:s')]
        );
    }
} else {
    message('Too many failed attempts. Try again later.', 'fail');
}
