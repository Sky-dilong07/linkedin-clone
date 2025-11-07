<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: feed.php");
    exit;
}

// Fetch the post to edit
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Post not found or not yours!");
}

$post = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $content = trim($_POST['content']);
    if (!empty($content)) {
        $update = $conn->prepare("UPDATE posts SET content = ? WHERE id = ? AND user_id = ?");
        $update->bind_param("sii", $content, $id, $_SESSION['user_id']);
        $update->execute();
        header("Location: feed.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Post</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<h2>Edit Post</h2>
<form method="POST">
    <textarea name="content" required><?= htmlspecialchars($post['content']) ?></textarea><br>
    <button type="submit">Save Changes</button>
</form>
<a href="feed.php">Back to Feed</a>
</body>
</html>
