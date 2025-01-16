<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    echo "<script>
        alert('You have to be connected as a Manager!');
        window.location.href = 'login.php';
    </script>";
    session_destroy();
    exit;
}
?>

<?php
include 'config/database.php';
include 'includes/header1.php';

$projects = $pdo->query("
    SELECT 
        projects.project_id, 
        projects.project_name, 
        clients.client_name, 
        projects.status, 
        projects.service_cost,
        projects.budget,
        IFNULL(SUM(pm.quantity * m.price_per_unit), 0) AS total_material_cost,
         (IFNULL(SUM(pm.quantity * m.price_per_unit), 0) + projects.service_cost) AS total_cost
    FROM projects
    LEFT JOIN clients ON projects.client_id = clients.client_id
    LEFT JOIN project_materials pm ON projects.project_id = pm.project_id
    LEFT JOIN materials m ON pm.material_id = m.material_id
    GROUP BY projects.project_id
")->fetchAll(PDO::FETCH_ASSOC);
?>

<head><link rel="stylesheet" href="css/style1.css">
    <script src="js/js.js"></script></head>
<main>
    <h1>Proiecte</h1>

    <div class="dropdown" style="float: right;">
        <button class="dropbtn" style="background-color : #00ff66;">Sort Projects</button>
        <div class="dropdown-content">
            <a href="#" onclick="sortTable(0)">By Project Name</a>
            <a href="#" onclick="sortTable(1)">By Client</a>
            <a href="#" onclick="sortTable(2)">By Status</a>
            <a href="#" onclick="sortTable(3, true)">By Budget</a>
            <a href="#" onclick="sortTable(4, true)">By Total Cost of Materials</a>
        </div>
    </div>

    <table id="projectsTable">
        <thead>
        <tr>
            <th>Project name</th>
            <th>Client</th>
            <th>Status</th>
            <th>Budget</th>
            <th>Total Cost</th>
            <th>Total Material Cost</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($projects as $project): ?>
            <tr>
                <td><?= htmlspecialchars($project['project_name']) ?></td>
                <td><?= htmlspecialchars($project['client_name']) ?></td>
                <td><?= htmlspecialchars($project['status']) ?></td>
                <td><?= number_format($project['budget'], 2) ?> RON</td>
                <td><?= number_format($project['total_cost'], 2) ?> RON</td>
                <td><?= number_format($project['total_material_cost'], 2) ?> RON</td>
                <td>
                    <a href="edit_project_eng.php?id=<?= $project['project_id'] ?>">View Details</a>
                    <form action="delete_project_eng.php" method="POST" style="display:inline;">
                        <input type="hidden" name="project_id" value="<?= $project['project_id'] ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this project')">Delete</button>
                    </form>
                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <a href="add_projects.php">
        <button type="button">Adaugă Proiect</button>
    </a>
</main>
<?php include 'includes/footer.php'; ?>
