<?php
require_once '../includes/auth_guard.php';
require_once '../config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../pages/home.php");
    exit();
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    validate_csrf_token($_POST['csrf_token'] ?? '');

    // Sanitize inputs
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Server-side validation
    if (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = "Username must be between 3 and 50 characters.";
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Username can only contain letters, numbers, and underscores.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }
    if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter and one number.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    // Check for duplicates
    if (empty($errors)) {
        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE email = ? OR username = ?"
        );
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "That username or email is already registered.";
        }
        $stmt->close();
    }

    // Insert new user
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare(
            "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $username, $email, $hash);
        if ($stmt->execute()) {
            $success = "Account created! You can now log in.";
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }
}

$csrf = generate_csrf_token();
?>

<!-- Your teammate adds their HTML form design below here -->
<!-- The form must have: -->
<!-- action="" method="POST" -->
<!-- inputs with name="username", name="email", -->
<!-- name="password", name="confirm_password" -->
<!-- A hidden input: <input type="hidden" name="csrf_token" value="<?= $csrf ?>"> -->
<!-- A submit button -->