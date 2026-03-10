<?php
require_once '../include/auth_guard.php';
require_once '../config/db.php';
require_login();

// Fetch all players ordered by jersey number
$players = $conn->query("SELECT * FROM players ORDER BY jersey_number ASC");
include "../include/header.php";
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        .player-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .player-card { border: 1px solid #ccc; padding: 10px; text-align: center; border-radius: 8px; }
        .player-photo { width: 100%; height: auto; border-radius: 4px; }
    </style>
</head>

<h1>Meet the Squad</h1>

<div class="player-grid">
<?php while($player = $players->fetch_assoc()): ?>

<?php 
$photoPath = !empty($player['photo_url'])
    ? "../" . $player['photo_url']
    : "../assets/images/players/default-player.jfif";
?>

<div class="player-card">
    <img src="<?= htmlspecialchars($photoPath) ?>" class="player-photo">

    <h3>#<?= htmlspecialchars($player['jersey_number']) ?> <?= htmlspecialchars($player['name']) ?></h3>
    <p><strong>Position:</strong> <?= htmlspecialchars($player['position']) ?></p>
    <p><em><?= htmlspecialchars($player['nationality']) ?></em></p>
</div>

<?php endwhile; ?>
</div>


<?php include '../include/footer.php'; ?>