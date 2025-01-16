<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    echo "<script>
        alert('You have to be connected as a Manager!');
        window.location.href = 'login_eng.php';
    </script>";
    session_destroy();
    exit;
}

include 'config/database.php';
include 'includes/header1.php';

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
    <h1>Employee</h1>

    <div class="dropdown" style="float: right;">
        <button class="dropbtn" style="background-color : #00ff66;">Sort Employee</button>
        <div class="dropdown-content">
            <a href="#" onclick="sortTable(0)">By Name</a>
            <a href="#" onclick="sortTable(2, true)">By Salary</a>
            <a href="#" onclick="sortTable(3)">By Occupation</a>
            <a href="#" onclick="sortTable(4)">By Project</a>
        </div>
    </div>


    <table id="projectsTable">
        <thead>
        <tr>
            <th>Employee Name</th>
            <th>Username</th>
            <th>Salary</th>
            <th>Ocupation</th>
            <th>Projects</th>
            <th>Action</th>
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
                    <a href="edit_member_eng.php?id=<?= $member['user_id'] ?>">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php include 'includes/footer.php'; ?>
