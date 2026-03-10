<?php
include 'connection.php';
session_start();
if(isset($_POST['admin_log.php'])){
	
     $email = $_POST[' Admin email'];
	$email = mysqli_real_escape_string($conn, $Adminemail);
	$email = htmlentities($Adminemail);
	$password = $_POST['password'];
	$password = mysqli_real_escape_string($conn, $password);
	$password = htmlentities($password);
	
	$insert = "insert into admin_register(Admin email, password) VALUES ('$Adminemail' ,'$password')";
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