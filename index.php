<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    echo "<script>
        alert('Trebuie să fii conectat ca manager pentru a accesa aceste informații!');
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
    <title>Pagina Principală</title>
</head>
<body>
<?php include 'includes/header.php'; ?>

<main>
    <h1>Bine ai venit, <?= htmlspecialchars($_SESSION['full_name']) ?>!</h1>
    <p>Aceasta este pagina principală a aplicației de management al firmei de construcții.</p>
 <table>

            <a href="members.php">
                <button type="button">Gestionare Angajati</button>
            </a>
     <a href="clients.php">
         <button type="button">Gestionare Clienti</button>
     </a>


            <a href="projects.php">
                <button type="button">Gestionare Proiecte</button>
            </a>
 </table>

</main>
<?php include 'includes/footer.php'; ?>