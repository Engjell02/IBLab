<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../public/dashboard.php');
}

$userId = $_POST['user_id'];

if ($userId === getCurrentUserId()) {
    setErrorMessage('You cannot delete your own account.');
    redirect('../public/admin.php');
}

// Delete user and their notes
Database::deleteUser($userId);
Database::deleteUserNotes($userId);

setSuccessMessage('User and their notes deleted successfully!');
redirect('../public/admin.php');
?>