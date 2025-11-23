<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../public/register.php');
}

$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirmPassword = $_POST['confirm_password'];

// Validation
if (!validateUsername($username)) {
    setErrorMessage('Username must be at least 3 characters long.');
    redirect('../public/register.php');
}

if (!validateEmail($email)) {
    setErrorMessage('Invalid email address.');
    redirect('../public/register.php');
}

if (!validatePassword($password)) {
    setErrorMessage('Password must be at least 8 characters long and contain at least one uppercase letter, one number, and one symbol.');
    redirect('../public/register.php');
}

if ($password !== $confirmPassword) {
    setErrorMessage('Passwords do not match.');
    redirect('../public/register.php');
}

// Check if user already exists
if (Database::findUserByUsername($username)) {
    setErrorMessage('Username already exists.');
    redirect('../public/register.php');
}

if (Database::findUserByEmail($email)) {
    setErrorMessage('Email already registered.');
    redirect('../public/register.php');
}

// Create new user
Database::createUser($username, $email, $password);

setSuccessMessage('Registration successful! Please login.');
redirect('../public/index.php');
?>