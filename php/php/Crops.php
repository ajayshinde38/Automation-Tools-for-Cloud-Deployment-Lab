<?php  ?>
<!DOCTYPE html>
<html>
<head>
    <title>Crops - Farmers Visit</title>
    <style>
        body { font-family:Arial; background:#f5f5f5; margin:0; }
        header, footer { background:#4CAF50; color:white; text-align:center; padding:15px; }
        nav a { margin:0 15px; color:white; text-decoration:none; }
        .crops-container { display:flex; flex-wrap:wrap; justify-content:center; margin:20px; }
        .crop { background:white; margin:10px; border-radius:10px; overflow:hidden; width:250px; box-shadow:0 0 10px rgba(0,0,0,0.2); }
        .crop img { width:100%; height:200px; object-fit:cover; }
        .crop h3 { text-align:center; padding:10px; }
    </style>
</head>
<body>
<header>
    <h1>Our Crops</h1>
    <body>
    <nav>
<a href="Home.php">Home</a>
<a href="about.php">About</a>
<a href="visit.php">Visits</a>
<a href="Crops.php">Crop </a>
<a href="crop guidence.php">Crop Guidance</a>
<a href="Contact.php">Contact</a>
</nav>
</header>
<div class="crops-container">
    <div class="crop">
        <img src="crop1.jpg" alt="Wheat">
        <h3>Wheat</h3>
    </div>
    <div class="crop">
        <img src="crop2.jpg" alt="Rice">
        <h3>Rice</h3>
    </div>
    <div class="crop">
        <img src="crop3.jpg" alt="Corn">
        <h3>Corn</h3>
    </div>
</div>
<footer>
    &copy; 2025 Farmers Visit
</footer>
</body>
</html>
