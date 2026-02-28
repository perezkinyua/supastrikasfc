<?php
require_once '../include/auth_guard.php';
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

include("../include/header.php");
?>

<!-- Your teammate adds their HTML form design below here -->
<!-- The form must have: -->
<!-- action="" method="POST" -->
<!-- inputs with name="username", name="email", -->
<!-- name="password", name="confirm_password" -->
<!-- A hidden input: <input type="hidden" name="csrf_token" value="<?= $csrf ?>"> -->
<!-- A submit button -->

<section class="login-form">
    <h2>Member Login</h2>

    <?php if (!empty($errors)): ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 10px;">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= h($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

    <form method="POST" action="register.php">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Your Password" required>
        <button type="submit">Login</button>
    </form>

    <?php if ($success): ?>
    <div style="color: green; border: 1px solid green; padding: 10px;">
        <?= h($success) ?> <a href="login.php">Log in here</a>
    </div>
<?php endif; ?>

</section>

<?php include("../include/footer.php"); ?>