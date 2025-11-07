<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();
}
header("Location: feed.php");
exit;
?>
