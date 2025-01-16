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

if (!isset($_GET['project_id']) || !isset($_GET['material_name'])) {
    echo "Date invalide!";
    exit;
}

$project_id = $_GET['project_id'];
$material_name = $_GET['material_name'];

$stmt = $pdo->prepare("
    SELECT SUM(pm.quantity) AS project_quantity, m.material_id, m.stock_quantity
    FROM project_materials pm
    INNER JOIN materials m ON pm.material_id = m.material_id
    WHERE pm.project_id = ? AND m.material_name = ?
");
$stmt->execute([$project_id, $material_name]);
$material = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$material) {
    echo "Materialul nu a fost găsit!";
    exit;
}

$project_quantity = $material['project_quantity'];
$material_id = $material['material_id'];
$stock_quantity = $material['stock_quantity'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity_to_return = floatval($_POST['quantity_to_return']);

    if ($quantity_to_return <= 0 || $quantity_to_return > $project_quantity) {
        echo "Cantitate invalidă!";
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE project_materials
        SET quantity = quantity - ?
        WHERE project_id = ? AND material_id = ?
    ");
    $stmt->execute([$quantity_to_return, $project_id, $material_id]);

    $stmt = $pdo->prepare("
        UPDATE materials
        SET stock_quantity = stock_quantity + ?
        WHERE material_id = ?
    ");
    $stmt->execute([$quantity_to_return, $material_id]);

    header("Location: edit_project.php?id=" . $project_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style1.css">
    <title>Returnare Material</title>
</head>
<body>
<?php include 'includes/header.php'; ?>

<main>
    <h1>Returnează Material</h1>
    <p>Material: <strong><?= htmlspecialchars($material_name) ?></strong></p>
    <p>Cantitate disponibilă în proiect: <strong><?= htmlspecialchars($project_quantity) ?></strong></p>

    <form action="" method="POST">
        <label for="quantity_to_return">Cantitate de returnat:</label>
        <input type="number" id="quantity_to_return" name="quantity_to_return" min="1" max="<?= htmlspecialchars($project_quantity) ?>" step="0.01" required>
        <button type="submit">Returnează</button>
    </form>

    <a href="edit_project.php?id=<?= htmlspecialchars($project_id) ?>">Înapoi la Proiect</a>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>
