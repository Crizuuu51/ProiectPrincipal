<?php
include 'config/database.php';


session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $message = "Toate câmpurile sunt obligatorii!";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];

                if ( $_SESSION['role'] == 'management')
                    header("Location: index.php");
                else
                    header("Location: program.php");
                exit;
            } else {
                $message = "Nume de utilizator sau parolă incorectă.";
            }
        } catch (PDOException $e) {
            $message = "Eroare la autentificare: " . $e->getMessage();
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
    <title>Autentificare Utilizator</title>
</head>
<body>
<main>
    <h1>Autentificare</h1>
    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <input type="text" name="username" placeholder="Nume de utilizator" required>
        <input type="password" name="password" placeholder="Parolă" required>
        <button type="submit">Autentifică-te</button>
        <a href="login_eng.php">Don't know Romanian?</a>
    </form>

</main>
</body>
</html>
<?php include 'includes/footer.php'; ?>
