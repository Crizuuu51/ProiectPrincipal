<?php
include 'config/database.php';


session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $message = "All fields must be filled";
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
                    header("Location: index_eng.php");
                else
                    header("Location: program-eng.php");
                exit;
            } else {
                $message = "Wrong username or password.";
            }
        } catch (PDOException $e) {
            $message = "Error to autentification" . $e->getMessage();
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
    <title>User Authentication</title>
</head>
<body>
<main>
    <h1>Authentication</h1>
    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" action="login_eng.php">
        <input type="text" name="username" placeholder="Usser Name" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Authenticate</button>
        <a href="login.php">Nu stii engleza?</a>
    </form>

</main>
</body>
</html>
<?php include 'includes/footer.php'; ?>
