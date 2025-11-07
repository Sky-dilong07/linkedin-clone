<?php
require 'config/db.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($name) || empty($email) || empty($password)) {
        $message = "⚠️ All fields are required!";
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "❌ Email already registered!";
        } else {
            // Hash password
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed);

            if ($stmt->execute()) {
                header("Location: index.php?registered=1");
                exit;
            } else {
                $message = "❌ Something went wrong. Try again.";
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | LinkedIn Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>📝 Create Account</h2>

        <?php if ($message): ?>
            <p style="color:red;"><?= $message ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="name" placeholder="Full Name" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Sign Up</button>
        </form>

        <p>Already have an account? <a href="index.php">Login here</a></p>
    </div>
</body>
</html>
