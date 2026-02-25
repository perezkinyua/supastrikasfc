<?php
require_once '../includes/auth_guard.php';
require_once '../config/db.php';
require_login();

$players = $conn->query(
    "SELECT * FROM players ORDER BY jersey_number ASC"
);
// Teammate loops $players to display player cards
?>