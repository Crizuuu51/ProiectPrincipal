<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('You havte to be connected as a Manager');
        window.location.href = 'login.php';
    </script>";
    session_destroy();
    exit;
}
include 'includes/header1.php';
include 'config/database.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT full_name, email, phone_number FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = htmlspecialchars($_POST['email']);
    $phone_number = htmlspecialchars($_POST['phone_number']);

    if (!empty($full_name) && !empty($email) && !empty($phone_number)) {
        try {
            $update_stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone_number = ? WHERE user_id = ?");
            $update_stmt->execute([$full_name, $email, $phone_number, $user_id]);
            $success_message = "Information Updated with success";
        } catch (PDOException $e) {
            $error_message = "Error";
        }
    } else {
        $error_message = "All fields are required";
    }

    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style1.css">
    <title>User Profile</title>
</head>
<body>

<main>
    <section class="profile-container">
        <h2>Personal Information</h2>


        <?php if ($success_message): ?>
            <p class="success-message"><?= $success_message ?></p>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <p class="error-message"><?= $error_message ?></p>
        <?php endif; ?>


        <form method="POST" action="Profil.php" class="profile-form">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="phone_number">Phone:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" required>

            <button type="submit">Update</button>
        </form>
    </section>
</main>
</body>
</html>
<?php include 'includes/footer.php'; ?>
