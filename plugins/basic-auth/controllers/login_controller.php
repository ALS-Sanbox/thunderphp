<?php
$user = new Siteusers\Siteusers();


if ($ses->startSession() === 1) { // Ensure the session starts using your custom method
    // Set time limit (15 minutes)
    $lockout_duration = 15 * 60; 

    // Get login attempts and first attempt time from the session using $ses->get()
    $attempts = $ses->get('login_attempts') ?? 0;
    $first_attempt_time = $ses->get('first_attempt_time') ?? null;

    if ($first_attempt_time && (time() - $first_attempt_time) > $lockout_duration) {
        $attempts = 0;
        $ses->set('first_attempt_time', null);  // Remove first attempt time using $ses
    }

    if ($attempts < 5) {
        if (csrf_verify($req->post('_token'))) {
            $postdata = $_POST;
        
            $row = $user->first(['email' => $postdata['email']]);
        
            if ($row) {
                if (password_verify($postdata['password'], $row->password)) {
                    $ses->auth($row);  // Authenticates the user and stores the session data
                    $ses->set('login_attempts', 0);  // Reset login attempts using $ses
                    $ses->set('first_attempt_time', null);  // Remove first attempt time on successful login
                    redirect('home');
                }
            }
        
            message('Wrong email or password!','fail');
        } else {
            message('Form expired! Please refresh','fail');
        }

        if ($attempts == 0) {
            $ses->set('first_attempt_time', time());  // Set the first attempt time using $ses
        }

        $ses->set('login_attempts', $attempts + 1);  // Increment login attempts using $ses
    } else {
        message('Too many failed attempts. Try again later.','fail');
    }
}