<?php
session_start();
require 'config/db.php';

if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    header("Location: feed.php");
    exit;
}

$user_id = intval($_GET['user_id']);

// Fetch user info
$userRes = $conn->query("SELECT name, email, created_at FROM users WHERE id = $user_id");
if ($userRes->num_rows === 0) {
    echo "User not found.";
    exit;
}
$user = $userRes->fetch_assoc();

// Count stats
$postCountRes = $conn->query("SELECT COUNT(*) AS total FROM posts WHERE user_id = $user_id");
$postCount = $postCountRes->fetch_assoc()['total'];
$likeCountRes = $conn->query("SELECT COUNT(*) AS total FROM likes WHERE user_id = $user_id");
$likeCount = $likeCountRes->fetch_assoc()['total'];
$commentCountRes = $conn->query("SELECT COUNT(*) AS total FROM comments WHERE user_id = $user_id");
$commentCount = $commentCountRes->fetch_assoc()['total'];

// Fetch user's posts
$postRes = $conn->query("SELECT id, content, image, created_at FROM posts WHERE user_id = $user_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($user['name']) ?> | Profile</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="header">
  <h2><?= htmlspecialchars($user['name']) ?>'s Profile</h2>
  <div>
    <a href="feed.php">🏠 Feed</a> |
    <a href="edit_profile.php">✏ Edit Profile</a> |
    <a href="logout.php">Logout</a>
  </div>
</header>

<div class="center">
  <h3>👤 <?= htmlspecialchars($user['name']) ?></h3>
  <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
  <p><strong>Joined:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
</div>

<div class="stats">
  <div class="stat-box">
    📜<br><span><?= $postCount ?></span><br>Posts
  </div>
  <div class="stat-box">
    ❤️<br><span><?= $likeCount ?></span><br>Likes Given
  </div>
  <div class="stat-box">
    💬<br><span><?= $commentCount ?></span><br>Comments
  </div>
</div>

<div class="posts">
  <h3>📝 Recent Posts</h3>
  <?php
  if ($postRes->num_rows > 0) {
      while ($p = $postRes->fetch_assoc()) {
          echo "<div class='post'>";
          echo "<p>" . nl2br(htmlspecialchars($p['content'])) . "</p>";
          if (!empty($p['image'])) {
              echo "<img src='" . htmlspecialchars($p['image']) . "' alt='Post image'>";
          }
          echo "<small>📅 Posted on " . $p['created_at'] . "</small>";
          echo "</div>";
      }
  } else {
      echo "<p>No posts yet.</p>";
  }
  ?>
</div>
</body>
</html>
