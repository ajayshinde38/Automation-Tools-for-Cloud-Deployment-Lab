<?php
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    
    $sql = "INSERT INTO contacts (name, email, message) VALUES ('$name','$email','$message')";
    if($conn->query($sql) === TRUE){
        $success = "Message sent successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact - Farmers Visit</title>
    <style>
        body { font-family:Arial; background:#f0f0f0; margin:0; }
        header, footer { background:#4CAF50; color:white; text-align:center; padding:15px; }
        nav a { margin:0 15px; color:white; text-decoration:none; }
        .form-container { background:white; max-width:500px; margin:30px auto; padding:20px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.2);}
        input, textarea { width:100%; padding:10px; margin:10px 0; border-radius:5px; border:1px solid #ccc; }
        input[type=submit] { background:#4CAF50; color:white; border:none; cursor:pointer; }
        input[type=submit]:hover { background:#45a049; }
    </style>
</head>
<body>
<header>
    <h1>Contact Us</h1>
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
<div class="form-container">
    <?php if(isset($success)) { echo "<p style='color:green;'>$success</p>"; } ?>
    <?php if(isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <form method="post">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" placeholder="Your Message" required></textarea>
        <input type="submit" name="submit" value="Send Message">
    </form>
</div>
<footer>
    &copy; 2026 Farmers Visit
</footer>
</body>
</html>
