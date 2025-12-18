<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/authorization.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

requireLogin();
requirePermission('access.grant_temporary');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../public/roles/grant_access.php');
}

$userId = $_POST['user_id'];
$role = $_POST['role'];
$duration = (int)$_POST['duration'];
$reason = trim($_POST['reason']);

// Validation
if (empty($userId) || empty($role) || empty($reason)) {
    setErrorMessage('All fields are required.');
    redirect('../../public/roles/grant_access.php');
}

if ($duration < 3600 || $duration > 604800) {
    setErrorMessage('Invalid duration selected.');
    redirect('../../public/roles/grant_access.php');
}

// Validate role exists
$rolesConfig = include '../../config/roles.php';
if (!isset($rolesConfig['roles'][$role])) {
    setErrorMessage('Invalid role selected.');
    redirect('../../public/roles/grant_access.php');
}

Authorization::grantTemporaryAccess($userId, $role, $duration, $reason);

$user = Database::findUserById($userId);
setSuccessMessage('Temporary access granted to ' . htmlspecialchars($user['username']) . ' for ' . ($duration / 3600) . ' hours.');

redirect('../../public/roles/manage_roles.php');
?>

