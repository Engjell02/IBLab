<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/authorization.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../public/dashboard.php');
}

$noteId = $_POST['note_id'];
$userId = $_POST['user_id'];
$permission = $_POST['permission'];
$expires = $_POST['expires'] ?? null;

// Validation
if (empty($noteId) || empty($userId) || empty($permission)) {
    setErrorMessage('All required fields must be filled.');
    redirect('../../public/notes/share_note.php?id=' . $noteId);
}

// Verify note ownership
$note = Database::findNoteById($noteId);
if (!$note || $note['user_id'] !== getCurrentUserId()) {
    setErrorMessage('Note not found or access denied.');
    redirect('../../public/dashboard.php');
}

// Calculate expiration time
$expiresAt = null;
if ($expires) {
    $expiresAt = date('Y-m-d H:i:s', time() + (int)$expires);
}

// Share the note
$success = Database::shareNote($noteId, $userId, $permission, $expiresAt);

if ($success) {
    $sharedUser = Database::findUserById($userId);
    setSuccessMessage('Note shared with ' . htmlspecialchars($sharedUser['username']) . ' successfully!');
} else {
    setErrorMessage('Failed to share note.');
}

redirect('../../public/notes/share_note.php?id=' . $noteId);
?>

