<?php
include("include/header.php");
require_once 'config/db.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}
?>

<section class="members-area">
    <h2>Exclusive Members Content</h2>
    <p>Welcome <?php echo $_SESSION['user']; ?> 👑</p>

    <div class="exclusive-box">
        <h3>Team Strategy</h3>
        <p>Confidential tactics for upcoming matches.</p>
    </div>

    <div class="exclusive-box">
        <h3>Training Schedule</h3>
        <p>Private training plans and recovery systems.</p>
    </div>
</section>

<?php include("include/footer.php"); ?>