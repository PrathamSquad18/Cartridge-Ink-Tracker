<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) die("Not logged in");
$user_id = $_SESSION['user_id'];

$date=$_POST['date'];
$pages=intval($_POST['pages']);
$usage=$_POST['usage_type'];
$cid=intval($_POST['cartridge_id']);

// Validate cartridge ownership
$check=$conn->prepare("SELECT id FROM cartridges WHERE id=? AND user_id=?");
$check->bind_param("ii",$cid,$user_id); $check->execute(); $check->store_result();
if($check->num_rows==0){ echo "<script>alert('Refill cartridge first!');window.location='dashboard.php';</script>"; exit; }

// Add log
$ins=$conn->prepare("INSERT INTO logs(cartridge_id,date,pages,usage_type) VALUES(?,?,?,?)");
$ins->bind_param("isis",$cid,$date,$pages,$usage);
$ins->execute();

header("Location: dashboard.php");
