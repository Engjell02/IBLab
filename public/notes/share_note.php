<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/authorization.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

requireLogin();

$noteId = $_GET['id'] ?? null;
if (!$noteId) {
    redirect('../dashboard.php');
}

$note = Database::findNoteById($noteId);
if (!$note || $note['user_id'] !== getCurrentUserId()) {
    setErrorMessage('Note not found or access denied.');
    redirect('../dashboard.php');
}

$users = Database::getUsers();
$currentUserId = getCurrentUserId();

$rolesConfig = include '../../config/roles.php';
$resourcePermissions = $rolesConfig['resource_permissions'];

$successMessage = getSuccessMessage();
$errorMessage = getErrorMessage();

// Get current shares
$shares = $note['shared_with'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Note - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .share-item {
            background: #f5f5f5;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .share-item.expired {
            background: #fff5f5;
            border-left: 4px solid #f44336;
        }
        .permission-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            background: #4CAF50;
            color: white;
        }
        .permission-badge.viewer { background: #2196F3; }
        .permission-badge.editor { background: #ff9800; }
        .permission-badge.owner { background: #9c27b0; }
    </style>
</head>
<body>
<div class="navbar">
    <h1>Share Note</h1>
    <div class="navbar-right">
        <a href="../dashboard.php">Dashboard</a>
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

    <div class="card">
        <h2>Share: <?php echo htmlspecialchars($note['title']); ?></h2>

        <h3>Add New Share</h3>
        <form method="POST" action="../../handlers/note_handlers/share_note_handler.php">
            <input type="hidden" name="note_id" value="<?php echo htmlspecialchars($noteId); ?>">

            <div class="form-group">
                <label for="user_id">Share With User</label>
                <select id="user_id" name="user_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">-- Select User --</option>
                    <?php foreach ($users as $user): ?>
                        <?php if ($user['id'] !== $currentUserId): ?>
                            <option value="<?php echo $user['id']; ?>">
                                <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="permission">Permission Level</label>
                <select id="permission" name="permission" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">-- Select Permission --</option>
                    <option value="viewer">Viewer (Read Only)</option>
                    <option value="editor">Editor (Read & Write)</option>
                    <option value="owner">Owner (Full Access)</option>
                </select>
                <small style="color: #666; display: block; margin-top: 5px;">
                    • Viewer: Can only read the note<br>
                    • Editor: Can read and modify the note<br>
                    • Owner: Can read, modify, delete, and share the note
                </small>
            </div>

            <div class="form-group">
                <label for="expires">Access Duration (Optional)</label>
                <select id="expires" name="expires" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">Permanent Access</option>
                    <option value="3600">1 Hour</option>
                    <option value="7200">2 Hours</option>
                    <option value="14400">4 Hours</option>
                    <option value="28800">8 Hours</option>
                    <option value="86400">1 Day</option>
                    <option value="259200">3 Days</option>
                    <option value="604800">1 Week</option>
                    <option value="2592000">30 Days</option>
                </select>
            </div>

            <div class="button-group">
                <button type="submit">Share Note</button>
                <a href="../dashboard.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>

    <?php if (!empty($shares)): ?>
        <div class="card">
            <h3>Current Shares</h3>
            <?php foreach ($shares as $index => $share): ?>
                <?php
                $sharedUser = Database::findUserById($share['user_id']);
                $expired = $share['expires_at'] && strtotime($share['expires_at']) < time();
                ?>
                <div class="share-item <?php echo $expired ? 'expired' : ''; ?>">
                    <div>
                        <strong><?php echo $sharedUser ? htmlspecialchars($sharedUser['username']) : 'Unknown User'; ?></strong>
                        <span class="permission-badge <?php echo $share['permission']; ?>">
                                <?php echo strtoupper($share['permission']); ?>
                            </span>
                        <br>
                        <small style="color: #666;">
                            Granted: <?php echo $share['granted_at']; ?>
                            <?php if ($share['expires_at']): ?>
                                | Expires: <?php echo $share['expires_at']; ?>
                                <?php if ($expired): ?>
                                    <span style="color: #f44336; font-weight: bold;">(EXPIRED)</span>
                                <?php endif; ?>
                            <?php else: ?>
                                | Never Expires
                            <?php endif; ?>
                        </small>
                    </div>
                    <div>
                        <form method="POST" action="../../handlers/note_handlers/unshare_note_handler.php" style="display: inline;">
                            <input type="hidden" name="note_id" value="<?php echo htmlspecialchars($noteId); ?>">
                            <input type="hidden" name="share_index" value="<?php echo $index; ?>">
                            <button type="submit" class="btn btn-danger" style="padding: 8px 15px;" onclick="return confirm('Remove this share?');">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
