<?php
require_once '../include/auth_guard.php';
require_once '../config/db.php';
require_login();

// Fetch all articles with author names
$articles = $conn->query(
    "SELECT n.title, n.content, n.published_at, u.username as author
     FROM news n
     LEFT JOIN users u ON n.author_id = u.id
     ORDER BY n.published_at DESC"
);
include '../include/header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Latest News</title>
</head>
<body>
    <h1>Latest News</h1>
    <?php while($row = $articles->fetch_assoc()): ?>
        <div class="news-item">
            <h2><?= htmlspecialchars($row['title']) ?></h2>
            <p><small>Published on <?= $row['published_at'] ?> by <?= htmlspecialchars($row['author']) ?></small></p>
            <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
            <hr>
        </div>
    <?php endwhile; ?>
</body>
</html>