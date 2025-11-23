<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';
require_once '../../includes/GoogleAuthenticator.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../public/dashboard.php');
}

$code = trim($_POST['code']);
$secret = $_SESSION['temp_2fa_secret'] ?? null;

if (!$secret) {
    setErrorMessage('Session expired. Please try again.');
    redirect('../../public/setup_2fa.php');
}

$ga = new GoogleAuthenticator();
if (!$ga->verifyCode($secret, $code, 2)) {
    setErrorMessage('Invalid code. Please try again.');
    redirect('../../public/setup_2fa.php');
}

$users = Database::getUsers();
foreach ($users as &$user) {
    if ($user['id'] === getCurrentUserId()) {
        $user['two_factor_enabled'] = true;
        $user['two_factor_secret'] = $secret;
        break;
    }
}
Database::saveUsers($users);
unset($_SESSION['temp_2fa_secret']);

setSuccessMessage('Two-Factor Authentication enabled successfully!');
redirect('../../public/dashboard.php');
?>