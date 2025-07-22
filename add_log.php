<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];
$mode = $_POST['mode'] ?? '';
$type = $_POST['type'] ?? '';
$date = $_POST['date'] ?? '';
$pages = intval($_POST['pages'] ?? 0);
$usage = $_POST['usage'] ?? '';
$ml = isset($_POST['ml']) && $_POST['ml'] !== '' ? floatval($_POST['ml']) : null;

if (!$mode || !$type || !$date || $pages <= 0 || !$usage) {
    http_response_code(400);
    exit('Invalid input');
}

if ($ml === null) {
    // Default ML based on cartridge size
    $defaults = ['small'=>5,'medium'=>10,'large'=>15];
    $ml = $defaults[$type] ?? 5;
}

// Insert into logs table
$stmt = $conn->prepare("INSERT INTO cartridge_logs (user_id, cartridge_mode, cartridge_type, date, pages, usage_type, ml_remaining) VALUES (?,?,?,?,?,?,?)");
$stmt->bind_param("isssisi", $user_id, $mode, $type, $date, $pages, $usage, $ml);

if ($stmt->execute()) {
    echo "success";
} else {
    http_response_code(500);
    echo "Database error";
}
