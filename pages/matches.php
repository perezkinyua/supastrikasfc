<?php
require_once '../include/auth_guard.php';
require_once '../config/db.php';
require_login();

// Fetch upcoming matches
$upcoming = $conn->query("SELECT * FROM matches WHERE status='upcoming' ORDER BY match_date ASC");

// Fetch recent completed matches
$results = $conn->query("SELECT * FROM matches WHERE status='completed' ORDER BY match_date DESC LIMIT 10");
include "../include/header.php"
?>

<!DOCTYPE html>
<html>
<body>
    <h2>Upcoming Matches</h2>
    <table border="1">
        <tr><th>Opponent</th><th>Date</th><th>Venue</th></tr>
        <?php while($row = $upcoming->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['opponent']) ?></td>
                <td><?= $row['match_date'] ?></td>
                <td><?= htmlspecialchars($row['venue']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h2>Recent Results</h2>
    <ul>
        <?php while($row = $results->fetch_assoc()): ?>
            <li>
                Supa Strikas <?= $row['score_home'] ?> - <?= $row['score_away'] ?> <?= htmlspecialchars($row['opponent']) ?>
                (<?= $row['match_date'] ?>)
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>