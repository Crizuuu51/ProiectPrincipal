<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    echo "<script>
        alert('You have to be connected as Manager!');
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
    <title>Materials</title>
</head>
<body>
<?php include 'includes/header1.php'; ?>

<main>
    <h1>Materials</h1>

    <div class="dropdown" style="float: right;">
        <button class="dropbtn" style="background-color : #00ff66;">Sort Materials</button>
        <div class="dropdown-content">
            <a href="#" onclick="sortTable(0)">By Material Name</a>
            <a href="#" onclick="sortTable(1, true)">By Price per Unit</a>
            <a href="#" onclick="sortTable(2, true)">By Quantity</a>
            <a href="#" onclick="sortTable(3)">By Status</a>
        </div>
    </div>

    <table id="projectsTable" border="1">
        <thead>
        <tr>
            <th>Material Name</th>
            <th>Price per Unit</th>
            <th>Quantity</th>
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
                    <?= $material['stock_quantity'] == 0 ? '<span style="color:red;">Out of Stock</span>' : 'Available' ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <a href="add_material_eng.php"><button>Add Material</button></a>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>
