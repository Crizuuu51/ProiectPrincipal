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

$sql = "SELECT u.user_id, u.full_name, u.username, p.project_name , u.salary , u.department
        FROM users u
        LEFT JOIN project_worker pa ON u.user_id = pa.user_id
        LEFT JOIN projects p ON pa.project_id = p.project_id
        WHERE u.role = 'angajat'";

$stmt = $pdo->query($sql);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<head><link rel="stylesheet" href="css/style1.css">
    <script src="js/js.js"></script></head>
<main>
    <h1>Angajați</h1>

    <div class="dropdown" style="float: right;">
        <button class="dropbtn" style="background-color : #00ff66;">Sortează Angajati</button>
        <div class="dropdown-content">
            <a href="#" onclick="sortTable(0)">După Nume</a>
            <a href="#" onclick="sortTable(2, true)">După Salariu</a>
            <a href="#" onclick="sortTable(3)">După Ocupatie</a>
            <a href="#" onclick="sortTable(4)">După Proiect</a>
        </div>
    </div>


    <table id="projectsTable">
        <thead>
        <tr>
            <th>Nume Angajat</th>
            <th>Username</th>
            <th>Salariu</th>
            <th>Ocupatie</th>
            <th>Proiecte</th>
            <th>Acțiuni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($members as $member): ?>
            <tr>
                <td><?= htmlspecialchars($member['full_name']) ?></td>
                <td><?= htmlspecialchars($member['username']) ?></td>
                <td><?= number_format($member["salary"],2)?></td>
                <td><?= htmlspecialchars($member["department"])?></td>
                <td>
                    <?= $member['project_name'] ? htmlspecialchars($member['project_name']) : "N/A" ?>
                </td>
                <td>
                    <a href="edit_member.php?id=<?= $member['user_id'] ?>">Editează</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php include 'includes/footer.php'; ?>
