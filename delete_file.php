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

if (isset($_GET['id'])) {
    $file_id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT file_path FROM project_files WHERE file_id = ?");
    $stmt->execute([$file_id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        if (file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }

        $stmt = $pdo->prepare("DELETE FROM project_files WHERE file_id = ?");
        $stmt->execute([$file_id]);

        header("Location: edit_project.php?id=" . $_GET['project_id'] . "&delete=success");
        exit;
    }
}
