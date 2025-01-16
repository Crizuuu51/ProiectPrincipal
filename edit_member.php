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

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: members.php");
    exit;
}

$user_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    echo "Angajatul nu a fost găsit.";
    exit;
}

$projects_stmt = $pdo->query("SELECT project_id, project_name FROM projects");
$projects = $projects_stmt->fetchAll(PDO::FETCH_ASSOC);

$current_project_stmt = $pdo->prepare("SELECT project_id FROM project_worker WHERE user_id = ?");
$current_project_stmt->execute([$user_id]);
$current_project = $current_project_stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $department = $_POST['department'];
    $project_id = $_POST['project_id'];
    $salary = $_POST['salary']; // Preluăm salariul din formular

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, phone_number = ?, department = ?, salary = ? WHERE user_id = ?");
        $stmt->execute([$full_name, $username, $email, $phone_number, $department, $salary, $user_id]);

        $stmt = $pdo->prepare("DELETE FROM project_worker WHERE user_id = ?");
        $stmt->execute([$user_id]);

        if ($project_id) {
            $stmt = $pdo->prepare("INSERT INTO project_worker (user_id, project_id, role_in_project) VALUES (?, ?, 'worker')");
            $stmt->execute([$user_id, $project_id]);
        }

        $pdo->commit();

        header("Location: members.php");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "A apărut o eroare: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style1.css">
    <title>Editează Angajat</title>
</head>
<body>
<?php include 'includes/header.php'; ?>

<main>
    <h1>Editează Angajat</h1>
    <form action="" method="POST">
        <label for="full_name">Nume complet:</label>
        <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($member['full_name']) ?>" required>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($member['username']) ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($member['email']) ?>">

        <label for="phone_number">Telefon:</label>
        <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($member['phone_number']) ?>">

        <label for="department">Departament:</label>
        <input type="text" id="department" name="department" value="<?= htmlspecialchars($member['department']) ?>">

        <label for="salary">Salariu:</label>
        <input type="number" step="0.01" id="salary" name="salary" value="<?= htmlspecialchars($member['salary']) ?>" required>

        <label for="project_id">Proiect:</label>
        <select id="project_id" name="project_id">
            <option value="">Niciun proiect</option>
            <?php foreach ($projects as $project): ?>
                <option value="<?= $project['project_id'] ?>" <?= $project['project_id'] == $current_project ? 'selected' : '' ?>>
                    <?= htmlspecialchars($project['project_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Salvează</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>
