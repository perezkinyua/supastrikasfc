<?php
require_once '../include/auth_guard.php';
require_once '../config/db.php';
require_login();

$articles = $conn->query(
    "SELECT n.title, n.content, n.published_at, u.username as author
     FROM news n
     LEFT JOIN users u ON n.author_id = u.id
     ORDER BY n.published_at DESC"
);
include '../include/header.php'
?>

<main class="news-container">
    <h2>Latest News & Announcements</h2>

    <div class="news-grid">
        <?php if ($articles && $articles->num_rows > 0): ?>
            <?php while($row = $articles->fetch_assoc()): ?>
                <article class="news-card">
                    <h3><?= h($row['title']) ?></h3> <div class="meta-info">
                        <small>By: <?= h($row['author']) ?></small> | 
                        <small>Published: <?= date('F j, Y', strtotime($row['published_at'])) ?></small>
                    </div>

                    <div class="news-content">
                        <p><?= nl2br(h($row['content'])) ?></p>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No news articles found. Check back later!</p>
        <?php endif; ?>
    </div>
</main>

<?php include '../include/footer.php'; ?>