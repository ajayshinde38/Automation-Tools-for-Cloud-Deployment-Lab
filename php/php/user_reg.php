<?php
include 'connection.php';
session_start();

if (isset($_POST['register'])) {

    $name   = mysqli_real_escape_string($conn, $_POST['name']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $role   = mysqli_real_escape_string($conn, $_POST['role']);

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Prevent duplicate email
    $check = mysqli_query($conn, "SELECT id FROM register WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "Email already registered!";
    } else {

        $insert = "INSERT INTO register (name, email, password, mobile, role)
                   VALUES ('$name', '$email', '$password', '$mobile', '$role')";

        if (mysqli_query($conn, $insert)) {

            // AUTO LOGIN
            $user_id = mysqli_insert_id($conn);

            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['role'] = $role;

            // 🔀 ROLE BASED REDIRECT
            if ($role == "Admin") {
                header("Location: dashboard.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            $msg = "Registration failed!";
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<style>
body{
    background:#4caf50;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    font-family:Arial;
}
.box{
    background:white;
    padding:30px;
    border-radius:10px;
    width:320px;
}
input,select,button{
    width:100%;
    padding:10px;
    margin:8px 0;
}
button{
    background:#2e7d32;
    color:white;
    border:none;
    cursor:pointer;
}
.msg{color:red;text-align:center;}
</style>
</head>
<body>

<div class="box">
<h2>Register</h2>

<?php if(isset($msg)) echo "<p class='msg'>$msg</p>"; ?>

<form method="post">

<input type="text" name="name" placeholder="Full Name" required>

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Password" required>

<input type="text" name="mobile" placeholder="Mobile" required>

<select name="role" required>
<option value="">Select Role</option>
<option value="Farmer">Farmer</option>
<option value="Adminr">Admin</option>
<option value="Officer">Officer</option>
<option value="Other">Other</option>
</select>

<button name="register">Register</button>

<p style="text-align:center;margin-top:10px;">
Already registered? <a href="login.php">Login</a>
</p>

</form>
</div>
</body>
</html>
