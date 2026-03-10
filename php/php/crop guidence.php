<?php
$crops = [
    [
        "name" => "Sugarcane Cultivation",
        "soil" => "Deep rich loamy soil",
        "water" => "Regular irrigation",
        "fertilizer" => "Nitrogen and organic manure",
        "season" => "Annual crop",
        "image" => "images\SugarCane1.jpg"
    ],
    [
        "name" => "Maize Cultivation",
        "soil" => "Well-drained loamy soil",
        "water" => "Moderate irrigation",
        "fertilizer" => "Nitrogen and Phosphorus",
        "season" => "Kharif",
        "image" => "images\maize1.jpg"
    ],
    [
        "name" => "Jowar Cultivation",
        "soil" => "Black cotton soil",
        "water" => "Low to moderate water",
        "fertilizer" => "Organic manure",
        "season" => "Kharif & Rabi",
        "image" => "images\jowar1.jpg"
    ],
    [
        "name" => "Bajra Cultivation",
        "soil" => "Sandy loam soil",
        "water" => "Low water requirement",
        "fertilizer" => "Nitrogen",
        "season" => "Kharif",
        "image" => "images\bajara1.jpg"
    ],
    [
        "name" => "Rice Cultivation",
        "soil" => "Clayey loam soil",
        "water" => "High water requirement",
        "fertilizer" => "NPK fertilizers",
        "season" => "Kharif",
        "image" => "images\Rice1.jpg"
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Crop Guidance</title>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f2f2f2;
}

/* Header */
.header {
    background: linear-gradient(to right, #2e7d32, #66bb6a);
    color: white;
    padding: 30px;
    text-align: center;
}

.header h1 {
    margin: 0;
    font-size: 32px;
}

.header p {
    font-size: 14px;
}

/* Navbar */
.navbar {
    background: #1b5e20;
    padding: 10px;
    text-align: center;
}

.navbar a {
    color: white;
    margin: 0 15px;
    text-decoration: none;
    font-weight: bold;
}

.navbar a:hover {
    text-decoration: underline;
}

/* Content */
.content {
    padding: 25px;
}

.crop-box {
    background: white;
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 30px;
    width: 80%;
}

.crop-box h2 {
    color: #2e7d32;
}

.crop-box p {
    margin: 6px 0;
}
.crop-box img {
    width: 300px;
    margin-top: 10px;
    border-radius: 5px;
}

/* Footer */
.footer {
    background: #2e7d32;
    color: white;
    text-align: center;
    padding: 12px;
}
</style>
</head>

<body>

<div class="header">
    <h1>🌾 Crop Guidance</h1>
    <p>Best Practices for Healthy Crop Production</p>
</div>

<div class="navbar">
    <a href="home.php">Home</a>
    <a href="about.php">About</a>
    <a href="visit.php">Visits</a>
    <a href="complaints.php">Complaints</a>
    <a href="random_sampling.php">Random Sampling</a>
    <a href="crop guidence.php">Crop Guidance</a>
    <a href="contact.php">Contact</a>
    <a href="admin_login.php">Admin</a>
</div>

<div class="content">
<?php foreach ($crops as $crop) { ?>
    <div class="crop-box">
        <h2>🍃 <?php echo $crop["name"]; ?></h2>
        <p><strong>Soil:</strong> <?php echo $crop["soil"]; ?></p>
        <p><strong>Water:</strong> <?php echo $crop["water"]; ?></p>
        <p><strong>Fertilizer:</strong> <?php echo $crop["fertilizer"]; ?></p>
        <p><strong>Season:</strong> <?php echo $crop["season"]; ?></p>
        <img src="<?php echo $crop["image"]; ?>" alt="Crop Image">
    </div>
<?php } ?>
</div>

<div class="footer">
    © 2026 Farmers Visit Website
</div>

</body>
</html>