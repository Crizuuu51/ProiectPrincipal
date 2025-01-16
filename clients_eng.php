<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    echo "<script>
        alert('You have to be conected as manager!');
        window.location.href = 'login.php';
    </script>";
    session_destroy();
    exit;
}

include 'config/database.php';
include 'includes/header1.php';

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
    <script src="js/js.js"></script>
    <title>Clients</title>
</head>
<main>
<body>
    <h1>Clients</h1>


    <div class="dropdown" style="float: right;">
        <button class="dropbtn" style="background-color : #00ff66;">Sort Clients</button>
        <div class="dropdown-content">
            <a href="#" onclick="sortTable(0)">By Name</a>
            <a href="#" onclick="sortTable(2)">By Project</a>
        </div>
    </div>


    <table id="projectsTable">
        <thead>
        <tr>
            <th>Client Name</th>
            <th>Phone</th>
            <th>Project</th>
            <th>Email</th>
            <th>Adress</th>
            <th>Action</th>
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
                    <a href="edit_client_eng.php?id=<?= $client['client_id'] ?>">Edit</a>
                    <a href="delete_client_eng.php?id=<?= $client['client_id'] ?>" onclick="return confirm('Are yo u sure you want to delete this client?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="add_client_eng.php">
        <button type="button">Add Client</button>
    </a>
</body>
</main>

<?php include 'includes/footer.php'; ?>

</html>
