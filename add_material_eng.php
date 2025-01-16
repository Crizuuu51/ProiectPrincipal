<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    echo "<script>
        alert('You have to be conected as a manager!');
        window.location.href = 'login.php';
    </script>";
    session_destroy();
    exit;
}
include 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $material_name = trim($_POST['material_name']);
    $price_per_unit = $_POST['price_per_unit'];
    $stock_quantity = $_POST['stock_quantity'];

    if (empty($material_name) || $price_per_unit <= 0 || $stock_quantity < 0) {
        $error_message = "All fields are required";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT material_id, stock_quantity, price_per_unit FROM materials WHERE material_name = ?");
            $stmt->execute([$material_name]);
            $existing_material = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_material) {
                $new_quantity = $existing_material['stock_quantity'] + $stock_quantity;

                if ($price_per_unit > $existing_material['price_per_unit']) {
                    $stmt = $pdo->prepare("UPDATE materials SET stock_quantity = ?, price_per_unit = ? WHERE material_id = ?");
                    $stmt->execute([$new_quantity, $price_per_unit, $existing_material['material_id']]);
                } else {

                    $stmt = $pdo->prepare("UPDATE materials SET stock_quantity = ? WHERE material_id = ?");
                    $stmt->execute([$new_quantity, $existing_material['material_id']]);
                }
            } else {
                $stmt = $pdo->prepare("INSERT INTO materials (material_name, price_per_unit, stock_quantity) VALUES (?, ?, ?)");
                $stmt->execute([$material_name, $price_per_unit, $stock_quantity]);
            }

            header("Location: materials_eng.php");
            exit;
        } catch (Exception $e) {
            $error_message = "A error has appeared " . $e->getMessage();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style1.css">
    <title>Add Material</title>
</head>
<body>
<?php include 'includes/header1.php'; ?>

<main>
    <h1>Add Material</h1>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>
    <form action="" method="POST">
        <label for="material_name">Material Name</label>
        <input type="text" id="material_name" name="material_name" required>

        <label for="price_per_unit">Price per Unit (RON):</label>
        <input type="number" step="0.01" id="price_per_unit" name="price_per_unit" required>

        <label for="stock_quantity">Quantity:</label>
        <input type="number" step="0.01" id="stock_quantity" name="stock_quantity" required>

        <button type="submit">Save</button>
    </form>
    <br>
    <a href="materials_eng.php"><button>Back to Materials</button></a>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>
