<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['content']) && !empty(trim($_POST['content']))) {
    $user_id = $_SESSION['user_id'];
    $content = trim($_POST['content']);

    $image = null;

    // Handle image upload (optional)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "assets/images/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
        $image = $targetFile;
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $content, $image);
    $stmt->execute();
    $stmt->close();

    header("Location: feed.php");
    exit;
} else {
    header("Location: feed.php?error=empty");
    exit;
}
?>
