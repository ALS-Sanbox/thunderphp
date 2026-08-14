<?php

/**
 * Plugin Name: Google Sign-In Plugin
 * Description: Adds "Sign in with Google" to the login page (OAuth 2.0 authorization code flow), auto-linking by verified email to any existing account.
 * Version: 1.0
 * Author: Afro Lion
 */

set_value([
    'admin_route'  => 'admin',
    'plugin_route' => 'google-auth',
    'table'        => ['google_accounts_table' => 'google_accounts'],
]);

$db = new \Core\Database;
$table = get_value()['table'];

if (!$db->tableExists($table)) {
    dd("Missing database tables in " . plugin_id() . " plugin: " . implode(",", $db->missing_tables));
    die;
}

/**
 * Writes directly via \Core\Database, same reason as seo_set_setting() in
 * plugins/seo/plugin.php: the settings plugin's own Settings model can't be
 * safely instantiated from outside plugins/settings/ (the autoloader
 * resolves an unqualified plugin model class relative to the *caller's*
 * file, not the class's own plugin).
 */
function google_auth_set_setting(\Core\Database $db, string $key, string $value): bool {
    $row = $db->fetch("SELECT id FROM settings WHERE `key` = ?", [$key]);

    if ($row) {
        $db->query("UPDATE settings SET `value` = ?, updated_at = NOW() WHERE id = ?", [$value, $row->id]);
        return true;
    }

    $db->query("INSERT INTO settings (`key`, `value`, `type`, `environment`) VALUES (?, ?, 'string', 'production')", [$key, $value]);
    return true;
}

function google_auth_redirect_uri(): string {
    return ROOT . '/google-auth/callback';
}

function google_auth_authorize_url(string $clientId, string $redirectUri, string $state): string {
    $params = [
        'client_id'     => $clientId,
        'redirect_uri'  => $redirectUri,
        'response_type' => 'code',
        'scope'         => 'openid email profile',
        'state'         => $state,
        'prompt'        => 'select_account',
    ];
    return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}

/** Exchanges an authorization code for an access token. Returns null on any failure. */
function google_auth_exchange_code(string $code, string $clientId, string $clientSecret, string $redirectUri): ?string {
    $postFields = http_build_query([
        'code'          => $code,
        'client_id'     => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri'  => $redirectUri,
        'grant_type'    => 'authorization_code',
    ]);

    $context = stream_context_create([
        'http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content'       => $postFields,
            'timeout'       => 10,
            'ignore_errors' => true,
        ],
        'ssl' => [
            'verify_peer'      => true,
            'verify_peer_name' => true,
        ],
    ]);

    $response = @file_get_contents('https://oauth2.googleapis.com/token', false, $context);
    if ($response === false) return null;

    $data = json_decode($response, true);
    return is_array($data) && !empty($data['access_token']) ? (string) $data['access_token'] : null;
}

/** Fetches the signed-in Google user's profile. Returns null on any failure. */
function google_auth_get_userinfo(string $accessToken): ?array {
    $context = stream_context_create([
        'http' => [
            'method'        => 'GET',
            'header'        => "Authorization: Bearer {$accessToken}\r\n",
            'timeout'       => 10,
            'ignore_errors' => true,
        ],
        'ssl' => [
            'verify_peer'      => true,
            'verify_peer_name' => true,
        ],
    ]);

    $response = @file_get_contents('https://openidconnect.googleapis.com/v1/userinfo', false, $context);
    if ($response === false) return null;

    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}

/**
 * Resolves a Google userinfo payload to a siteusers row: already-linked ->
 * log in; unlinked but a verified-matching email already has an account ->
 * link + log in; otherwise create a brand new account + link. Returns null
 * if the payload is unusable (missing/unverified email) or account
 * creation fails. Kept independent of the actual HTTP calls above so it can
 * be exercised directly with fixture data in tests.
 */
function google_auth_resolve_user(array $userinfo, \Core\Database $db) {
    if (empty($userinfo['sub']) || empty($userinfo['email']) || empty($userinfo['email_verified'])) {
        return null;
    }

    $sub = (string) $userinfo['sub'];
    $email = (string) $userinfo['email'];

    $accounts = new \GoogleAuth\GoogleAccounts;
    $siteusers = new \Siteusers\Siteusers;

    $link = $accounts->findBySub($sub);
    if ($link) {
        return $siteusers->find($link->user_id);
    }

    $existing = $siteusers->first(['email' => $email, 'deleted' => 0]);
    if ($existing) {
        $accounts->link((int) $existing->id, $sub, $email);
        return $existing;
    }

    // Siteusers::validate_insert() (not used for this path - see below)
    // requires letters-only names; sanitize Google's name claims the same
    // way rather than rejecting sign-in over an unusual name. Names outside
    // a-zA-Z (accented, non-Latin scripts) fall back to a generic label.
    $firstName = preg_replace('/[^a-zA-Z]/', '', (string) ($userinfo['given_name'] ?? '')) ?: 'Google';
    $lastName  = preg_replace('/[^a-zA-Z]/', '', (string) ($userinfo['family_name'] ?? '')) ?: 'User';

    $newUser = [
        'first_name'   => $firstName,
        'last_name'    => $lastName,
        'image'        => '',
        'email'        => $email,
        // Random, never-displayed password - satisfies the NOT NULL column
        // for an account that only ever signs in via Google. validate_insert()
        // is intentionally skipped here: it's built for the manual signup
        // form (password strength, etc.), none of which applies to a Google-
        // verified email and a placeholder password nobody will ever type.
        'password'     => password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT),
        'date_created' => date('Y-m-d H:i:s'),
    ];

    if (!$siteusers->create($newUser)) {
        return null;
    }

    $newId = $siteusers->insert_id;
    $accounts->link($newId, $sub, $email);

    return $siteusers->find($newId);
}

function google_auth_handle_start(): void {
    if (!setting('google_oauth_enabled') || !setting('google_oauth_client_id') || !setting('google_oauth_client_secret')) {
        message('Google sign-in is not available right now.', 'fail');
        redirect('login');
    }

    $ses = new \Core\Session;
    $state = bin2hex(random_bytes(24));
    $ses->set('google_oauth_state', $state);

    $url = google_auth_authorize_url(
        (string) setting('google_oauth_client_id'),
        google_auth_redirect_uri(),
        $state
    );

    header('Location: ' . $url);
    die;
}

function google_auth_handle_callback(): void {
    $req = new \Core\Request;
    $ses = new \Core\Session;

    $code = (string) $req->get('code');
    $state = (string) $req->get('state');
    $storedState = (string) ($ses->get('google_oauth_state') ?: '');
    $ses->set('google_oauth_state', null);

    if ($code === '' || $state === '' || $storedState === '' || !hash_equals($storedState, $state)) {
        message('Google sign-in failed (invalid request). Please try again.', 'fail');
        redirect('login');
    }

    $clientId = (string) setting('google_oauth_client_id');
    $clientSecret = (string) setting('google_oauth_client_secret');

    if ($clientId === '' || $clientSecret === '') {
        message('Google sign-in is not configured.', 'fail');
        redirect('login');
    }

    $accessToken = google_auth_exchange_code($code, $clientId, $clientSecret, google_auth_redirect_uri());

    if (!$accessToken) {
        message('Google sign-in failed. Please try again.', 'fail');
        redirect('login');
    }

    $userinfo = google_auth_get_userinfo($accessToken);

    if (!$userinfo) {
        message('Google sign-in failed. Please try again.', 'fail');
        redirect('login');
    }

    $db = new \Core\Database;
    $user = google_auth_resolve_user($userinfo, $db);

    if (!$user) {
        message('Could not sign you in with Google. Please try again, or use email/password.', 'fail');
        redirect('login');
    }

    $ses->auth($user);
    redirect('home');
}

add_filter('permissions', function ($permissions) {
    $permissions[] = 'manage_google_auth';
    return $permissions;
});

add_filter('basic-admin_before_admin_links', function ($links) {
    if (user_can('manage_google_auth')) {
        $vars = get_value();

        $links[] = (object) [
            'title'  => 'Google Sign-In',
            'link'   => ROOT . '/' . $vars['admin_route'] . '/' . $vars['plugin_route'],
            'icon'   => 'google',
            'parent' => 0,
        ];
    }
    return $links;
});

add_action('login_form_after', function () {
    if (!setting('google_oauth_enabled') || !setting('google_oauth_client_id') || !setting('google_oauth_client_secret')) {
        return;
    }
    ?>
    <div class="d-flex align-items-center my-3" style="gap:10px;">
        <hr class="flex-grow-1"><span class="text-muted small">or</span><hr class="flex-grow-1">
    </div>
    <a href="<?= ROOT ?>/google-auth/start" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center" style="gap:10px;">
        <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
            <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z"/>
            <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
            <path fill="#FBBC05" d="M3.964 10.707A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.707V4.961H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.039l3.007-2.332z"/>
            <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.293C4.672 5.166 6.656 3.58 9 3.58z"/>
        </svg>
        Sign in with Google
    </a>
    <?php
});

add_action('view', function () {
    if (page() !== 'google-auth') return;

    switch (URL(1)) {
        case 'start':
            google_auth_handle_start();
            break;
        case 'callback':
            google_auth_handle_callback();
            break;
        default:
            redirect('login');
    }
});

add_action('controller', function () {
    $req = new \Core\Request;
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (URL(1) === $plugin_route && $req->posted()) {
        require plugin_path('controllers/save_controller.php');
    }
});

add_action('basic-admin_main_content', function () {
    $vars = get_value();
    $admin_route = $vars['admin_route'];
    $plugin_route = $vars['plugin_route'];

    if (page() !== $admin_route || URL(1) !== $plugin_route) return;

    require plugin_path('views/admin/view.php');
});
