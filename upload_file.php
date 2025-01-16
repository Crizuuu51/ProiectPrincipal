<?php
session_start();
include 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $project_id = $_POST['project_id'];
    $file = $_FILES['file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $file_name = basename($file['name']);
        $file_type = pathinfo($file_name, PATHINFO_EXTENSION);
        $upload_dir = 'uploads/';
        $file_path = $upload_dir . uniqid() . '_' . $file_name;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Insert file info into the database
            $stmt = $pdo->prepare("INSERT INTO project_files (project_id, file_name, file_path, file_type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$project_id, $file_name, $file_path, $file_type]);

            header("Location: edit_project.php?id=" . $project_id . "&upload=success");
            exit;
        } else {
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "Error uploading file.";
    }
}
