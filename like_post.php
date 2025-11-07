<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    exit("Not logged in");
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;

if (!$post_id) exit("Invalid post");

// Check if already liked
$stmt = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Unlike
    $conn->query("DELETE FROM likes WHERE user_id=$user_id AND post_id=$post_id");
    echo "unliked";
} else {
    // Like
    $stmt = $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    echo "liked";
}
