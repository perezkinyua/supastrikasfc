<?php
require_once '../include/auth_guard.php';
require_once '../config/db.php';
include("../include/header.php");

if (isset($_SESSION['user_id'])) {
    header("Location: ../pages/home.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf_token($_POST['csrf_token'] ?? '');

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = "Both fields are required.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id, username, email, password_hash, role, theme_preference
             FROM users WHERE email = ?"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Prevent session fixation
            session_regenerate_id(true);

            // Store user info in session
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['email']     = $user['email'];
            $_SESSION['role']      = $user['role'];

            // Sync theme cookie with database preference
            setcookie(
                'theme',
                $user['theme_preference'],
                time() + (86400 * 30),
                '/'
            );

            header("Location: ../pages/home.php");
            exit();
        } else {
            // Vague error on purpose
            $errors[] = "Invalid email or password.";
        }
    }
}

$csrf = generate_csrf_token();
?>
<!-- Teammate adds form HTML below -->
<!-- Inputs needed: name="email", name="password" -->
<!-- Hidden: <input type="hidden" name="csrf_token" value="<?= $csrf ?>"> -->


<section class="login-form">
    <h2>Member Login</h2>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="text" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </form>
</section>

<?php include("../include/footer.php"); ?>