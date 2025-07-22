<?php
session_start();
require 'config.php';

if (!isset($_SESSION['pending_email'])) {
    header("Location: register.php");
    exit;
}
$email = $_SESSION['pending_email'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $otp = $_POST['otp'];
    $stmt = $conn->prepare("SELECT id, otp_expires FROM users WHERE email=? AND otp=?");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id,$expiry);
        $stmt->fetch();
        if (strtotime($expiry) < time()) {
            echo "<script>alert('OTP expired!'); window.location='register.php';</script>";
        } else {
            $conn->query("UPDATE users SET is_verified=1, otp=NULL, otp_expires=NULL WHERE id=$id");
            unset($_SESSION['pending_email']);
            echo "<script>alert('Email verified! You can now login.'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid OTP!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Verify OTP</title>
<link rel="stylesheet" href="auth.css">
</head>
<body>
<form method="POST">
	<div class="auth-container">
    <h2>Verify Email</h2>
    <p>Enter the OTP sent to <strong><?php echo $email; ?></strong></p>
    <input type="text" name="otp" placeholder="6-digit OTP" required>
    <button type="submit">Verify</button>
	</div>
</form>
</body>
</html>
