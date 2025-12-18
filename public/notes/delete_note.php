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

$note = Database::findNoteById($noteId, getCurrentUserId());
if ($note && ($note['user_id'] === getCurrentUserId() || can('notes.delete_any'))) {
    Database::deleteNote($noteId);
    setSuccessMessage('Note deleted successfully!');
} else {
    setErrorMessage('You do not have permission to delete this note.');
}

redirect('../dashboard.php');
?>