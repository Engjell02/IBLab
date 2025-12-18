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

// Check if user can read this note
if (!Authorization::canAccessNote($noteId, 'read')) {
    setErrorMessage('You do not have permission to view this note.');
    redirect('../dashboard.php');
}

$note = Database::findNoteById($noteId);
$owner = Database::findUserById($note['user_id']);
$canEdit = Authorization::canAccessNote($noteId, 'write');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($note['title']); ?> - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="navbar">
    <h1>View Note</h1>
    <div class="navbar-right">
        <a href="../dashboard.php">Dashboard</a>
        <a href="shared_notes.php">Shared Notes</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <div class="card">
        <h2><?php echo htmlspecialchars($note['title']); ?></h2>
        <small style="color: #999;">
            Owner: <?php echo $owner ? htmlspecialchars($owner['username']) : 'Unknown'; ?><br>
            Created: <?php echo $note['created_at']; ?><br>
            Last Updated: <?php echo $note['updated_at']; ?>
        </small>

        <div style="margin: 20px 0; padding: 20px; background: #f5f5f5; border-radius: 4px; white-space: pre-wrap;">
            <?php echo htmlspecialchars($note['content']); ?>
        </div>

        <div class="button-group">
            <?php if ($canEdit): ?>
                <a href="edit_note.php?id=<?php echo $note['id']; ?>" class="btn btn-primary">Edit</a>
            <?php endif; ?>
            <a href="../dashboard.php" class="btn btn-cancel">Back</a>
        </div>
    </div>
</div>
</body>
</html>
