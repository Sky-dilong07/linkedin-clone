<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$userRes = $conn->query("SELECT * FROM users WHERE id=$user_id");
$user = $userRes->fetch_assoc();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET name='$name', email='$email', password='$hashed' WHERE id=$user_id";
    } else {
        $sql = "UPDATE users SET name='$name', email='$email' WHERE id=$user_id";
    }

    if ($conn->query($sql)) {
        $_SESSION['user_name'] = $name; // update session name
        $message = "✅ Profile updated successfully!";
    } else {
        $message = "❌ Error updating profile: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Profile | LinkedIn Clone</title>
<style>
body { font-family: Arial, sans-serif; background: #f3f3f3; margin:0; }
header { background:#0077b5; color:white; padding:15px; display:flex; justify-content:space-between; }
main { width:40%; margin:40px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 0 5px rgba(0,0,0,0.1); }
input,button { width:100%; padding:10px; margin:8px 0; border-radius:6px; border:1px solid #ccc; }
button { background:#0077b5; color:white; border:none; cursor:pointer; }
button:hover { background:#005f8f; }
.message { text-align:center; margin-bottom:10px; }
</style>
</head>
<body>
<header>
  <h2>Edit Profile</h2>
  <div>
    <a href="feed.php" style="color:white;">🏠 Feed</a> |
    <a href="logout.php" style="color:white;">Logout</a>
  </div>
</header>

<main>
  <h3>👤 Update Your Details</h3>
  <?php if($message) echo "<p class='message'>$message</p>"; ?>
  <form method="post">
    <label>Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    <label>New Password (leave blank to keep old)</label>
    <input type="password" name="password" placeholder="New password">
    <button type="submit">Save Changes</button>
  </form>
</main>
</body>
</html>
