<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/authorization.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

requireLogin();

$userNotes = Database::getUserNotes(getCurrentUserId());
$successMessage = getSuccessMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="navbar">
    <h1><?php echo APP_NAME; ?></h1>
    <div class="navbar-right">
        <span>Welcome, <?php echo htmlspecialchars(getCurrentUsername()); ?></span>
        <a href="notes/shared_notes.php">Shared With Me</a>
        <?php if (can('team.view')): ?>
            <a href="manager_dashboard.php">Team Dashboard</a>
        <?php endif; ?>
        <?php
        if (can('roles.assign')): ?>
            <a href="roles/manage_roles.php">Manage Roles</a>
        <?php endif; ?>
        <a href="roles/my_access.php">My Access</a>
        <?php
        $currentUser = Database::findUserById(getCurrentUserId());
        if ($currentUser['two_factor_enabled']): ?>
            <a href="two_fa/disable_2fa.php">Disable 2FA</a>
        <?php else: ?>
            <a href="two_fa/setup_2fa.php">Enable 2FA</a>
        <?php endif; ?>

        <?php if (isAdmin()): ?>
            <a href="admin.php">Admin Panel</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <?php if ($successMessage): ?>
        <div class="success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <div class="notes-header">
        <h2>My Notes</h2>
        <a href="notes/create_note.php" class="btn btn-primary">+ Create New Note</a>
    </div>

    <?php if (empty($userNotes)): ?>
        <div class="empty-state">
            <h3>No notes yet</h3>
            <p>Create your first note to get started!</p>
        </div>
    <?php else: ?>
        <div class="notes-grid">
            <?php foreach ($userNotes as $note): ?>
                <div class="note-card">
                    <h3><?php echo htmlspecialchars($note['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars(substr($note['content'], 0, 150))); ?><?php echo strlen($note['content']) > 150 ? '...' : ''; ?></p>
                    <small style="color: #999;">Created: <?php echo $note['created_at']; ?></small>
                    <div class="note-actions">
                        <a href="notes/edit_note.php?id=<?php echo $note['id']; ?>" class="btn btn-secondary">Edit</a>
                        <a href="notes/share_note.php?id=<?php echo $note['id']; ?>" class="btn btn-primary" style="background: #2196F3;">Share</a>
                        <a href="notes/delete_note.php?id=<?php echo $note['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>