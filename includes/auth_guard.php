<?php
// Start session if not already started
//Also protects from doule start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Call this function on any page that requires login
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /supastrikas/auth/login.php");
        exit();
    }
}

// Call this on admin-only pages
function require_admin() {
    require_login();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: /supastrikas/pages/home.php");
        exit();
    }
}

// Generate CSRF token once per session
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token on form submissions
function validate_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die("Security validation failed. Please go back and try again.");
    }
}

// Safely output data to HTML (prevents XSS)
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
?>