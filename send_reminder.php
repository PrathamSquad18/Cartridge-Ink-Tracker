<?php
session_start();
require 'config.php';
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['email'];

// Fetch last print date
$stmt = $conn->prepare("SELECT MAX(date) as last_print FROM cartridge_logs WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$last_print = $result['last_print'];

if ($last_print) {
    $days_diff = (time() - strtotime($last_print)) / (60*60*24);
    if ($days_diff >= 7) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->SMTPOptions = array(
                'ssl'=>array('verify_peer'=>false,'verify_peer_name'=>false,'allow_self_signed'=>true)
            );
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'YOUR_GMAIL';
            $mail->Password = 'YOUR_APP_PASSWORD';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('YOUR_GMAIL', 'Cartridge Tracker');
            $mail->addAddress($user_email);
            $mail->Subject = "Reminder: Take a Test Print";
            $mail->Body = "It has been over 7 days since your last print. Please take a test print to avoid cartridge blockage.";
            $mail->send();
            echo "reminder sent";
        } catch (Exception $e) {
            echo "Mailer Error: ".$mail->ErrorInfo;
        }
    }
}
