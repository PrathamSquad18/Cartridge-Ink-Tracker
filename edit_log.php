<?php
session_start();
require 'config.php';
if(!isset($_SESSION['user_id'])){header("Location: login.php");exit;}
$user_id=$_SESSION['user_id'];

if(!isset($_GET['id'])) die("No Log ID");
$id=$_GET['id'];

if($_SERVER['REQUEST_METHOD']==='POST'){
    $date=$_POST['date']; $pages=$_POST['pages']; $usage=$_POST['usage_type'];
    $stmt=$conn->prepare("UPDATE logs SET date=?,pages=?,usage_type=? WHERE id=?");
    $stmt->bind_param("sisi",$date,$pages,$usage,$id);
    $stmt->execute();
    header("Location: manage_logs.php");
}

$stmt=$conn->prepare("SELECT * FROM logs WHERE id=?");
$stmt->bind_param("i",$id); $stmt->execute();
$res=$stmt->get_result(); $log=$res->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Log</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Edit Log</h1>
<form method="POST">
    <input type="date" name="date" value="<?=$log['date']?>" required>
    <input type="number" name="pages" value="<?=$log['pages']?>" required>
    <select name="usage_type">
        <option value="light" <?=$log['usage_type']=='light'?'selected':''?>>Light</option>
        <option value="medium" <?=$log['usage_type']=='medium'?'selected':''?>>Medium</option>
        <option value="heavy" <?=$log['usage_type']=='heavy'?'selected':''?>>Heavy</option>
    </select>
    <button type="submit">Save</button>
</form>
<a href="manage_logs.php" class="btn">Cancel</a>
</div>
</body>
</html>
