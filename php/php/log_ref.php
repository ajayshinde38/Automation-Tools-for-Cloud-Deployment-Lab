<?php
include 'connection.php';
session_start();
if(isset($_POST['user_login'])){
    $email = $_POST['email'];
	$email = mysqli_real_escape_string($conn, $email);
	$email = htmlentities($email);
	$password = $_POST['password'];
	$password = mysqli_real_escape_string($conn, $password);
	$password = htmlentities($password);
	
	$insert = "insert into user_login(email, password) VALUES ('$email','$password')";
	// echo $insert;
	$insert_query = mysqli_query($conn, $insert);
	if($insert_query){
		$_SESSION['user'] = "New user added successfully.";
	}
	else{
		$_SESSION['usernot'] = "Error!! New user not added.";
	}
	header("Location: Home.php");
}

?>