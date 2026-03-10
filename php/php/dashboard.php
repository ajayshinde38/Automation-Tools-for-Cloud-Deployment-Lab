<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'connection.php';

/* ===== COUNTS ===== */
$totalVisitors = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM farmer_visits")
)['total'];

$totalFarmers = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(id) AS total FROM register")
)['total'];

$totalComplaints = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM complaints")
)['total'];

$totalPending = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM complaints WHERE solve_status='Pending'")
)['total'];

$totalSolved = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM complaints WHERE solve_status='Solved'")
)['total'];

$totalRandomSampling = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM map_feedback")
)['total'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Segoe UI,sans-serif}
body{background:#f4f6f9}

.header{
background:#2c5364;color:white;
padding:15px 25px;
display:flex;
justify-content:space-between
}
.header a{
background:#c0392b;
color:white;
padding:8px 15px;
border-radius:5px;
text-decoration:none
}

.container{display:flex}

.sidebar{
width:220px;
background:#203a43;
min-height:100vh;
padding-top:20px
}
.sidebar a{
display:block;
color:white;
padding:12px 20px;
text-decoration:none
}
.sidebar a:hover{background:#2c5364}

.content{flex:1;padding:25px}

.cards{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px
}

.card{
background:#4CAF50;
color:white;
padding:25px;
border-radius:10px;
box-shadow:0 8px 15px rgba(0,0,0,0.1);
transition:.3s;
cursor:pointer
}
.card:hover{transform:scale(1.05)}

.card h3{font-weight:normal}
.card p{
font-size:30px;
font-weight:bold;
margin-top:10px
}

.card-link{
text-decoration:none;
color:inherit
}

.footer{
text-align:center;
margin-top:30px;
color:#888
}
</style>
</head>

<body>

<div class="header">
<h2>Admin Dashboard</h2>
<a href="Home.php">Logout</a>
</div>

<div class="container">

<!-- ===== SIDEBAR UPDATED ===== -->
<div class="sidebar">
<a href="admin_dashboard.php">Dashboard</a>
<a href="view_visitors.php">Visitors</a>
<a href="view_farmers.php">Farmers</a>
<a href="complaints_list.php">All Complaints</a>

<!-- NEW LINKS -->
<a href="pending_complaints.php">Pending Complaints</a>
<a href="solved_complaints.php">Solved Complaints</a>
<a href="random_sampling_list.php"> Random Sampling </a></div>

<div class="content">

<h2>Welcome, <?= $_SESSION['admin_name']; ?> 👋</h2>
<p style="color:#555;">System Overview</p>
<br>

<div class="cards">

<a href="view_visitors.php" class="card-link">
<div class="card">
<h3>Farm Visitors</h3>
<p><?= $totalVisitors ?></p>
</div>
</a>

<a href="view_farmers.php" class="card-link">
<div class="card">
<h3>Registered Farmers</h3>
<p><?= $totalFarmers ?></p>
</div>
</a>

<a href="complaints_list.php" class="card-link">
<div class="card">
<h3>Total Complaints</h3>
<p><?= $totalComplaints ?></p>
</div>
</a>

<a href="pending_complaints.php" class="card-link">
<div class="card">
<h3>Pending Complaints</h3>
<p><?= $totalPending ?></p>
</div>
</a>

<a href="solved_complaints.php" class="card-link">
<div class="card">
<h3>Solved Complaints</h3>
<p><?= $totalSolved ?></p>
</div>
</a>

<a href="random_sampling_list.php" class="card-link">
<div class="card">
<h3>Random Sampling</h3>
<p><?= $totalRandomSampling ?></p>
</div>
</a>

</div>

<div class="footer">
© <?= date("Y") ?> Admin Panel | Agriculture Support System
</div>

</div>
</div>

</body>
</html>
