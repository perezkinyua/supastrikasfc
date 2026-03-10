<?php
require_once '../include/auth_guard.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supa Strikas FC</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="<?= $_COOKIE['theme'] ?? 'light' ?>"> <header>
    <div class="logo">Supa Strikas FC</div>
    <nav>
        <a href="../pages/index.php">Home</a>
        <a href="../pages/squad.php">Squad</a>
        <a href="../pages/matches.php">Matches</a>
        <a href="../pages/profile.php">My Profile</a>
        <a href="../auth/logout.php">Logout</a>

        <?php 
        // Member A uses 'user_id' to track logged-in status
        if(isset($_SESSION['user_id'])): 
        ?>
        <?php else: ?>
            <a href="../auth/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>