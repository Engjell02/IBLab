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
$shareIndex = (int)$_POST['share_index'];

// Verify note ownership
$note = Database::findNoteById($noteId);
if (!$note || $note['user_id'] !== getCurrentUserId()) {
    setErrorMessage('Note not found or access denied.');
    redirect('../../public/dashboard.php');
}

// Remove share
$notes = Database::getNotes();
foreach ($notes as &$n) {
    if ($n['id'] === $noteId) {
        if (isset($n['shared_with'][$shareIndex])) {
            array_splice($n['shared_with'], $shareIndex, 1);
            Database::saveNotes($notes);
            setSuccessMessage('Share removed successfully!');
        } else {
            setErrorMessage('Share not found.');
        }
        break;
    }
}

redirect('../../public/notes/share_note.php?id=' . $noteId);
?>

