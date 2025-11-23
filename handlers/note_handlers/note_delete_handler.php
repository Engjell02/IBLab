<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

requireLogin();

$noteId = $_GET['id'] ?? null;
if (!$noteId) {
    redirect('../../public/dashboard.php');
}

// Verify ownership
$note = Database::findNoteById($noteId, getCurrentUserId());
if (!$note) {
    setErrorMessage('Note not found or access denied.');
    redirect('../../public/dashboard.php');
}

Database::deleteNote($noteId);

setSuccessMessage('Note deleted successfully!');
redirect('../../public/dashboard.php');
?>
