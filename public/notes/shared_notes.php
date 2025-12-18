<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/authorization.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

requireLogin();

$sharedNotes = Database::getSharedNotes(getCurrentUserId());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Notes - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .permission-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            background: #4CAF50;
            color: white;
            margin-left: 10px;
        }
        .permission-badge.viewer { background: #2196F3; }
        .permission-badge.editor { background: #ff9800; }
        .permission-badge.owner { background: #9c27b0; }
    </style>
</head>
<body>
<div class="navbar">
    <h1><?php echo APP_NAME; ?></h1>
    <div class="navbar-right">
        <span>Welcome, <?php echo htmlspecialchars(getCurrentUsername()); ?></span>
        <a href="../dashboard.php">My Notes</a>
        <a href="shared_notes.php">Shared With Me</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <div class="notes-header">
        <h2>Notes Shared With Me</h2>
    </div>

    <?php if (empty($sharedNotes)): ?>
        <div class="empty-state">
            <h3>No shared notes</h3>
            <p>When someone shares a note with you, it will appear here.</p>
        </div>
    <?php else: ?>
        <div class="notes-grid">
            <?php foreach ($sharedNotes as $note): ?>
                <?php
                $owner = Database::findUserById($note['user_id']);
                $myPermission = null;

                // Find user's permission level
                foreach ($note['shared_with'] as $share) {
                    if ($share['user_id'] === getCurrentUserId()) {
                        $myPermission = $share['permission'];
                        break;
                    }
                }
                ?>
                <div class="note-card">
                    <h3>
                        <?php echo htmlspecialchars($note['title']); ?>
                        <?php if ($myPermission): ?>
                            <span class="permission-badge <?php echo $myPermission; ?>">
                                    <?php echo strtoupper($myPermission); ?>
                                </span>
                        <?php endif; ?>
                    </h3>
                    <p><?php echo nl2br(htmlspecialchars(substr($note['content'], 0, 150))); ?><?php echo strlen($note['content']) > 150 ? '...' : ''; ?></p>
                    <small style="color: #999;">
                        Shared by: <?php echo $owner ? htmlspecialchars($owner['username']) : 'Unknown'; ?><br>
                        Created: <?php echo $note['created_at']; ?>
                    </small>
                    <div class="note-actions">
                        <?php if (Authorization::canAccessNote($note['id'], 'write')): ?>
                            <a href="edit_note.php?id=<?php echo $note['id']; ?>" class="btn btn-secondary">Edit</a>
                        <?php else: ?>
                            <a href="view_note.php?id=<?php echo $note['id']; ?>" class="btn btn-secondary">View</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
