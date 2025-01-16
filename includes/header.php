<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">

    <title>Management Firma Construcții</title>
</head>
<body>
<header>
    <nav>
        <ul>
            <?php

            if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'management') {
                ?>
                <li><a href="index.php">Acasă</a></li>
                <li><a href="clients.php">Clienți</a></li>
                <li><a href="members.php">Angajați</a></li>
                <li><a href="projects.php">Proiecte</a></li>
                <li><a href="materials.php">Materiale</a></li>
                <li><a href="register.php">Creare User</a></li>
                <?php
            }
            ?>

            <?php
            if (isset($_SESSION['user_id'])) {
                ?>
                <li class="dropdown">
                    <a href="Profil.php" class="dropbtn" style="font-weight: 900; color : black;">
                        <?= htmlspecialchars($_SESSION['full_name']) ?>
                    </a>
                    <div class="dropdown-content">
                        <a href="Profil.php">Profil</a>
                        <a href="index_eng.php">ENG</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </li>
                <?php
            }
            ?>
        </ul>
    </nav>
</header>
