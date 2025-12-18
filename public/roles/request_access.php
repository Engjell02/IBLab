<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/authorization.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

requireLogin();

$rolesConfig = include '../../config/roles.php';
$availableRoles = $rolesConfig['roles'];

$successMessage = getSuccessMessage();
$errorMessage = getErrorMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0;">
    <title>Request Access - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="navbar">
    <h1>Request Temporary Access</h1>
    <div class="navbar-right">
        <a href="../dashboard.php">Dashboard</a>
        <a href="my_access.php">My Access</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <h2>Request Elevated Access</h2>
        <p style="color: #666; margin-bottom: 20px;">
            Request temporary elevated privileges. An administrator will review your request.
        </p>

        <?php if ($successMessage): ?>
            <div class="success"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <form method="POST" action="../../handlers/role_handlers/request_access_handler.php">
            <div class="form-group">
                <label for="role">Requested Role</label>
                <select id="role" name="role" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">-- Select Role --</option>
                    <?php foreach ($availableRoles as $roleKey => $roleData): ?>
                        <?php if ($roleKey !== 'super_admin'): ?>
                            <option value="<?php echo $roleKey; ?>">
                                <?php echo htmlspecialchars($roleData['name']); ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="duration">Requested Duration</label>
                <select id="duration" name="duration" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="3600">1 Hour</option>
                    <option value="7200">2 Hours</option>
                    <option value="14400">4 Hours</option>
                    <option value="28800">8 Hours</option>
                    <option value="86400">24 Hours</option>
                </select>
            </div>

            <div class="form-group">
                <label for="reason">Reason for Request</label>
                <textarea id="reason" name="reason" required rows="5"
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: Arial, sans-serif;"
                          placeholder="Explain in detail why you need this access and what you plan to do with it..."></textarea>
            </div>

            <div class="button-group">
                <button type="submit">Submit Request</button>
                <a href="../dashboard.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>

