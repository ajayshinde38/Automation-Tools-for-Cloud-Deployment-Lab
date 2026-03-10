<?php
include 'connection.php';
session_start();
if(isset($_POST['user_reg.php'])){
	$name = $_POST['name'];
	$name = mysqli_real_escape_string($conn, $name);
	$name = htmlentities($name);
	$password = $_POST['password'];
	$password = mysqli_real_escape_string($conn, $password);
	$password = htmlentities($password);
	$mobile = $_POST['mobile'];
	$mobile = mysqli_real_escape_string($conn, $mobile);
	$mobile = htmlentities($mobile);
    $role = $_POST['role'];
	$role = mysqli_real_escape_string($conn, $role);
	$role = htmlentities($role);
	
	$insert = "insert into register(name, password, mobile,role) VALUES ('$name','$password','$mobile','$role')";
	// echo $insert;
	$insert_query = mysqli_query($conn, $insert);
	if($insert_query){
		$_SESSION['user'] = "New user added successfully.";
	}
	else{
		$_SESSION['usernot'] = "Error!! New user not added.";
	}
	header("Location: login.php");
}

?>