<?php
require_once '../include/auth_guard.php';
require_once '../config/db.php';
require_login();
session_start();

$user_id = $_SESSION['user_id'] ?? null;

// Initialize messages
$errors = [];
$success = '';

if (!$user_id) {
    $errors[] = "Invalid session. Please login again.";
}

// Only process form if POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Basic validation
    if (empty($username)) $errors[] = "Username cannot be empty.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";

    // If no errors, update database
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $email, $user_id);

        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
        } else {
            $errors[] = "Failed to update profile. Try again.";
        }
    }
}

// Save messages to session and redirect back
$_SESSION['errors'] = $errors;
$_SESSION['success'] = $success;
header("Location: profile.php");
exit();