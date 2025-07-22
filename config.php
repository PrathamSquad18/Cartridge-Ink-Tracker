<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

// === Database Connection (MySQL) ===
$host = "localhost";       // XAMPP default
$user = "root";            // XAMPP default
$pass = "";                // XAMPP default (no password)
$db   = "cartridge_tracker_db";  // Tumhara DB name (already created)

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

try {
    // === SMTP Settings ===
    $mail->isSMTP();
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'prathamshirsekar18@gmail.com';   // Tumhara Gmail
    $mail->Password = 'vqdk dbka jpmb cgdc';            // App password (already working)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Sender info (default from)
    $mail->setFrom('prathamshirsekar18@gmail.com', 'Cartridge Ink Tracker');
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}
?>
