<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../public/index.php');
}

$username = trim($_POST['username']);
$password = $_POST['password'];

if (empty($username) || empty($password)) {
    setErrorMessage('Please fill in all fields.');
    redirect('../public/index.php');
}

$user = Database::findUserByUsername($username);

if (!$user) {
    setErrorMessage('User not found.');
    redirect('../public/index.php');
}

if (!password_verify($password, $user['password'])) {
    setErrorMessage('Invalid password.');
    redirect('../public/index.php');
}

if ($user['two_factor_enabled']) {
    $_SESSION['pending_2fa_user_id'] = $user['id'];
    $_SESSION['pending_2fa_username'] = $user['username'];
    $_SESSION['pending_2fa_is_admin'] = $user['is_admin'];
    redirect('../public/two_fa/verify_2fa.php');

} else {
    loginUser($user);
    redirect('../public/dashboard.php');
}
loginUser($user);
redirect('../public/dashboard.php');
?>
