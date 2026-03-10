<?php
// index.php – Farmers Visit Website Home Page
include 'connection.php';
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farmers Visit Website</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f4;
        }

        /* Header */
        header {
            background: linear-gradient(to left, #1b5e20, #4caf50);
            color: white;
            padding: 15px;
        }

        header h1 {
            margin: 0;
        }

        header p {
            margin-top: 5px;
            font-size: 14px;
        }

        /* Navigation */
        nav {
            margin-top: 15px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        /* Content */
        .container {
            padding: 30px;
        }

        .card {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #4caf50;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .card h2 {
            color: #2e7d32;
            margin-top: 0;
        }

        /* Footer */
        footer {
            background-color: #1b5e20;
            color: white;
            text-align: center;
            padding: 10px;
           
            bottom: 0;
            width: 200px00%;
            font-size: 14px;
        }
    </style>
</head>
<body>

<header>
    <h1>Farmers Visit Website</h1>
    <p>Connecting Farmers with Knowledge & Opportunities</p>

    <nav>
<a href="Home.php">Home</a>
<a href="about.php">About</a>
<a href="visit.php">Visits</a>
<a href="complaints.php">Complaints</a>
<a href="random_sampling.php">Random Sampling</a>

<a href="crop guidence.php">Crop Guidance</a>
<a href="Contact.php">Contact</a>
<a href="admin_login.php">Admin</a>
</nav>
</header>

<div class="container">

    <div class="card">
        <h2>Welcome Farmers</h2>
        <p>
            This website provides farming guidance, visit programs,
            training support and government scheme information.
        </p>
    </div>

    <div class="card">
        <h2>Field Visits & Training</h2>
        <p>
            Farmers can participate in agriculture awareness programs
            and field demonstrations.
        </p>
    </div>

    <div class="card">
        <h2>Modern Farming Tips</h2>
        <p>
            Learn about crop production, fertilizers, irrigation
            and organic farming.
        </p>
    </div>

</div>

<footer>
    © 2026 Farmers Visit Website 
</footer>

</body>
</html>