<?php
include 'connection.php';
session_start();
if(isset($_POST['admin_reg.php'])){
	$name = $_POST['name'];
	$name = mysqli_real_escape_string($conn, $name);
	$name = htmlentities($name);
     $email = $_POST['email'];
	$email = mysqli_real_escape_string($conn, $email);
	$email = htmlentities($email);
	$password = $_POST['password'];
	$password = mysqli_real_escape_string($conn, $password);
	$password = htmlentities($password);
	$mobile = $_POST['mobile'];
	$mobile = mysqli_real_escape_string($conn, $mobile);
	$mobile = htmlentities($mobile);
    
	$insert = "insert into admin_register(name, email, password, mobile,) VALUES ('$name','$email' ,'$password','$mobile')";
	// echo $insert;
	$insert_query = mysqli_query($conn, $insert);
	if($insert_query){
		$_SESSION['admin'] = "New admin added successfully.";
	}
	else{
		$_SESSION['adminnot'] = "Error!! New admin not added.";
	}
	header("Location: login.php");
}

?>