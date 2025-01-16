<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    echo "<script>
        alert('You have to be connected as manager!');
        window.location.href = 'login.php';
    </script>";
    session_destroy();
    exit;
}
include 'config/database.php';
include 'includes/header1.php';

$message = '';

$clients = $pdo->query("SELECT client_id, client_name FROM clients")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_name = $_POST['project_name'];
    $client_id = $_POST['client_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $budget = $_POST['budget'];
    $description = $_POST['description'];

    if (empty($project_name) || empty($client_id) || empty($start_date) || empty($end_date) || empty($budget)) {
        $message = "All fields are required";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO projects (project_name, client_id, start_date, end_date, status, budget, description) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$project_name, $client_id, $start_date, $end_date, $status, $budget, $description]);
            $message = "project added successfully";
        } catch (PDOException $e) {
            $message = "Error to adding project " . $e->getMessage();
        }
    }
}
?>
<head><link rel="stylesheet" href="css/style1.css"></head>
<main>
    <h1>Add project</h1>

    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="add_projects_eng.php">
        <input type="text" name="project_name" placeholder="Project Name" required>

        <select name="client_id" required>
            <option value="">Select Client</option>
            <?php foreach ($clients as $client): ?>
                <option value="<?= $client['client_id'] ?>"><?= htmlspecialchars($client['client_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="date" name="start_date" placeholder="Start Date" required>
        <input type="date" name="end_date" placeholder="End Date" required>

        <select name="status" required>
            <option value="in_desfasurare">On Going</option>
            <option value="finalizat">Finalized</option>
            <option value="anulat">Cancelled</option>
        </select>

        <input type="number" name="budget" placeholder="Budget" step="0.01" required>
        <textarea name="description" placeholder="Project Description" required></textarea>

        <a href="projects_eng.php"> <button type="submit">Add project</button></a>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
