<?php
include 'connection.php';
session_start();

if (isset($_POST['admin_login'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM admin WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {

        $admin = mysqli_fetch_assoc($result);

        if (password_verify($password, $admin['password'])) {

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];

            header("Location: dashboard.php");
            exit();

        } else {
            $error = "Wrong password!";
        }

    } else {
        $error = "Admin not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>

<style>


/* Reset */
*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

/* Header */
/* header {
    background: linear-gradient(to left, #1a8a27, #289c31);
    color: white;
    padding: 40px 60px;
}

header h1 {
    margin-bottom: 5px;
}

header p {
    font-size: 14px;
}

/* Navigation */
/* nav {
    margin-top: 15px;
    margin-right: 50px;
}

nav a {
    color: white;
    text-decoration: none;
    margin-right: 50px;
    font-weight: bold;
}

nav a:hover {
    text-decoration: underline;
} */ 

    header, footer { 
        background-color:#4CAF50; 
    color:black; 
    text-align:center;
     padding:15px; }

     nav {
    margin-top: 15px;
    margin-right: 50px;
}

        nav a  { 
        margin:0 15px; 
        color:white; 
        text-decoration:none; 
    }

/* Page Layout */
body{
    background: linear-gradient(to right, #2c8620, #219e21, #34af21);
}

/* Login Container */
.login-container{
    background: #fff;
    width: 350px;
    padding: 35px;
    border-radius: 10px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.4);
    margin: 40px auto;
}

.login-container h2{
    text-align: center;
    margin-bottom: 20px;
    color: #2c5364;
}

/* Input */
.input-group{
    margin-bottom: 15px;
}

.input-group label{
    font-size: 14px;
    color: #555;
}

.input-group input{
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.input-group input:focus{
    outline: none;
    border-color: #4efe6b;
}

/* Button */
.login-btn{
    width: 100%;
    padding: 10px;
    background: #469f3e;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.login-btn:hover{
    background: #358719;
}

/* Error */
.error{
    background: #ffe0e0;
    color: #b30000;
    padding: 8px;
    border-radius: 5px;
    margin-bottom: 10px;
    text-align: center;
}

/* Footer text */
.footer-text{
    text-align: center;
    margin-top: 15px;
    font-size: 13px;
    color: #777;
}

 
</style>
</head>

<body>
    <body>
<header>
    <h1>Admin Login</h1>
    
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

<div class="login-container">
    <h2>Admin Login</h2>

    <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="admin@gmail.com" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="********" required>
        </div>

        <button class="login-btn" type="submit" name="admin_login">
            Login
        </button>
    </form>

    <div class="footer-text">
        Authorized Admin Access Only
       

    </div>
</div>

</body>
</html>
