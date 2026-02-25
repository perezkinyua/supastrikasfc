<?php
require_once '../includes/auth_guard.php';
require_once '../config/db.php';
require_login();

// Fetch next upcoming match for the hero section
$stmt = $conn->prepare(
    "SELECT * FROM matches WHERE status='upcoming'
     ORDER BY match_date ASC LIMIT 1"
);
$stmt->execute();
$next_match = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch 3 most recent news items
$recent_news = $conn->query(
    "SELECT title, published_at FROM news
     ORDER BY published_at DESC LIMIT 3"
);
?>
<!-- 
Teammate uses:
- $_SESSION['username'] for welcome message
- $next_match for a "Next Match" countdown card
- $recent_news loop for a news ticker or cards
- $_COOKIE['theme'] ?? 'light' on the body class
- if no upcoming matches are available dispa; "No matches scheduled"V
-->