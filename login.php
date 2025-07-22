<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id,password,is_verified FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id,$hashed,$verified);
        $stmt->fetch();
        if (!$verified) {
            echo "<script>alert('Please verify your email first!'); window.location='register.php';</script>";
        } elseif (password_verify($password,$hashed)) {
            $_SESSION['user_id']=$id;
            $_SESSION['email']=$email;
            header("Location: dashboard.php"); // Cartridge tracker main page
            exit;
        } else {
            echo "<script>alert('Invalid password!');</script>";
        }
    } else {
        echo "<script>alert('Email not found!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="auth.css">
<title>Login</title></head>
<body>
<form method="POST">
	<div class="auth-container">
    <h2>Login</h2>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
    <p>Don't have an account? <a href="register.php">Register</a></p>
	</div>
</form>
</body>
</html>
