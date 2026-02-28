<?php
require_once '../include/auth_guard.php'; // Use 'include' as per your folder rename
require_once '../config/db.php';           //
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

include '../include/header.php';
?>
<!-- 
Teammate uses:
- $_SESSION['username'] for welcome message
- $next_match for a "Next Match" countdown card
- $recent_news loop for a news ticker or cards
- $_COOKIE['theme'] ?? 'light' on the body class
- if no upcoming matches are available dispa; "No matches scheduled"V
-->

<body class="<?= $_COOKIE['theme'] ?? 'light' ?>">
    <header>
        <h1>Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
    </header>

<section class="next-match-card">
    <h2>Upcoming Match</h2>
    <?php if ($next_match): ?>
        <div class="match-details">
            <p><strong>Opponent:</strong> <?= $next_match['opponent_name'] ?></p>
            <p><strong>Date:</strong> <?= $next_match['match_date'] ?></p>
            <p><strong>Venue:</strong> <?= $next_match['venue'] ?></p>
        </div>
    <?php else: ?>
        <p>No matches scheduled at the moment. Stay tuned!</p>
    <?php endif; ?>

    <h2>News</h2>
    <?php while($news = $recent_news->fetch_assoc()): ?>
    <div class="news-card"><?= h($news['title']) ?></div>
    <?php endwhile; ?>
    
</section>