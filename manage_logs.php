<?php
session_start();
require 'config.php';
if(!isset($_SESSION['user_id'])){header("Location: login.php");exit;}
$user_id=$_SESSION['user_id'];

$stmt=$conn->prepare("SELECT logs.*,cartridges.mode,cartridges.type FROM logs 
    JOIN cartridges ON logs.cartridge_id=cartridges.id 
    WHERE cartridges.user_id=? ORDER BY date DESC");
$stmt->bind_param("i",$user_id); $stmt->execute();
$res=$stmt->get_result(); $logs=$res->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Logs</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Your Logs</h1>
<a href="dashboard.php" class="btn">Back to Dashboard</a>
<table class="table">
<tr><th>Date</th><th>Cartridge</th><th>Pages</th><th>Usage</th><th>Actions</th></tr>
<?php foreach($logs as $log): ?>
<tr>
    <td><?=$log['date']?></td>
    <td><?=$log['mode']?>-<?=$log['type']?></td>
    <td><?=$log['pages']?></td>
    <td><?=$log['usage_type']?></td>
    <td>
        <a href="edit_log.php?id=<?=$log['id']?>" class="btn">Edit</a>
        <a href="delete_log.php?id=<?=$log['id']?>" class="btn danger" onclick="return confirm('Delete this log?')">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
</div>
</body>
</html>
