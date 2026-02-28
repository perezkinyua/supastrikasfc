<?php
require_once '../include/auth_guard.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Juja Titans FC</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="<?= $_COOKIE['theme'] ?? 'light' ?>"> <header>
    <div class="logo">JUJA TITANS FC</div>
    <nav>
        <a href="../pages/index.php">Home</a>
        <a href="../pages/squad.php">Squad</a>
        <a href="../pages/matches.php">Matches</a>
        <a href="../pages/profile.php">My Profile</a>

        <?php 
        // Member A uses 'user_id' to track logged-in status
        if(isset($_SESSION['user_id'])): 
        ?>
            <span>Welcome, <?= h($_SESSION['username']) ?></span> <a href="../auth/logout.php">Logout</a>
        <?php else: ?>
            <a href="../auth/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>