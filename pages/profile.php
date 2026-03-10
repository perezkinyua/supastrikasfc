<?php
require_once '../include/auth_guard.php';
require_once '../config/db.php';
require_login();  // Make sure the user is logged in
include "../include/header.php";

session_start(); // Needed to get messages from update_profile.php

$user_id = $_SESSION['user_id'] ?? null;

// Initialize variables
$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? '';
unset($_SESSION['errors'], $_SESSION['success']); // clear session messages
$user = [];

// Fetch user data from the database
if ($user_id) {
    $stmt = $conn->prepare("SELECT username, full_name, email, favourite_player, bio, role, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();  // associative array or null

    if (!$user) {
        $errors[] = "User not found.";
    }
} else {
    $errors[] = "Invalid session. Please login again.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile - Juja Titans FC</title>
</head>
<body>
<h1>My Profile</h1>

<?php if (!empty($errors)): ?>
    <div class="errors">
        <?php foreach ($errors as $error): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if (!empty($user)): ?>
    <h2>Account Information</h2>
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
    
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    
    <p><strong>Member Since:</strong> <?= date("F Y", strtotime($user['created_at'])) ?></p>

    <h3>Edit Profile</h3>
    <form method="post" action="update_profile.php">
        <label>username:</label><br>
        <input type="text" name="username" value="<?= htmlspecialchars($user['full_name']) ?>"><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"><br><br>

        <button type="submit">Update Profile</button>
    </form>

    <form method="post" action="delete_account.php" onsubmit="return confirm('Are you sure?');">
        <button type="submit" name="delete">Delete Account</button>
    </form>
<?php endif; ?>
</body>
</html>