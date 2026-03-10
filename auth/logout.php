<?php
require_once '../include/auth_guard.php';

// Clear all session data
session_unset();
session_destroy();

// Expire the theme cookie
setcookie('theme', '', time() - 3600, '/');
setcookie(session_name(), '', time() - 3600, '/');

header("Location: /supastrikasfc/pages/index.php");
exit();
?>