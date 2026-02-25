<?php
require_once '../includes/auth_guard.php';
require_once '../config/db.php';
require_login();

$articles = $conn->query(
    "SELECT n.title, n.content, n.published_at, u.username as author
     FROM news n
     LEFT JOIN users u ON n.author_id = u.id
     ORDER BY n.published_at DESC"
);
// Teammate loops $articles
?>