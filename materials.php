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

$stmt = $pdo->query("SELECT material_name, unit, price_per_unit, stock_quantity FROM materials");
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style1.css">
    <script src="js/js.js"></script>
    <title>Materiale</title>
</head>
<body>
<?php include 'includes/header.php'; ?>

<main>
    <h1>Materiale</h1>

    <div class="dropdown" style="float: right;">
        <button class="dropbtn" style="background-color : #00ff66;">Sortează Materiale</button>
        <div class="dropdown-content">
            <a href="#" onclick="sortTable(0)">După Nume Material</a>
            <a href="#" onclick="sortTable(1, true)">După Preț per Unitate</a>
            <a href="#" onclick="sortTable(2, true)">După Cantitate</a>
            <a href="#" onclick="sortTable(3)">După Status</a>
        </div>
    </div>

    <table id="projectsTable" border="1">
        <thead>
        <tr>
            <th>Nume Material</th>
            <th>Preț per unitate</th>
            <th>Cantitate</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($materials as $material): ?>
            <tr>
                <td><?= htmlspecialchars($material['material_name']) ?></td>
                <td><?= number_format($material['price_per_unit'], 2) ?> RON</td>
                <td><?= $material['stock_quantity'] ?></td>
                <td>
                    <?= $material['stock_quantity'] == 0 ? '<span style="color:red;">Epuizat</span>' : 'Disponibil' ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <a href="add_material.php"><button>Adaugă Material</button></a>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>
