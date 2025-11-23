<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$errorMessage = getErrorMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="auth-container">
    <h2>Register</h2>

    <?php if ($errorMessage): ?>
        <div class="error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <form method="POST" action="../handlers/register_handler.php">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required minlength="3">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="8"
                   pattern="^(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}$"
                   title="Password must be at least 8 characters and contain at least one uppercase letter, one number, and one symbol">
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
        </div>

        <button type="submit">Register</button>
    </form>

    <div class="link">
        Already have an account? <a href="index.php">Login here</a>
    </div>
</div>
</body>
</html>
