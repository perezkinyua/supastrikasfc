<?php 
require_once '../include/auth_guard.php';
include '../include/header.php';
?>

<section class="hero">
    <h1>Welcome to Juja Titans FC</h1>
    <p>Passion. Pride. Power.</p>
    
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="home.php" class="btn">Go to Dashboard</a>
    <?php else: ?>
        <a href="../auth/register.php" class="btn">Join The Movement</a>
    <?php endif; ?>
</section>

<section class="about">
    <h2>About Us</h2>
    <p>Juja Titans FC is built on discipline, talent, and ambition. 
    We represent the future of Kenyan football.</p>
</section>

<?php include '../include/footer.php'; ?>