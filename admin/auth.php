<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/config.php';

$admin_user = env_value('ADMIN_USERNAME', 'admin');
$admin_pass_hash = env_value('ADMIN_PASSWORD_HASH', '');
$admin_name = env_value('ADMIN_NAME', 'Admin');
$admin_role = env_value('ADMIN_ROLE', 'Администратор');

// Login attempt rate limiting
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_SECONDS', 300); // 5 minutes

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function is_login_locked() {
    if (!isset($_SESSION['login_attempts']) || !isset($_SESSION['login_locked_until'])) {
        return false;
    }
    if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
        if (time() < $_SESSION['login_locked_until']) {
            return true;
        }
        // Lockout expired, reset
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['login_locked_until']);
    }
    return false;
}

function record_failed_login() {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }
    $_SESSION['login_attempts']++;
    if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
        $_SESSION['login_locked_until'] = time() + LOGIN_LOCKOUT_SECONDS;
    }
}

function reset_login_attempts() {
    $_SESSION['login_attempts'] = 0;
    unset($_SESSION['login_locked_until']);
}

function check_auth($user, $pass) {
    global $admin_user, $admin_pass_hash, $admin_name, $admin_role;

    if (is_login_locked()) {
        return false;
    }

    if (trim($user) !== $admin_user) {
        record_failed_login();
        return false;
    }

    if (empty($admin_pass_hash)) {
        error_log('ADMIN_PASSWORD_HASH is not set; rejecting admin login attempt.');
        record_failed_login();
        return false;
    }
    $authenticated = password_verify($pass, $admin_pass_hash);

    if ($authenticated) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_name'] = $admin_name;
        $_SESSION['admin_role'] = $admin_role;
        
        // Load admin email from site repository
        try {
            if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
                require_once __DIR__ . '/../vendor/autoload.php';
                $siteRepo = new \App\Repositories\SiteRepository();
                $contact = $siteRepo->getContactInfo();
                $_SESSION['admin_email'] = $contact['email'] ?? 'admin@rentcarphuket.ru';
            }
        } catch (\Throwable $e) {
            $_SESSION['admin_email'] = 'admin@rentcarphuket.ru';
        }
        
        reset_login_attempts();
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);
        return true;
    }

    record_failed_login();
    return false;
}

function logout() {
    session_destroy();
    header("Location: /admin/login");
    exit;
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: /admin/login");
        exit;
    }
}
