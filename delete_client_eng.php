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
?>
<?php
include 'config/database.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM clients WHERE client_id = ?");
$stmt->execute([$id]);

header("Location: clients_eng.php");
exit;
?>
