<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';

requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Note - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="navbar">
    <h1>Create New Note</h1>
</div>

<div class="container">
    <div class="card">
        <form method="POST" action="../../handlers/note_handlers/note_create_handler.php">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" required></textarea>
            </div>

            <div class="button-group">
                <button type="submit">Create Note</button>
                <a href="../dashboard.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
