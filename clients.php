<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    echo "<script>
        alert('Trebuie să fii conectat ca manager pentru a accesa aceste informații!');
        window.location.href = 'login.php';
    </script>";
    session_destroy();
    exit;
}

include 'config/database.php';
include 'includes/header.php';

$clients = $pdo->query("
    SELECT clients.*, projects.project_name 
    FROM clients
    LEFT JOIN projects ON clients.client_id = projects.client_id
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style1.css">
    <script src="js/js.js"></script> <!-- Include scriptul JavaScript -->
    <title>Clienți</title>
</head>
<main>
<body>
    <h1>Clienți</h1>


    <div class="dropdown" style="float: right;">
        <button class="dropbtn" style="background-color : #00ff66;">Sortează Clienți</button>
        <div class="dropdown-content">
            <a href="#" onclick="sortTable(0)">După Nume</a>
            <a href="#" onclick="sortTable(2)">După Proiect</a>
        </div>
    </div>

    <!-- Tabelul clienților -->
    <table id="projectsTable">
        <thead>
        <tr>
            <th>Nume Client</th>
            <th>Telefon</th>
            <th>Proiect</th>
            <th>Email</th>
            <th>Adresă</th>
            <th>Acțiuni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($clients as $client): ?>
            <tr>
                <td><?= htmlspecialchars($client['client_name']) ?></td>
                <td><?= htmlspecialchars($client['phone_number']) ?></td>
                <td><?= htmlspecialchars($client['project_name']) ? htmlspecialchars($client['project_name']) : "N/A" ?></td>
                <td><?= htmlspecialchars($client['email']) ?></td>
                <td><?= htmlspecialchars($client['address']) ?></td>
                <td>
                    <a href="edit_client.php?id=<?= $client['client_id'] ?>">Editează</a>
                    <a href="delete_client.php?id=<?= $client['client_id'] ?>" onclick="return confirm('Sigur ștergi acest client?')">Șterge</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="add_client.php">
        <button type="button">Adaugă Client</button>
    </a>
</body>
</main>

<?php include 'includes/footer.php'; ?>

</html>
