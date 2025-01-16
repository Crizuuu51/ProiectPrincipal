<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    echo "<script>
        alert('You have to be connected as Manager!');
        window.location.href = 'login.php';
    </script>";
    session_destroy();
    exit;
}
?>
<?php
include 'config/database.php';
include 'includes/header1.php';

$id = $_GET['id'];
$client = $pdo->prepare("SELECT * FROM clients WHERE client_id = ?");
$client->execute([$id]);
$client = $client->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_name = $_POST['client_name'];

    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $pdo->prepare("UPDATE clients SET client_name = ?, phone_number = ?, email = ?, address = ? WHERE client_id = ?");
    $stmt->execute([$client_name, $phone_number, $email, $address, $id]);
    header("Location: clients_eng.php");
    exit;
}
?>
<head><link rel="stylesheet" href="css/style1.css"></head>
<main>
    <h1>Edit Client</h1>
    <form method="POST">
        <input type="text" name="client_name" value="<?= htmlspecialchars($client['client_name']) ?>" required>
        <input type="text" name="phone_number" value="<?= htmlspecialchars($client['phone_number']) ?>">
        <input type="email" name="email" value="<?= htmlspecialchars($client['email']) ?>">
        <textarea name="address"><?= htmlspecialchars($client['address']) ?></textarea>
        <button type="submit">Save</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
