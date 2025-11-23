<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../public/dashboard.php');
}

$title = trim($_POST['title']);
$content = trim($_POST['content']);

if (empty($title) || empty($content)) {
    setErrorMessage('Title and content are required.');
    redirect('../../public/create_note.php');
}

Database::createNote(getCurrentUserId(), $title, $content);

setSuccessMessage('Note created successfully!');
redirect('../../public/dashboard.php');
?>