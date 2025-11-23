<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('index.php');
    }
}

function requireAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        redirect('dashboard.php');
    }
}

function loginUser($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['is_admin'] = $user['is_admin'];
}

function logoutUser() {
    session_destroy();
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}
?>