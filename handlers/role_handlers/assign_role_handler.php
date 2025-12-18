<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/authorization.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

requireLogin();
requirePermission('roles.assign');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../public/roles/manage_roles.php');
}

$userId = $_POST['user_id'];
$role = $_POST['role'];

// Validate role exists
$rolesConfig = include '../../config/roles.php';
if (!isset($rolesConfig['roles'][$role])) {
    setErrorMessage('Invalid role selected.');
    redirect('../../public/roles/manage_roles.php');
}

// Prevent changing own role
if ($userId === getCurrentUserId()) {
    setErrorMessage('You cannot change your own role.');
    redirect('../../public/roles/manage_roles.php');
}

// Update user role
$success = Database::assignRole($userId, $role);

if ($success) {
    $user = Database::findUserById($userId);
    setSuccessMessage('Role updated successfully for ' . htmlspecialchars($user['username']) . '.');
} else {
    setErrorMessage('Failed to update role.');
}

redirect('../../public/roles/manage_roles.php');
?>

