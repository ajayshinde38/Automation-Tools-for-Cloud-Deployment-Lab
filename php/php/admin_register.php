<?php
include 'connection.php';
session_start();

if (isset($_POST['admin_register'])) {

    $name   = mysqli_real_escape_string($conn, $_POST['name']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);

    // Secure password hash
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if admin already exists
    $check = mysqli_query($conn, "SELECT id FROM admin WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Admin already registered!";
    } else {

        $insert = "INSERT INTO admin (name, email, password, mobile)
                   VALUES ('$name', '$email', '$password', '$mobile')";

        if (mysqli_query($conn, $insert)) {
            $_SESSION['admin_success'] = "Admin registered successfully!";
            header("Location: admin_login.php");
            exit();
        } else {
            $error = "Registration failed!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
    <style>
        body {
            font-family: Arial;
            background: linear-gradient(120deg, #1f4037, #99f2c8);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .box {
            background: white;
            padding: 30px;
            width: 350px;
            border-radius: 8px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        button {
            background: #1f4037;
            color: white;
            border: none;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Admin Registration</h2>

    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Admin Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="mobile" placeholder="Mobile Number">
        <button type="submit" name="admin_register">Register</button>

         <p style="text-align:center;margin-top:10px;">
        Already registered? <a href="admin_login.php">Login here</a>
    </p>
    </form>

    
</div>

</body>
</html>
