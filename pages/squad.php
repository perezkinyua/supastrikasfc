<?php
require_once '../include/auth_guard.php'; //
require_once '../config/db.php';           //
require_login();

$players = $conn->query(
    "SELECT * FROM players ORDER BY jersey_number ASC"
);

include '../include/header.php';
// Teammate loops $players to display player cards
?>

<main class="squad-container">
    <h2>Our Squad</h2>
    <div class="player-grid">
        <?php if ($players->num_rows > 0): ?>
            <?php while($row = $players->fetch_assoc()): ?>
                <div class="player-card">
                    <img src="../assets/images/players/<?= h($row['photo']) ?>" alt="<?= h($row['name']) ?>">
                    <h3>#<?= h($row['jersey_number']) ?> - <?= h($row['name']) ?></h3>
                    <p>Position: <?= h($row['position']) ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No players found in the database.</p>
        <?php endif; ?>
    </div>
</main>

<?php include '../include/footer.php'; ?>