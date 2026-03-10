<?php
include 'connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = $_POST["password"];

    // CHECK USER FROM REGISTER TABLE
    $sql = "SELECT * FROM register WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

        $row = mysqli_fetch_assoc($result);

        // VERIFY PASSWORD
        if (password_verify($password, $row['password'])) {

            // CREATE SESSION
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['role']     = $row['role'];

            // ✅ INSERT LOGIN HISTORY (NO DUPLICATE ISSUE)
            $stmt = $conn->prepare("INSERT INTO user_login (email) VALUES (?)");
            $stmt->bind_param("s", $email);
            $stmt->execute();

            // REDIRECT BASED ON ROLE
            if ($row['role'] == "Admin") {
                header("Location: dashboard.php");
            } else {
                header("Location: home.php");
            }
            exit();

        } else {
            $msg = "Wrong password!";
        }

    } else {
        $msg = "Email not registered!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
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
    width:300px;
    text-align:center;
}
input{
    width:100%;
    padding:10px;
    margin:8px 0;
}
button{
    width:100%;
    padding:10px;
    background:#2e7d32;
    color:white;
    border:none;
    cursor:pointer;
}
button:hover{
    background:#1b5e20;
}
.msg{
    color:red;
    margin-bottom:10px;
}
</style>
</head>
<body>

<div class="box">
<h2>Login</h2>

<?php if(isset($msg)) echo "<p class='msg'>$msg</p>"; ?>

<form method="post">

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit">Login</button>

<p style="margin-top:10px;">
Create a new account <a href="user_reg.php">Register Here</a>
</p>

</form>
</div>

</body>
</html>
