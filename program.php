<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<head><link rel="stylesheet" href="css/style1.css"></head>
<?php include 'includes/header.php'; ?>
