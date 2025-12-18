<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/authorization.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

requireLogin();
requirePermission('access.grant_temporary');

$users = Database::getUsers();
$rolesConfig = include '../../config/roles.php';
$availableRoles = $rolesConfig['roles'];

$errorMessage = getErrorMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grant Temporary Access - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="navbar">
    <h1>Grant Temporary Access (JIT)</h1>
    <div class="navbar-right">
        <a href="../dashboard.php">Dashboard</a>
        <a href="manage_roles.php">Manage Roles</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <h2>Grant Just-In-Time Access</h2>
        <p style="color: #666; margin-bottom: 20px;">
            Grant temporary elevated privileges to a user for a specific time period.
        </p>

        <?php if ($errorMessage): ?>
            <div class="error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <form method="POST" action="../../handlers/role_handlers/grant_access_handler.php">
            <div class="form-group">
                <label for="user_id">Select User</label>
                <select id="user_id" name="user_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">-- Select User --</option>
                    <?php foreach ($users as $user): ?>
                        <?php if ($user['id'] !== getCurrentUserId()): ?>
                            <option value="<?php echo $user['id']; ?>">
                                <?php echo htmlspecialchars($user['username']); ?>
                                (<?php echo htmlspecialchars($user['email']); ?>)
                                - Current: <?php echo htmlspecialchars($availableRoles[$user['role'] ?? 'user']['name']); ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="role">Temporary Role</label>
                <select id="role" name="role" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">-- Select Role --</option>
                    <?php foreach ($availableRoles as $roleKey => $roleData): ?>
                        <option value="<?php echo $roleKey; ?>">
                            <?php echo htmlspecialchars($roleData['name']); ?> (Level: <?php echo $roleData['level']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="duration">Duration</label>
                <select id="duration" name="duration" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="3600">1 Hour</option>
                    <option value="7200">2 Hours</option>
                    <option value="14400">4 Hours</option>
                    <option value="28800">8 Hours</option>
                    <option value="86400">24 Hours</option>
                    <option value="172800">48 Hours</option>
                    <option value="604800">1 Week</option>
                </select>
            </div>

            <div class="form-group">
                <label for="reason">Reason for Access</label>
                <textarea id="reason" name="reason" required rows="4"
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: Arial, sans-serif;"
                          placeholder="Explain why this user needs temporary access..."></textarea>
            </div>

            <div class="button-group">
                <button type="submit">Grant Access</button>
                <a href="manage_roles.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>