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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'])) {
    include 'config/database.php';
    $projectId = $_POST['project_id'];

    try {
        // Ștergere asociată în project_worker
        $pdo->prepare("DELETE FROM project_worker WHERE project_id = ?")->execute([$projectId]);

        // Ștergere asociată în project_materials
        $pdo->prepare("DELETE FROM project_materials WHERE project_id = ?")->execute([$projectId]);

        // Ștergere proiect
        $stmt = $pdo->prepare("DELETE FROM projects WHERE project_id = ?");
        $stmt->execute([$projectId]);

        echo "<script>
            alert('Proiectul a fost șters cu succes!');
            window.location.href = 'projects.php'; // Înlocuiți cu fișierul care afișează proiectele
        </script>";
    } catch (PDOException $e) {
        echo "<script>
            alert('A apărut o eroare: {$e->getMessage()}');
            window.location.href = 'projects.php';
        </script>";
    }
} else {
    echo "<script>
        alert('Solicitare invalidă!');
        window.location.href = 'projects.php';
    </script>";
    exit;
}
