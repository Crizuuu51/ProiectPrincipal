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
        $message = "Toate câmpurile sunt obligatorii!";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO projects (project_name, client_id, start_date, end_date, status, budget, description) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$project_name, $client_id, $start_date, $end_date, $status, $budget, $description]);
            $message = "Proiectul a fost adăugat cu succes!";
        } catch (PDOException $e) {
            $message = "Eroare la adăugarea proiectului: " . $e->getMessage();
        }
    }
}
?>
<head><link rel="stylesheet" href="css/style1.css"></head>
<main>
    <h1>Adaugă Proiect</h1>

    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="projects.php">
        <input type="text" name="project_name" placeholder="Nume Proiect" required>

        <select name="client_id" required>
            <option value="">Selectează Clientul</option>
            <?php foreach ($clients as $client): ?>
                <option value="<?= $client['client_id'] ?>"><?= htmlspecialchars($client['client_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="date" name="start_date" placeholder="Data Începerii" required>
        <input type="date" name="end_date" placeholder="Data Finalizării" required>

        <select name="status" required>
            <option value="in_desfasurare">În desfășurare</option>
            <option value="finalizat">Finalizat</option>
            <option value="anulat">Anulat</option>
        </select>

        <input type="number" name="budget" placeholder="Buget" step="0.01" required>
        <textarea name="description" placeholder="Descriere Proiect" required></textarea>

        <a href="projects.php"> <button type="submit">Adaugă Proiect</button> </a>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
