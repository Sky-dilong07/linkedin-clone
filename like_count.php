<?php
require 'config/db.php';
$post_id = $_GET['post_id'] ?? 0;
$result = $conn->query("SELECT COUNT(*) AS total FROM likes WHERE post_id = $post_id");
echo $result->fetch_assoc()['total'];
?>
