<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    echo "<script>
        alert('You havte to be connected as a Manager!');
        window.location.href = 'login.php';
    </script>";
    session_destroy();
    exit;
}

include 'config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: projects_eng.php");
    exit;
}

$project_id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT projects.project_id, projects.project_name, projects.status, projects.budget, projects.client_id, clients.client_name, projects.service_cost
    FROM projects
    LEFT JOIN clients ON projects.client_id = clients.client_id
    WHERE projects.project_id = ?
");
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    echo "Project Could not be found.";
    exit;
}

$files_stmt = $pdo->prepare("SELECT * FROM project_files WHERE project_id = ?");
$files_stmt->execute([$project_id]);
$files = $files_stmt->fetchAll(PDO::FETCH_ASSOC);

$clients = $pdo->query("SELECT client_id, client_name FROM clients")->fetchAll(PDO::FETCH_ASSOC);

$materials_stmt = $pdo->prepare("
    SELECT 
        m.material_name, 
        SUM(pm.quantity) AS total_quantity, 
        m.price_per_unit, 
        SUM(pm.quantity * m.price_per_unit) AS total_cost
    FROM project_materials pm
    INNER JOIN materials m ON pm.material_id = m.material_id
    WHERE pm.project_id = ?
    GROUP BY m.material_name, m.price_per_unit
");
$materials_stmt->execute([$project_id]);
$materials = $materials_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_name = $_POST['project_name'];
    $status = $_POST['status'];
    $budget = $_POST['budget'];
    $service_cost = $_POST['service_cost'];
    $client_id = $_POST['client_id'];


    $stmt = $pdo->prepare("
        UPDATE projects 
        SET project_name = ?, status = ?, budget = ?, client_id = ? , service_cost=?
        WHERE project_id = ?
    ");
    $stmt->execute([$project_name, $status, $budget, $client_id, $service_cost, $project_id]);

    header("Location: projects_eng.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style1.css">
    <title>Edit Project</title>
</head>
<body>
<?php include 'includes/header1.php'; ?>

<main>
    <h1>Edit Project</h1>
    <form action="" method="POST">
        <label for="project_name">Project Name:</label>
        <input type="text" id="project_name" name="project_name" value="<?= htmlspecialchars($project['project_name']) ?>" required>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="in_desfasurare" <?= $project['status'] === 'in_desfasurare' ? 'selected' : '' ?>>On Going</option>
            <option value="finalizat" <?= $project['status'] === 'finalizat' ? 'selected' : '' ?>>Finalized</option>
            <option value="anulat" <?= $project['status'] === 'anulat' ? 'selected' : '' ?>>Canceled</option>
        </select>

        <label for="budget">Budget:</label>
        <input type="number" id="budget" name="budget" value="<?= htmlspecialchars($project['budget']) ?>" required>

        <label for="service_cost">Service Cost:</label>
        <input type="number" id="service_cost" name="service_cost" value="<?= htmlspecialchars($project['service_cost']) ?>" required>

        <label for="client_id">Client:</label>
        <select id="client_id" name="client_id" required>
            <?php foreach ($clients as $client): ?>
                <option value="<?= $client['client_id'] ?>" <?= $client['client_id'] == $project['client_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($client['client_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Save</button>
    </form>

    <h2>Materials Used in the Project</h2>
    <table border="1">
        <thead>
        <tr>
            <th>Material Name</th>
            <th>Total Quantity</th>
            <th>Price per unit (RON)</th>
            <th>Toral Price (RON)</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($materials as $material): ?>
            <?php if ($material['total_quantity'] > 0): ?>
                <tr>
                    <td><?= htmlspecialchars($material['material_name']) ?></td>
                    <td><?= $material['total_quantity'] ?></td>
                    <td><?= number_format($material['price_per_unit'], 2) ?></td>
                    <td><?= number_format($material['total_cost'], 2) ?></td>
                    <td>
                        <form action="return_eng.php" method="GET" style="display:inline;">
                            <input type="hidden" name="project_id" value="<?= $project_id ?>">
                            <input type="hidden" name="material_name" value="<?= htmlspecialchars($material['material_name']) ?>">
                            <input type="hidden" name="total_quantity" value="<?= $material['total_quantity'] ?>">
                            <button type="submit">Return Material </button>
                        </form>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>




    <br>
    <a href="add_material_project_eng.php?project_id=<?= $project_id ?>"> <button class="button">Add Material</button></a>


    <h2>Uploaded Files</h2>
    <form action="upload_file_eng.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="project_id" value="<?= $project_id ?>">
        <label for="file">Select File:</label>
        <input type="file" name="file" id="file" required>
        <button type="submit">Upload File</button>
    </form>
    <table>
        <tr>
            <th>File Name</th>
            <th>Tipe</th>
            <th>Upload Date</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($files as $file): ?>
            <tr>
                <td><?= htmlspecialchars($file['file_name']) ?></td>
                <td><?= htmlspecialchars($file['file_type']) ?></td>
                <td><?= htmlspecialchars($file['uploaded_at']) ?></td>
                <td>
                    <a href="<?= htmlspecialchars($file['file_path']) ?>" download>Download</a>
                    <a href="delete_file_eng.php?id=<?= $file['file_id'] ?>">Delete</a>
                    <a href="<?= htmlspecialchars($file['file_path']) ?>" target="_blank">View</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>
