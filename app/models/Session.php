<?php

namespace Core;

defined('ROOT') or die("Direct script access denied");

class Session {
    private $varKey = 'APP';
    private $userKey = 'USER';

    public function startSession(): int {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return 1;
    }

    public function set(string|array $keyOrArray, mixed $value = null): bool {
        $this->startSession();

        if (is_array($keyOrArray)) {
            foreach ($keyOrArray as $key => $value) {
                $_SESSION[$this->varKey][$key] = $value;
            }
            return true;
        } else {
            $_SESSION[$this->varKey][$keyOrArray] = $value;
            return true;
        }
    }

    public function get(string $key): mixed {
        $this->startSession();

        if (!empty($_SESSION[$this->varKey][$key])) {
            return $_SESSION[$this->varKey][$key];
        }

        return false;
    }

    public function auth(object|array $row): bool {
        $this->startSession();
        $_SESSION[$this->userKey] = $row;
        $_SESSION['last_activity'] = time();

        return true;
    }

    public function is_logged_in(): bool {
        $this->startSession();
        if (empty($_SESSION[$this->userKey])) {
            return false;
        }

        if (is_object($_SESSION[$this->userKey]) || is_array($_SESSION[$this->userKey])) {
            return true;
        }

        return false;
    }

    public function is_admin(): bool {
        if (!$this->is_logged_in()) {
            return false;
        }
    
        $arr = do_filter('before_check_admin', ['is_admin' => false]);
    
        if (!empty($arr['is_admin'])) {
            return true;
        }
    
        // Check if user session has 'role' or 'is_admin' field
        $user = $this->user(); // Get user from session
    
        if (is_array($user) && !empty($user['is_admin'])) {
            return true;
        }
    
        if (is_object($user) && !empty($user->is_admin)) {
            return true;
        }
    
        return false;
    }

    public function reset(): bool {
        session_destroy();
        session_regenerate_id();

        return true;
    }

    public function logout(): bool {
        $this->startSession();
        
        // Destroy session data
        session_unset();
        session_destroy();
        session_regenerate_id(true);
    
        // Redirect to home page
        header("Location: /"); // Adjust URL if needed
        exit();
    }

    public function user(string $key = ''): mixed {
        $this->startSession();

        if (!empty($_SESSION[$this->userKey])) {
            if (empty($key)) {
                return $_SESSION[$this->userKey];
            }

            if (is_object($_SESSION[$this->userKey])) {
                if (!empty($_SESSION[$this->userKey]->$key)) {
                    return $_SESSION[$this->userKey]->$key;
                }
            } else if (is_array($_SESSION[$this->userKey])) {
                if (!empty($_SESSION[$this->userKey][$key])) {
                    return $_SESSION[$this->userKey][$key];
                }
            }
        }

        return null;
    }

    public function all(): mixed {
        $this->startSession();

        if (!empty($_SESSION[$this->varKey])) {
            return $_SESSION[$this->varKey];
        }

        return null;
    }
    
    public function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $token) {
            die('CSRF token validation failed');
        }

        return true;
    }

    public function oldValue(string $key, string $default = '', string $type = 'post'): string {
        if (isset($_SESSION['old_values'][$key])) {
            return $_SESSION['old_values'][$key];
        }

        if ($type === 'post' && isset($_POST[$key])) {
            return $_POST[$key];
        }

        if ($type === 'get' && isset($_GET[$key])) {
            return $_GET[$key];
        }

        return $default;
    }

    public function oldSelect(string $key, string $default = '', string $type = 'post'): string {
        return $this->oldValue($key, $default, $type);
    }

    public function oldChecked(string $key, string $default = '', string $type = 'post'): string {
        $value = $this->oldValue($key, $default, $type);
        return (isset($_POST[$key]) || isset($_GET[$key]) || $value) ? 'checked' : '';
    }
}