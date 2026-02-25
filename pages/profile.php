<?php
require_once '../includes/auth_guard.php';
require_once '../config/db.php';
require_login();  // Redirect to login if not authenticated

$user_id = $_SESSION['user_id'];
$errors  = [];
$success = '';

// ============================================================
// DELETE ACCOUNT
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])
    && $_POST['action'] === 'delete_account') {

    validate_csrf_token($_POST['csrf_token'] ?? '');

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Log them out after deletion
    session_unset();
    session_destroy();
    setcookie('theme', '', time() - 3600, '/');
    header("Location: /supastrikas/index.php?msg=account_deleted");
    exit();
}

// ============================================================
// UPDATE PROFILE
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])
    && $_POST['action'] === 'update_profile') {

    validate_csrf_token($_POST['csrf_token'] ?? '');

    $full_name        = trim($_POST['full_name'] ?? '');
    $bio              = trim($_POST['bio'] ?? '');
    $favourite_player = trim($_POST['favourite_player'] ?? '');
    $theme            = in_array($_POST['theme'], ['light', 'dark'])
                        ? $_POST['theme'] : 'light';
    $new_email        = trim($_POST['email'] ?? '');

    // Validate
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (strlen($full_name) > 100) {
        $errors[] = "Full name is too long.";
    }

    // Check email not taken by someone else
    if (empty($errors)) {
        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE email = ? AND id != ?"
        );
        $stmt->bind_param("si", $new_email, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "That email is already used by another account.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "UPDATE users
             SET full_name=?, bio=?, favourite_player=?,
                 theme_preference=?, email=?
             WHERE id=?"
        );
        $stmt->bind_param(
            "sssssi",
            $full_name, $bio, $favourite_player, $theme, $new_email, $user_id
        );
        if ($stmt->execute()) {
            // Update session and cookie to reflect changes
            $_SESSION['email'] = $new_email;
            setcookie('theme', $theme, time() + (86400 * 30), '/');
            $success = "Profile updated successfully!";
        } else {
            $errors[] = "Update failed. Please try again.";
        }
        $stmt->close();
    }
}

// ============================================================
// READ — Fetch current user data (runs on every page load)
// ============================================================
$stmt = $conn->prepare(
    "SELECT username, email, full_name, bio, favourite_player,
            profile_photo, theme_preference, role, created_at
     FROM users WHERE id = ?"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();

$csrf = generate_csrf_token();
?>

<!-- 
WHAT TO TELL YOUR TEAMMATE WHO DESIGNS THIS PAGE:

They need two forms:

FORM 1 — Edit Profile:
action="" method="POST"
hidden: name="action" value="update_profile"
hidden: name="csrf_token" value="<?= $csrf ?>"
inputs: name="full_name" value="<?= h($user['full_name']) ?>"
        name="email" value="<?= h($user['email']) ?>"
        name="bio" (textarea) — <?= h($user['bio']) ?>
        name="favourite_player" value="<?= h($user['favourite_player']) ?>"
        name="theme" (select: light/dark) selected="<?= h($user['theme_preference']) ?>"

FORM 2 — Delete Account (separate form, needs confirm dialog in JS):
action="" method="POST"
hidden: name="action" value="delete_account"
hidden: name="csrf_token" value="<?= $csrf ?>"
A delete button — red, with onclick="return confirm('Are you sure?')"

Display (Read) section shows:
- <?= h($user['username']) ?>
- <?= h($user['full_name']) ?>
- <?= h($user['email']) ?>
- Member since: <?= date('F Y', strtotime($user['created_at'])) ?>
- Role badge: <?= h($user['role']) ?>
-->