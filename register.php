<?php
include 'config/database.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    echo "<script>
        alert('Trebuie să fii conectat ca manager pentru a accesa aceste informații!');
        window.location.href = 'login.php';
    </script>";
    session_destroy();
    exit;
}
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $hire_date = $_POST['hire_date'];
    $salary = $_POST['salary'];
    $phone_number = $_POST['phone_number'];
    $department = $_POST['department'];

    if (empty($username) || empty($password) || empty($full_name) || empty($email) || empty($role) || empty($hire_date) || empty($salary) || empty($phone_number) || empty($department)) {
        $message = "Toate câmpurile sunt obligatorii!";
    } else {

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {

            $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, email, role, hire_date, salary, phone_number, department) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $hashed_password, $full_name, $email, $role, $hire_date, $salary, $phone_number, $department]);
            $message = "Înregistrarea a fost realizată cu succes!";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "Numele de utilizator sau email-ul este deja înregistrat.";
            } else {
                $message = "Eroare la înregistrare: " . $e->getMessage();
            }
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
    <title>Înregistrare Utilizator</title>
</head>
<body>
<main>
    <h1>Înregistrare Utilizator</h1>
    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" action="register.php">
        <input type="text" name="username" placeholder="Nume de utilizator" required>
        <input type="password" name="password" placeholder="Parolă" required>
        <input type="text" name="full_name" placeholder="Nume complet" required>
        <select name="role" required>
            <option value="angajat">Angajat</option>
            <option value="management">Management</option>
        </select>
        <input type="email" name="email" placeholder="Email" required>
        <input type="date" name="hire_date" placeholder="Data angajării" required>
        <input type="number" name="salary" placeholder="Salariu" required>
        <input type="text" name="phone_number" placeholder="Număr de telefon" required>
        <input type="text" name="department" placeholder="Departament" required>
        <button type="submit">Înregistrează-te</button>
    </form>
</main>
</body>
</html>
<?php include 'includes/footer.php'; ?>