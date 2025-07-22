<?php
session_start();
require 'config.php'; // SMTP + DB config

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.location='register.php';</script>";
        exit;
    }

    // Generate OTP
    $otp = rand(100000,999999);
    $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

    // Insert new user (unverified)
    $stmt = $conn->prepare("INSERT INTO users (email,password,otp,otp_expires) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $email, $password, $otp, $expiry);
    $stmt->execute();

    // Send OTP email
    try {
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = "Your OTP Verification Code";
        $mail->Body = "<h2>Welcome to Cartridge Tracker</h2>
                       <p>Your OTP is <strong>$otp</strong>. Valid for 10 minutes.</p>";
        $mail->send();
        $_SESSION['pending_email'] = $email;
        echo "<script>alert('OTP sent to your email. Verify now!'); window.location='verify.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Failed to send OTP. Check SMTP settings.'); window.location='register.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="auth.css">
<title>Register</title>
</head>
<body>
<form method="POST">
	<div class="auth-container">
    <h2>Create Account</h2>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
    <p>Already have an account? <a href="login.php">Login</a></p>
	</div>
</form>
</body>
</html>
