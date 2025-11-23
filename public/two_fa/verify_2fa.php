<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

if (!isset($_SESSION['pending_2fa_user_id'])) {
    redirect('../index.php');
}

$errorMessage = getErrorMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Verification - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="auth-container">
    <h2>Two-Factor Authentication</h2>
    <p style="text-align: center; color: #666; margin-bottom: 20px;">
        Enter the 6-digit code from your authenticator app
    </p>

    <?php if ($errorMessage): ?>
        <div class="error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <form method="POST" action="../../handlers/two_fa_handlers/verify_2fa_handler.php">
        <div class="form-group">
            <label for="code">6-Digit Code</label>
            <input type="text" id="code" name="code" required
                   pattern="[0-9]{6}" maxlength="6"
                   placeholder="123456" autofocus
                   style="font-size: 32px; text-align: center; letter-spacing: 8px;">
        </div>

        <button type="submit">Verify</button>
    </form>

    <div class="link">
        <a href="../logout.php">Cancel & Logout</a>
    </div>
</div>
</body>
</html>