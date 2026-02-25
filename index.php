<?php
require_once 'includes/auth_guard.php';
require_once 'config/db.php';

// If already logged in send them to home, no need to see landing page
if (isset($_SESSION['user_id'])) {
    header("Location: pages/home.php");
    exit();
}
?>