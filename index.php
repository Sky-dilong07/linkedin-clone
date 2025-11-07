<?php
require 'config/db.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "⚠️ Both fields are required!";
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Correct password → log user in
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header("Location: feed.php");
                exit;
            } else {
                $message = "❌ Invalid password.";
            }
        } else {
            $message = "❌ No account found with that email.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | LinkedIn Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>🔐 Login</h2>

        <?php if ($message): ?>
            <p style="color:red;"><?= $message ?></p>
        <?php elseif (isset($_GET['registered'])): ?>
            <p style="color:green;">✅ Registration successful! You can now log in.</p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>

        <p>Don’t have an account? <a href="signup.php">Sign up here</a></p>
    </div>
</body>
</html>
