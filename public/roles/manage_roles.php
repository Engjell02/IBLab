<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/authorization.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

requireLogin();
requirePermission('roles.assign');

// Clean expired temporary access
Authorization::cleanExpiredAccess();

$users = Database::getUsers();
$rolesConfig = include '../../config/roles.php';
$availableRoles = $rolesConfig['roles'];

$successMessage = getSuccessMessage();
$errorMessage = getErrorMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Roles - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .role-super_admin { background: #9c27b0; color: white; }
        .role-admin { background: #f44336; color: white; }
        .role-manager { background: #ff9800; color: white; }
        .role-user { background: #4CAF50; color: white; }
        .role-guest { background: #9e9e9e; color: white; }

        .temp-access {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .expired {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }

        select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="navbar">
    <h1>Manage User Roles</h1>
    <div class="navbar-right">
        <a href="../dashboard.php">Dashboard</a>
        <a href="grant_access.php">Grant Temporary Access</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <?php if ($successMessage): ?>
        <div class="success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <div class="section">
        <h2>User Roles & Permissions</h2>

        <table>
            <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Current Role</th>
                <th>Department</th>
                <th>Manager</th>
                <th>Temporary Access</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <?php
                $role = $user['role'] ?? 'user';
                $hasTemp = isset($user['temporary_roles']) && !empty($user['temporary_roles']);

                // Find manager name
                $managerName = 'None';
                if (isset($user['manager_id']) && $user['manager_id']) {
                    $manager = Database::findUserById($user['manager_id']);
                    if ($manager) {
                        $managerName = $manager['username'];
                    }
                }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                                <span class="role-badge role-<?php echo $role; ?>">
                                    <?php echo htmlspecialchars($availableRoles[$role]['name']); ?>
                                </span>
                    </td>
                    <td><?php echo htmlspecialchars($user['department'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($managerName); ?></td>
                    <td>
                        <?php if ($hasTemp): ?>
                            <?php foreach ($user['temporary_roles'] as $tempRole): ?>
                                <?php
                                $expired = strtotime($tempRole['expires_at']) < time();
                                $expiresIn = $expired ? 'Expired' : 'Expires: ' . $tempRole['expires_at'];
                                ?>
                                <div class="temp-access <?php echo $expired ? 'expired' : ''; ?>">
                                    <strong><?php echo htmlspecialchars($availableRoles[$tempRole['role']]['name']); ?></strong><br>
                                    <small><?php echo $expiresIn; ?></small><br>
                                    <small>Reason: <?php echo htmlspecialchars($tempRole['reason']); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span style="color: #999;">None</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($user['id'] !== getCurrentUserId()): ?>
                            <form method="POST" action="../../handlers/role_handlers/assign_role_handler.php" style="display: inline-block;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <select name="role" required>
                                    <?php foreach ($availableRoles as $roleKey => $roleData): ?>
                                        <option value="<?php echo $roleKey; ?>" <?php echo $role === $roleKey ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($roleData['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-secondary" style="padding: 8px 15px;">Update</button>
                            </form>
                        <?php else: ?>
                            <span style="color: #999;">You</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Role Hierarchy & Permissions</h2>

        <?php foreach ($availableRoles as $roleKey => $roleData): ?>
            <div style="background: white; padding: 15px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #4CAF50;">
                <h3>
                        <span class="role-badge role-<?php echo $roleKey; ?>">
                            <?php echo htmlspecialchars($roleData['name']); ?>
                        </span>
                    <small style="color: #999; margin-left: 10px;">Level: <?php echo $roleData['level']; ?></small>
                </h3>
                <p><strong>Permissions:</strong></p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <?php if (in_array('*', $roleData['permissions'])): ?>
                        <li><strong>All Permissions</strong></li>
                    <?php else: ?>
                        <?php foreach ($roleData['permissions'] as $perm): ?>
                            <li><code><?php echo htmlspecialchars($perm); ?></code></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
