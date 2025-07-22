<?php
session_start();
require 'config.php';
if(!isset($_SESSION['user_id'])){header("Location: login.php");exit;}

if(!isset($_GET['id'])) die("No ID");
$id=$_GET['id'];

$stmt=$conn->prepare("DELETE FROM logs WHERE id=?");
$stmt->bind_param("i",$id); $stmt->execute();

header("Location: manage_logs.php");
