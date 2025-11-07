<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
require 'config/db.php';

// Fetch posts with author info (latest first)
$sql = "SELECT posts.id, posts.user_id, posts.content, posts.image, posts.created_at, users.name 
        FROM posts 
        JOIN users ON posts.user_id = users.id
        ORDER BY posts.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Feed | LinkedIn Clone</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="header">
  <h2>Feed</h2>
  <div>
    Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?> |
    <a href="profile.php?user_id=<?= $_SESSION['user_id'] ?>">👤 My Profile</a> |
    <a href="logout.php">Logout</a>
  </div>
</header>

<main>
  <!-- Create Post Form -->
  <form action="create_post.php" method="post" enctype="multipart/form-data" class="create-post">
    <textarea name="content" placeholder="What's on your mind?" required></textarea><br>
    <input type="file" name="image"><br>
    <button type="submit">Post</button>
  </form>

  <div class="posts">
  <h3>Recent Posts</h3>
<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $post_id = $row['id'];

        // Like counts and status
        $countRes = $conn->query("SELECT COUNT(*) AS total FROM likes WHERE post_id = $post_id");
        $likeCount = $countRes->fetch_assoc()['total'];
        $liked = $conn->query("SELECT * FROM likes WHERE user_id=" . $_SESSION['user_id'] . " AND post_id=$post_id")->num_rows > 0;
        $likeText = $liked ? "💔 Unlike" : "❤️ Like";

        echo "<div class='post' data-id='$post_id'>";
        echo "<h4><a href='profile.php?user_id=" . $row['user_id'] . "'>" . htmlspecialchars($row['name']) . "</a></h4>";
        echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
        if (!empty($row['image'])) echo "<img src='" . htmlspecialchars($row['image']) . "' width='300'><br>";
        echo "<small>Posted on " . $row['created_at'] . "</small><br>";

        // ❤️ Like system
        echo "<button class='like-btn' data-id='$post_id'>$likeText</button> ";
        echo "<span class='like-count' id='like-count-$post_id'>$likeCount Likes</span>";

        // 💬 Comment section
        echo "<div class='comment-box'>
                <div id='comments-$post_id'></div>
                <textarea class='comment-input' placeholder='Write a comment...'></textarea><br>
                <button class='add-comment' data-id='$post_id'>💬 Comment</button>
              </div>";

        // ✏ Edit / 🗑 Delete
        if ($row['user_id'] == $_SESSION['user_id']) {
            echo "<div class='actions'>
                    <a href='edit_post.php?id=$post_id'>✏ Edit</a>
                    <a href='delete_post.php?id=$post_id' onclick='return confirm(\"Delete post?\");'>🗑 Delete</a>
                  </div>";
        }
        echo "</div><hr>";
    }
} else {
    echo "<p>No posts yet. Be the first to post!</p>";
}
?>
  </div>
</main>

<script>
// === Like ===
document.querySelectorAll(".like-btn").forEach(btn=>{
 btn.addEventListener("click",()=>{
   const id=btn.dataset.id;
   fetch("like_post.php",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:"post_id="+id})
   .then(r=>r.text())
   .then(res=>{
     btn.textContent=res==="liked"?"💔 Unlike":"❤️ Like";
     fetch("like_count.php?post_id="+id)
       .then(r=>r.text())
       .then(c=>document.getElementById("like-count-"+id).textContent=c+" Likes");
   });
 });
});

// === Load comments ===
document.querySelectorAll(".post").forEach(p=>{
  const id=p.dataset.id;
  fetch("fetch_comments.php?post_id="+id)
    .then(r=>r.text())
    .then(html=>document.getElementById("comments-"+id).innerHTML=html);
});

// === Add comment ===
document.querySelectorAll(".add-comment").forEach(btn=>{
 btn.addEventListener("click",()=>{
   const id=btn.dataset.id;
   const input=btn.parentElement.querySelector(".comment-input");
   const txt=input.value.trim();
   if(!txt) return;
   fetch("add_comment.php",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:"post_id="+id+"&comment="+encodeURIComponent(txt)})
   .then(r=>r.text())
   .then(()=>{
      input.value="";
      fetch("fetch_comments.php?post_id="+id)
        .then(r=>r.text())
        .then(html=>document.getElementById("comments-"+id).innerHTML=html);
   });
 });
});
</script>
</body>
</html>
