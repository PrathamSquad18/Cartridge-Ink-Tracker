<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$user_id=$_SESSION['user_id'];

$cartridges=[];
$stmt=$conn->prepare("SELECT * FROM cartridges WHERE user_id=?");
$stmt->bind_param("i",$user_id); $stmt->execute();
$res=$stmt->get_result();
while($row=$res->fetch_assoc()){
    $row['logs']=[];
    $logQ=$conn->prepare("SELECT * FROM logs WHERE cartridge_id=? ORDER BY date DESC");
    $logQ->bind_param("i",$row['id']); $logQ->execute();
    $logR=$logQ->get_result();
    while($l=$logR->fetch_assoc()) $row['logs'][]=$l;
    $cartridges[]=$row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Cartridge Dashboard</title>
<style>
/* Global */
body {
    margin:0;
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(120deg,#89f7fe,#66a6ff);
    min-height:100vh;
    display:flex; flex-direction:column;
}
.header-bar {
    display:flex; justify-content:space-between; align-items:center;
    background:rgba(44,62,80,0.8); color:#fff; padding:15px 30px;
    box-shadow:0 4px 15px rgba(0,0,0,0.2); backdrop-filter: blur(5px);
}
.header-title { font-size:26px; font-weight:bold; letter-spacing:1px; }
.logout-btn {
    background:#ff6b6b; border:none; color:#fff; padding:10px 18px;
    border-radius:25px; cursor:pointer; font-size:14px; font-weight:bold;
    box-shadow:0 2px 6px rgba(0,0,0,0.2); transition:all 0.3s ease;
}
.logout-btn:hover { background:#ff4b5c; transform:scale(1.05); }
.container { width:90%; margin:20px auto; flex:1; }
.forms { display:flex; flex-wrap:wrap; gap:20px; justify-content:center; }
.form-section {
    flex:1 1 45%; background:rgba(255,255,255,0.9); padding:20px; border-radius:12px;
    box-shadow:0 5px 20px rgba(0,0,0,0.15); backdrop-filter:blur(10px);
}
.form-section h3 { margin:0 0 10px; color:#2c3e50; font-size:18px; }
.form-section input, .form-section select, .form-section button {
    width:95%; margin-top:8px; padding:10px; font-size:14px; border-radius:6px; border:1px solid #ccc;
}
.form-section button {
    background:linear-gradient(45deg,#42e695,#3bb2b8); color:#fff; border:none; margin-top:12px;
    font-weight:bold; cursor:pointer; box-shadow:0 3px 8px rgba(0,0,0,0.2);
}
.form-section button:hover { background:linear-gradient(45deg,#38d39f,#2b9ea6); }
.summary-section {
    display:flex; flex-wrap:wrap; justify-content:center; margin-top:40px; gap:20px;
}
.card {
    width:320px; background:rgba(255,255,255,0.85); margin:12px; border-radius:14px;
    padding:22px; text-align:center;
    box-shadow:0 8px 25px rgba(0,0,0,0.15); backdrop-filter:blur(8px);
    transition:transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover { transform:translateY(-6px); box-shadow:0 12px 30px rgba(0,0,0,0.2); }
.card h3 { color:#34495e; margin-bottom:12px; }
.progress { background:#dfe6e9; border-radius:30px; overflow:hidden; margin:15px 0; height:28px; }
.progress-bar {
    height:28px; line-height:28px; color:#fff; text-align:center; font-size:14px;
    font-weight:bold; border-radius:30px;
    background:linear-gradient(90deg,#6a11cb,#2575fc);
}
.detail-text { font-size:14px; margin:5px 0; color:#2c3e50; }
.manage-btn {
    display:inline-block; margin-top:14px; text-align:center;
    background:linear-gradient(45deg,#6a11cb,#2575fc);
    color:#fff; text-decoration:none; padding:10px 18px;
    border-radius:30px; font-size:14px; font-weight:bold;
    box-shadow:0 3px 8px rgba(0,0,0,0.2); transition:0.3s;
}
.manage-btn:hover { background:linear-gradient(45deg,#8e2de2,#4a00e0); transform:scale(1.05); }
</style>
</head>
<body>
<div class="header-bar">
    <div class="header-title">Cartridge Tracker</div>
    <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
</div>

<div class="container">
    <div class="forms">
        <!-- Refill Form -->
        <form action="process_refill.php" method="POST" class="form-section">
            <h3>Refill Cartridge</h3>
            <select name="mode"><option value="black">Black</option><option value="color">Color</option></select>
            <select name="type"><option value="small">Small</option><option value="medium">Medium</option><option value="large">Large</option></select>
            <input type="number" step="0.1" name="ml" placeholder="Ink (ml)" required>
            <button type="submit">Refill</button>
        </form>

        <!-- Log Form -->
        <form action="process_log.php" method="POST" class="form-section">
            <h3>Add Log</h3>
            <input type="date" name="date" required>
            <input type="number" name="pages" placeholder="Pages" required>
            <select name="usage_type"><option value="light">Light</option><option value="medium">Medium</option><option value="heavy">Heavy</option></select>
            <select name="cartridge_id" required>
                <option value="">Select Cartridge</option>
                <?php foreach($cartridges as $c): ?>
                <option value="<?=$c['id']?>"><?=$c['mode']?> - <?=$c['type']?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Add Log</button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="summary-section">
    <?php foreach($cartridges as $c):
        $base = ($c['type']=='small'?150:($c['type']=='medium'?300:450));
        $usedPages=0; $lastDate="N/A"; $totalPages=0;
        foreach($c['logs'] as $log){
            $factor=($log['usage_type']=='light'?1:($log['usage_type']=='medium'?1.5:2));
            $usedPages += $log['pages']*$factor;
            $totalPages += $log['pages'];
            if ($lastDate=="N/A") $lastDate=$log['date'];
        }
        $remaining=max(0,$base-$usedPages);
        $percent=($remaining/$base)*100;
        $colorGradient = ($percent>60)?'linear-gradient(90deg,#11998e,#38ef7d)':($percent>30?'linear-gradient(90deg,#f7971e,#ffd200)':'linear-gradient(90deg,#cb2d3e,#ef473a)');

        $inkFilled=$c['ml'];
        $inkRemaining = ($inkFilled * $percent)/100;

        $daysSince = ($lastDate!="N/A") ? (floor((time()-strtotime($lastDate))/86400)) : "N/A";
    ?>
    <div class="card">
        <h3><?=$c['mode']?> - <?=$c['type']?></h3>
        <div class="progress">
            <div class="progress-bar" style="width:<?=$percent?>%;background:<?=$colorGradient?>;">
                <?=number_format($inkRemaining,1)?> ml left
            </div>
        </div>
        <div class="detail-text">Ink Filled: <?=number_format($inkFilled,1)?> ml</div>
        <div class="detail-text">Pages Remaining: <?=$remaining?></div>
        <div class="detail-text">Total Pages Printed: <?=$totalPages?></div>
        <div class="detail-text">Last Printed: <?=$lastDate?></div>
        <div class="detail-text">Days Since Last Print: <?=$daysSince?></div>
        <a href="manage_cartridges.php?id=<?=$c['id']?>" class="manage-btn">Manage Cartridge</a>
		<a href="manage_logs.php?id=<?=$c['id']?>" class="manage-btn">Manage logs</a>
    </div>
    <?php endforeach; ?>
    </div>
</div>
</body>
</html>
