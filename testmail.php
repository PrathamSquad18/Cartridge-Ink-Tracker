<?php
require 'config.php';

try {
    global $mail;

    $mail->addAddress('ps9137286102@gmail.com');  // test ke liye khudko bhejo
    $mail->Subject = 'Test Mail';
    $mail->Body    = 'SMTP Mail working successfully!';

    $mail->send();
    echo "Mail sent successfully!";
} catch (Exception $e) {
    echo "Message could not be sent. Error: {$mail->ErrorInfo}";
}
