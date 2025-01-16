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
                <li><a href="index_eng.php">Home</a></li>
                <li><a href="clients_eng.php">Clients</a></li>
                <li><a href="members_eng.php">Employees</a></li>
                <li><a href="projects_eng.php">Project</a></li>
                <li><a href="materials_eng.php">Materials</a></li>
                <li><a href="register_eng.php">Create User</a></li>
                <?php
            }
            ?>

            <?php
            if (isset($_SESSION['user_id'])) {
                ?>
                <li class="dropdown">
                    <a href="Profil_eng.php" class="dropbtn" style="font-weight: 900; color : black;">
                        <?= htmlspecialchars($_SESSION['full_name']) ?>
                    </a>
                    <div class="dropdown-content">
                        <a href="Profil_eng.php">Profile</a>
                        <a href="index.php">RO</a>
                        <a href="logout_eng.php">Logout</a>

                    </div>
                </li>
                <?php
            }
            ?>
        </ul>
    </nav>
</header>
