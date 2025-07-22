<?php
session_start();
require 'config.php';
if(!isset($_SESSION['user_id'])){header("Location: login.php");exit;}
$user_id=$_SESSION['user_id'];

if(!isset($_GET['id'])) die("No ID");
$id=$_GET['id'];

$stmt=$conn->prepare("DELETE FROM cartridges WHERE id=? AND user_id=?");
$stmt->bind_param("ii",$id,$user_id); $stmt->execute();

header("Location: manage_cartridges.php");
