<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_client_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function get_logged_client() {
    if (!is_client_logged_in()) return null;
    $userRepo = new \App\Repositories\UserRepository();
    return $userRepo->findById($_SESSION['user_id']);
}

function client_logout() {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    header("Location: /");
    exit;
}

function check_client_auth($email, $password) {
    $userRepo = new \App\Repositories\UserRepository();
    $user = $userRepo->findByEmail($email);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        session_regenerate_id(true);
        return true;
    }
    return false;
}
