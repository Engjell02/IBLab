<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';
require_once '../../includes/GoogleAuthenticator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../public/index.php');
}

// Check pending 2FA session
if (!isset($_SESSION['pending_2fa_user_id'])) {
    redirect('../../public/index.php');
}

$code = trim($_POST['code']);
$userId = $_SESSION['pending_2fa_user_id'];


$user = Database::findUserById($userId);
if (!$user || !$user['two_factor_enabled']) {
    setErrorMessage('Invalid session.');
    redirect('../../public/index.php');
}


$ga = new GoogleAuthenticator();
if (!$ga->verifyCode($user['two_factor_secret'], $code, 2)) {
    setErrorMessage('Invalid code. Please try again.');
    redirect('../../public/verify_2fa.php');
}

$_SESSION['user_id'] = $_SESSION['pending_2fa_user_id'];
$_SESSION['username'] = $_SESSION['pending_2fa_username'];
$_SESSION['is_admin'] = $_SESSION['pending_2fa_is_admin'];

unset($_SESSION['pending_2fa_user_id']);
unset($_SESSION['pending_2fa_username']);
unset($_SESSION['pending_2fa_is_admin']);

redirect('../../public/dashboard.php');
?>