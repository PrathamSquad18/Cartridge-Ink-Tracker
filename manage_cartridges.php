<?php
session_start();
require 'config.php';
if(!isset($_SESSION['user_id'])){header("Location: login.php");exit;}
$user_id=$_SESSION['user_id'];

// Fetch cartridges
$stmt=$conn->prepare("SELECT * FROM cartridges WHERE user_id=?");
$stmt->bind_param("i",$user_id); $stmt->execute();
$res=$stmt->get_result();
$cartridges=$res->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Cartridges</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Your Cartridges</h1>
<a href="dashboard.php" class="btn">Back to Dashboard</a>
<table class="table">
<tr><th>Mode</th><th>Type</th><th>Ink (ml)</th><th>Actions</th></tr>
<?php foreach($cartridges as $c): ?>
<tr>
    <td><?=$c['mode']?></td>
    <td><?=$c['type']?></td>
    <td><?=$c['ml']?></td>
    <td>
        <a href="edit_cartridge.php?id=<?=$c['id']?>" class="btn">Edit</a>
        <a href="delete_cartridge.php?id=<?=$c['id']?>" class="btn danger" onclick="return confirm('Delete this cartridge?')">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
</div>
</body>
</html>
