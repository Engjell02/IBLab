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
if ($note) {
    Database::deleteNote($noteId);
    setSuccessMessage('Note deleted successfully!');
} else {
    setErrorMessage('Note not found or access denied.');
}

redirect('../dashboard.php');
?>