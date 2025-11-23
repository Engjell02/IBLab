<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

requireLogin();

$noteId = $_GET['id'] ?? null;
if (!$noteId) {
    redirect('../dashboard.php');
}

$note = Database::findNoteById($noteId, getCurrentUserId());
if (!$note) {
    setErrorMessage('Note not found or access denied.');
    redirect('../dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="navbar">
    <h1>Edit Note</h1>
</div>

<div class="container">
    <div class="card">
        <form method="POST" action="../../handlers/note_handlers/note_edit_handler.php">
            <input type="hidden" name="note_id" value="<?php echo htmlspecialchars($note['id']); ?>">

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($note['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" required><?php echo htmlspecialchars($note['content']); ?></textarea>
            </div>

            <div class="button-group">
                <button type="submit">Update Note</button>
                <a href="../dashboard.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
