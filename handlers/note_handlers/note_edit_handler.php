<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../public/dashboard.php');
}

$noteId = $_POST['note_id'];
$title = trim($_POST['title']);
$content = trim($_POST['content']);

if (empty($title) || empty($content)) {
    setErrorMessage('Title and content are required.');
    redirect('../../public/edit_note.php?id=' . $noteId);
}

// Verify ownership
$note = Database::findNoteById($noteId, getCurrentUserId());
if (!$note) {
    setErrorMessage('Note not found or access denied.');
    redirect('../../public/dashboard.php');
}

Database::updateNote($noteId, $title, $content);

setSuccessMessage('Note updated successfully!');
redirect('../../public/dashboard.php');
?>
