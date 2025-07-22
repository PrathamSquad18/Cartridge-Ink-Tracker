<?php
session_start();
require 'config.php';
if(!isset($_SESSION['user_id'])){header("Location: login.php");exit;}
$user_id=$_SESSION['user_id'];

if(!isset($_GET['id'])) die("No cartridge ID");
$id=$_GET['id'];

if($_SERVER['REQUEST_METHOD']==='POST'){
    $mode=$_POST['mode']; $type=$_POST['type']; $ml=$_POST['ml'];
    $stmt=$conn->prepare("UPDATE cartridges SET mode=?,type=?,ml=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssdii",$mode,$type,$ml,$id,$user_id);
    $stmt->execute();
    header("Location: manage_cartridges.php");
}

$stmt=$conn->prepare("SELECT * FROM cartridges WHERE id=? AND user_id=?");
$stmt->bind_param("ii",$id,$user_id); $stmt->execute();
$res=$stmt->get_result(); $cart=$res->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Cartridge</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Edit Cartridge</h1>
<form method="POST">
    <select name="mode">
        <option value="black" <?=$cart['mode']=='black'?'selected':''?>>Black</option>
        <option value="color" <?=$cart['mode']=='color'?'selected':''?>>Color</option>
    </select>
    <select name="type">
        <option value="small" <?=$cart['type']=='small'?'selected':''?>>Small</option>
        <option value="medium" <?=$cart['type']=='medium'?'selected':''?>>Medium</option>
        <option value="large" <?=$cart['type']=='large'?'selected':''?>>Large</option>
    </select>
    <input type="number" step="0.1" name="ml" value="<?=$cart['ml']?>" required>
    <button type="submit">Save</button>
</form>
<a href="manage_cartridges.php" class="btn">Cancel</a>
</div>
</body>
</html>
