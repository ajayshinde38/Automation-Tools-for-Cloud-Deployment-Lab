<?php
session_start();
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}
include 'connection.php';

$q = mysqli_query($conn,"SELECT id FROM farmer_visits ORDER BY id");

$labels = [];
$data = [];
$i = 0;

while(mysqli_fetch_assoc($q)){
    $i++;
    $labels[] = $i;
    $data[] = $i;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Visitors Progress</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    font-family: Segoe UI;
    background:#f4f6f9;
    padding:30px;
}

/* 📐 Medium chart container */
.chart-box{
    width:650px;
    height:400px;
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 6px 15px rgba(0,0,0,.1);
}

canvas{
    width:100% !important;
    height:100% !important;
}
</style>
</head>

<body>

<h2>Visitors Growth Progress</h2>

<div class="chart-box">
    <canvas id="chart"></canvas>
</div>

<script>
new Chart(document.getElementById('chart'),{
    type:'line',
    data:{
        labels: <?= json_encode($labels) ?>,
        datasets:[{
            label:'Total Visitors',
            data: <?= json_encode($data) ?>,
            fill:true,
            borderColor:'#4CAF50',
            backgroundColor:'rgba(76,175,80,0.2)',
            tension:0.4
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        scales:{
            y:{ beginAtZero:true }
        }
    }
});
</script>

</body>
</html>
