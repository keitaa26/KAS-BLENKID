<?php

function esc($conn, $value) {
    return mysqli_real_escape_string($conn, trim($value));
}

function sc($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function require_admin() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['login'])) {
        header('Location: login.php');
        exit;
    }
}

function is_hashed_password($hash) {
    return password_get_info($hash)['algo'] !== 0;
}

function hash_admin_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_admin_password($password, $hash) {
    if (password_verify($password, $hash)) {
        return true;
    }

    // Fallback untuk password tersimpan dalam bentuk plain text
    return $password === $hash;
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}
