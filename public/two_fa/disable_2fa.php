<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

requireLogin();

$user = Database::findUserById(getCurrentUserId());
if (!$user['two_factor_enabled']) {
    setErrorMessage('2FA is not enabled!');
    redirect('../dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Disable 2FA
    $users = Database::getUsers();
    foreach ($users as &$u) {
        if ($u['id'] === getCurrentUserId()) {
            $u['two_factor_enabled'] = false;
            $u['two_factor_secret'] = null;
            break;
        }
    }
    Database::saveUsers($users);

    setSuccessMessage('Two-Factor Authentication disabled.');
    redirect('../dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disable 2FA - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="auth-container">
    <h2>Disable Two-Factor Authentication</h2>
    <p style="text-align: center; color: #666; margin-bottom: 20px;">
        Are you sure you want to disable 2FA? This will make your account less secure.
    </p>

    <form method="POST">
        <div class="button-group">
            <button type="submit" class="btn btn-danger">Yes, Disable 2FA</button>
            <a href="../dashboard.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>