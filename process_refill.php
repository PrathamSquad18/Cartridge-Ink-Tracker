<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) die("Not logged in");
$user_id = $_SESSION['user_id'];

$mode = $_POST['mode'];
$type = $_POST['type'];
$ml = floatval($_POST['ml']);

// Replace or Insert cartridge
$stmt = $conn->prepare("SELECT id FROM cartridges WHERE user_id=? AND mode=? AND type=?");
$stmt->bind_param("iss",$user_id,$mode,$type);
$stmt->execute(); $stmt->store_result();
if($stmt->num_rows>0){
    $stmt->bind_result($cid); $stmt->fetch();
    $up=$conn->prepare("UPDATE cartridges SET ml=? WHERE id=?");
    $up->bind_param("di",$ml,$cid); $up->execute();
} else {
    $ins=$conn->prepare("INSERT INTO cartridges(user_id,mode,type,ml) VALUES(?,?,?,?)");
    $ins->bind_param("issd",$user_id,$mode,$type,$ml); $ins->execute();
}
header("Location: dashboard.php");
