<?php
require_once '../include/auth_guard.php';
require_once '../config/db.php';
require_login();

$errors  = [];
$success = '';
$is_admin = ($_SESSION['role'] === 'admin');

// ============================================================
// CREATE — Admin adds a new match
// ============================================================
if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'create_match') {

    validate_csrf_token($_POST['csrf_token'] ?? '');

    $opponent   = trim($_POST['opponent'] ?? '');
    $match_date = $_POST['match_date'] ?? '';
    $venue      = trim($_POST['venue'] ?? '');
    $status     = in_array($_POST['status'], ['upcoming','completed','cancelled'])
                  ? $_POST['status'] : 'upcoming';

    if (empty($opponent)) { $errors[] = "Opponent name is required."; }
    if (empty($match_date) || !strtotime($match_date)) {
        $errors[] = "A valid match date is required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "INSERT INTO matches (opponent, match_date, venue, status)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $opponent, $match_date, $venue, $status);
        if ($stmt->execute()) {
            $success = "Match added successfully.";
        }
        $stmt->close();
    }
}

// ============================================================
// UPDATE — Admin updates a match result
// ============================================================
if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'update_match') {

    validate_csrf_token($_POST['csrf_token'] ?? '');

    $id         = (int)$_POST['match_id'];
    $opponent   = trim($_POST['opponent'] ?? '');
    $match_date = $_POST['match_date'] ?? '';
    $venue      = trim($_POST['venue'] ?? '');
    $score_home = (int)$_POST['score_home'];
    $score_away = (int)$_POST['score_away'];
    $status     = in_array($_POST['status'], ['upcoming','completed','cancelled'])
                  ? $_POST['status'] : 'upcoming';

   if (empty($opponent)) {
    $errors[] = "Opponent name is required.";
    }
    if (empty($match_date) || !strtotime($match_date)) {
    $errors[] = "A valid match date is required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "UPDATE matches
             SET opponent=?, match_date=?, venue=?,
                 score_home=?, score_away=?, status=?
             WHERE id=?"
        );
        $stmt->bind_param(
            "sssiisi",
            $opponent, $match_date, $venue,
            $score_home, $score_away, $status, $id
        );
        if ($stmt->execute()) {
            $success = "Match updated.";
        }
        $stmt->close();
    }
}

// ============================================================
// DELETE — Admin removes a match
// ============================================================
if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'delete_match') {

    validate_csrf_token($_POST['csrf_token'] ?? '');

    $id   = (int)$_POST['match_id'];
    $stmt = $conn->prepare("DELETE FROM matches WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $success = "Match deleted.";
}

// ============================================================
// READ — Fetch all matches (everyone sees this)
// ============================================================
$upcoming = $conn->query(
    "SELECT * FROM matches WHERE status='upcoming'
     ORDER BY match_date ASC"
);
$results = $conn->query(
    "SELECT * FROM matches WHERE status='completed'
     ORDER BY match_date DESC LIMIT 10"
);

$csrf = generate_csrf_token();
?>

<!--
WHAT TO TELL YOUR TEAMMATE:

PUBLIC SECTION (all logged-in users):
- Loop $upcoming with while($row = $upcoming->fetch_assoc())
  Show: h($row['opponent']), h($row['match_date']), h($row['venue'])
  
- Loop $results similarly, showing scores:
  h($row['score_home']) . ' - ' . h($row['score_away'])

ADMIN SECTION (wrap in: if ($is_admin): )
- Add Match Form:
  hidden: name="action" value="create_match"
  hidden: name="csrf_token" value="<?= $csrf ?>"
  inputs: name="opponent", name="match_date" (type="date"),
          name="venue", name="status" (select)

- Each match row gets Edit & Delete buttons
  Edit triggers a pre-filled modal or inline form with:
  hidden: name="action" value="update_match"
  hidden: name="match_id" value="<?= $row['id'] ?>"
  (all other fields pre-filled)

  Delete is a small form:
  hidden: name="action" value="delete_match"
  hidden: name="match_id" value="<?= $row['id'] ?>"
  onclick="return confirm('Delete this match?')"
endif;
-->

<?php
include '../include/header.php';
?>

<main class="matches-page">
    <?php if ($success): ?><p class="alert-success"><?= h($success) ?></p><?php endif; ?>
    <?php foreach ($errors as $error): ?><p class="alert-error"><?= h($error) ?></p><?php endforeach; ?>

    <section>
        <h2>Upcoming Fixtures</h2>
        <?php while($row = $upcoming->fetch_assoc()): ?>
            <div class="match-row">
                <span><?= h($row['match_date']) ?></span>
                <strong>vs <?= h($row['opponent']) ?></strong>
                <span>@ <?= h($row['venue']) ?></span>
                
                <?php if ($is_admin): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                        <input type="hidden" name="match_id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="action" value="delete_match">
                        <button type="submit" onclick="return confirm('Delete match?')">Delete</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </section>

    <?php if ($is_admin): ?>
        <section class="admin-panel">
            <h3>Add New Match</h3>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="create_match">
                <input type="text" name="opponent" placeholder="Opponent Name" required>
                <input type="date" name="match_date" required>
                <input type="text" name="venue" placeholder="Stadium Name">
                <select name="status">
                    <option value="upcoming">Upcoming</option>
                    <option value="completed">Completed</option>
                </select>
                <button type="submit">Add Match</button>
            </form>
        </section>
    <?php endif; ?>
</main>