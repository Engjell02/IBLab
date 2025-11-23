<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../public/dashboard.php');
}

$noteId = $_POST['note_id'];

Database::deleteNote($noteId);

setSuccessMessage('Note deleted successfully!');
redirect('../public/admin.php');
?>