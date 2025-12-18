<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/authorization.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

requireLogin();

$user = Database::findUserById(getCurrentUserId());
$rolesConfig = include '../../config/roles.php';
$availableRoles = $rolesConfig['roles'];

$currentRole = $user['role'] ?? 'user';
$tempRoles = $user['temporary_roles'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Access - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .access-card {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 4px solid #4CAF50;
        }
        .access-card.expired {
            border-left-color: #f44336;
            background: #fff5f5;
        }
        .access-card h3 {
            margin-bottom: 10px;
            color: #333;
        }
        .permission-list {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .permission-list code {
            display: inline-block;
            background: white;
            padding: 4px 8px;
            margin: 4px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
<div class="navbar">
    <h1>My Access & Permissions</h1>
    <div class="navbar-right">
        <a href="../dashboard.php">Dashboard</a>
        <a href="request_access.php">Request Access</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <div class="section">
        <h2>Current Role</h2>
        <div class="access-card">
            <h3><?php echo htmlspecialchars($availableRoles[$currentRole]['name']); ?></h3>
            <p><strong>Level:</strong> <?php echo $availableRoles[$currentRole]['level']; ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($user['department'] ?? 'N/A'); ?></p>

            <div class="permission-list">
                <strong>Permissions:</strong><br>
                <?php if (in_array('*', $availableRoles[$currentRole]['permissions'])): ?>
                    <code>All Permissions</code>
                <?php else: ?>
                    <?php foreach ($availableRoles[$currentRole]['permissions'] as $perm): ?>
                        <code><?php echo htmlspecialchars($perm); ?></code>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!empty($tempRoles)): ?>
        <div class="section">
            <h2>Temporary Access (JIT)</h2>
            <?php foreach ($tempRoles as $tempRole): ?>
                <?php
                $expired = strtotime($tempRole['expires_at']) < time();
                $grantedBy = Database::findUserById($tempRole['granted_by']);
                ?>
                <div class="access-card <?php echo $expired ? 'expired' : ''; ?>">
                    <h3>
                        <?php echo htmlspecialchars($availableRoles[$tempRole['role']]['name']); ?>
                        <?php if ($expired): ?>
                            <span style="color: #f44336; font-size: 14px;">(EXPIRED)</span>
                        <?php else: ?>
                            <span style="color: #4CAF50; font-size: 14px;">(ACTIVE)</span>
                        <?php endif; ?>
                    </h3>
                    <p><strong>Granted:</strong> <?php echo $tempRole['granted_at']; ?></p>
                    <p><strong>Expires:</strong> <?php echo $tempRole['expires_at']; ?></p>
                    <p><strong>Granted by:</strong> <?php echo $grantedBy ? htmlspecialchars($grantedBy['username']) : 'Unknown'; ?></p>
                    <p><strong>Reason:</strong> <?php echo htmlspecialchars($tempRole['reason']); ?></p>

                    <?php if (!$expired): ?>
                        <div class="permission-list">
                            <strong>Additional Permissions:</strong><br>
                            <?php if (in_array('*', $availableRoles[$tempRole['role']]['permissions'])): ?>
                                <code>All Permissions</code>
                            <?php else: ?>
                                <?php foreach ($availableRoles[$tempRole['role']]['permissions'] as $perm): ?>
                                    <code><?php echo htmlspecialchars($perm); ?></code>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="section">
            <h2>Temporary Access</h2>
            <p style="color: #999; text-align: center; padding: 40px;">
                You have no temporary access grants.
            </p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>


