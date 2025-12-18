<?php
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../public/roles/request_access.php');
}

$role = $_POST['role'];
$duration = (int)$_POST['duration'];
$reason = trim($_POST['reason']);

// Validation
if (empty($role) || empty($reason)) {
    setErrorMessage('All fields are required.');
    redirect('../../public/roles/request_access.php');
}

// In a real system, this would create a request in a database
// For this lab, we'll just show a success message
// Admins would review requests and manually grant access

setSuccessMessage('Your access request has been submitted. An administrator will review it shortly.');
redirect('../../public/roles/my_access.php');
?>