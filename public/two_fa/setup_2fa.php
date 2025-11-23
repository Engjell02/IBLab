<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';
require_once '../../includes/GoogleAuthenticator.php';

requireLogin();

$ga = new GoogleAuthenticator();
$user = Database::findUserById(getCurrentUserId());

if ($user['two_factor_enabled']) {
    setSuccessMessage('2FA is already enabled!');
    redirect('../dashboard.php');
}

$secret = $ga->createSecret();
$_SESSION['temp_2fa_secret'] = $secret;

$qrCodeUrl = $ga->getQRCodeGoogleUrl(
    getCurrentUsername() . '@NotesApp',
    $secret,
    'Notes App'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup 2FA - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="navbar">
    <h1>Setup Two-Factor Authentication</h1>
</div>

<div class="container">
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <h2>Enable 2FA</h2>

        <div style="margin: 20px 0;">
            <h3>Step 1: Install Authenticator App</h3>
            <p>Download one of these apps on your phone:</p>
            <ul>
                <li>Google Authenticator (iOS/Android)</li>
                <li>Authy (iOS/Android)</li>
                <li>Microsoft Authenticator (iOS/Android)</li>
            </ul>
        </div>

        <div style="margin: 20px 0;">
            <h3>Step 2: Scan QR Code</h3>
            <p>Open your authenticator app and scan this QR code:</p>
            <div style="text-align: center; margin: 20px 0;">
                <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code">
            </div>

            <p><strong>Or enter this code manually:</strong></p>
            <div style="background: #f5f5f5; padding: 15px; border-radius: 4px; text-align: center; font-family: monospace; font-size: 18px; letter-spacing: 2px;">
                <?php echo $secret; ?>
            </div>
        </div>

        <div style="margin: 20px 0;">
            <h3>Step 3: Verify Code</h3>
            <p>Enter the 6-digit code from your authenticator app:</p>

            <form method="POST" action="../../handlers/two_fa_handlers/setup_2fa_handler.php">
                <div class="form-group">
                    <label for="code">6-Digit Code</label>
                    <input type="text" id="code" name="code" required
                           pattern="[0-9]{6}" maxlength="6"
                           placeholder="123456"
                           style="font-size: 24px; text-align: center; letter-spacing: 5px;">
                </div>

                <div class="button-group">
                    <button type="submit">Enable 2FA</button>
                    <a href="../dashboard.php" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>