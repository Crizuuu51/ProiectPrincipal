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

if (!isset($_GET['project_id']) || empty($_GET['project_id'])) {
    header("Location: projects.php");
    exit;
}

$project_id = $_GET['project_id'];

$materials_stmt = $pdo->query("SELECT material_id, material_name, stock_quantity, price_per_unit FROM materials WHERE stock_quantity > 0");
$materials = $materials_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $material_id = $_POST['material_id'];
    $quantity = $_POST['quantity'];

    $stmt = $pdo->prepare("SELECT stock_quantity FROM materials WHERE material_id = ?");
    $stmt->execute([$material_id]);
    $stock = $stmt->fetchColumn();

    if ($stock >= $quantity) {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO project_materials (project_id, material_id, quantity, cost) VALUES (?, ?, ?, ?)");
            $stmt->execute([$project_id, $material_id, $quantity, $quantity * $_POST['price_per_unit']]);

            $stmt = $pdo->prepare("UPDATE materials SET stock_quantity = stock_quantity - ? WHERE material_id = ?");
            $stmt->execute([$quantity, $material_id]);

            $pdo->commit();

            header("Location: edit_project.php?id=$project_id");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            echo "A apărut o eroare: " . $e->getMessage();
        }
    } else {
        echo "Cantitate insuficientă în stoc.";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style1.css">
    <title>Adaugă Material la Proiect</title>
</head>
<body>
<?php include 'includes/header.php'; ?>

<main>
    <h1>Adaugă Material la Proiect</h1>
    <form action="" method="POST">
        <label for="material_id">Material:</label>
        <select id="material_id" name="material_id" required>
            <?php foreach ($materials as $material): ?>
                <option value="<?= $material['material_id'] ?>">
                    <?= htmlspecialchars($material['material_name']) ?> (<?= $material['stock_quantity'] ?> disponibile, <?= number_format($material['price_per_unit'], 2) ?> RON/unitate)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="quantity">Cantitate:</label>
        <input type="number" id="quantity" name="quantity" min="1" required>

        <input type="hidden" name="price_per_unit" value="<?= $material['price_per_unit'] ?>">
        <button type="submit">Adaugă</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>
