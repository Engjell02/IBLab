<?php
function redirect($page) {
    header("Location: $page");
    exit();
}

function setSuccessMessage($message) {
    $_SESSION['success_message'] = $message;
}

function setErrorMessage($message) {
    $_SESSION['error_message'] = $message;
}

function getSuccessMessage() {
    if (isset($_SESSION['success_message'])) {
        $message = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
        return $message;
    }
    return null;
}

function getErrorMessage() {
    if (isset($_SESSION['error_message'])) {
        $message = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
        return $message;
    }
    return null;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($password) {
    return strlen($password) >= 8 &&
        preg_match('/[A-Z]/', $password) && // uppercase
        preg_match('/[0-9]/', $password) && // number
        preg_match('/[^A-Za-z0-9]/', $password); // symbol
}

function validateUsername($username) {
    return strlen($username) >= 3;
}
?>