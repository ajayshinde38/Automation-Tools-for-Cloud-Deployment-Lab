<?php
session_start();
include "connection.php";

if (isset($_POST['submit'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $email = htmlentities($email);

    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $password = htmlentities($password);

    // Admin login using email
    $sql = "SELECT adminid, email, password FROM admin WHERE email='$email' AND password='$password'";
    $query = mysqli_query($conn, $sql);

    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);

        $_SESSION['admin_login'] = true;
        $_SESSION['admin_id'] = $row['adminid'];
        $_SESSION['admin_email'] = $row['email'];

        header("Location: admin_dashboard.php");
        exit();
    } else {
        $_SESSION['loginmsg'] = "Invalid Email or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Visitor Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background-color: #F3EBF6;
            font-family: 'Ubuntu', sans-serif;
            background-image: url('img/exam_desk.jpg');
            background-size: cover;
        }

        .main {
            background-color: #FFFFFF;
            width: 400px;
            height: 420px;
            margin: 5em auto;
            border-radius: 1.5em;
            box-shadow: 0px 11px 35px 2px rgba(0, 0, 0, 0.14);
        }

        .sign {
            padding-top: 40px;
            color: #8C55AA;
            font-weight: bold;
            font-size: 23px;
            text-align: center;
        }

        .input {
            width: 76%;
            font-size: 14px;
            padding: 10px 20px;
            border-radius: 20px;
            margin: 20px auto;
            display: block;
            border: 2px solid rgba(0,0,0,0.02);
            text-align: center;
        }

        .submit {
            cursor: pointer;
            border-radius: 5em;
            color: #fff;
            background: linear-gradient(to right, #9C27B0, #E040FB);
            border: 0;
            padding: 10px 40px;
            margin: 10px auto;
            display: block;
        }

        .loginmsg {
            text-align: center;
            color: red;
            font-family: Georgia, serif;
        }

        .role-msg {
            text-align: center;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

<div class="main">
    <p class="sign">VISITOR ADMIN LOGIN</p>

    <p class="loginmsg">
        <?php
        if (isset($_SESSION['loginmsg'])) {
            echo $_SESSION['loginmsg'];
            unset($_SESSION['loginmsg']);
        }
        ?>
    </p>

    <form method="post">
        <input class="input" type="email" name="email" placeholder="Admin Email ID" required>
        <input class="input" type="password" name="password" placeholder="Password" required>
        <button class="submit" type="submit" name="submit">LOGIN</button>
    </form>

    <p class="role-msg">Create new admin? <a href="registration_admin.php">Register Here</a></p>
</div>

</body>
</html>
