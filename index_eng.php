<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    echo "<script>
        alert('You have to be connected as Manager!');
        window.location.href = 'login.php';
    </script>";
    session_destroy();
    exit;
}?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style1.css">
    <title>Front Page</title>
</head>
<body>
<?php include 'includes/header1.php'; ?>

<main>
    <h1>Welocome, <?= htmlspecialchars($_SESSION['full_name']) ?>!</h1>

 <table>

            <a href="members_eng.php">
                <button type="button">Employee Managerment</button>
            </a>
     <a href="clients_eng.php">
         <button type="button">Client Management</button>
     </a>


            <a href="projects_eng.php">
                <button type="button">Project Management</button>
            </a>
 </table>

</main>
<?php include 'includes/footer.php'; ?>