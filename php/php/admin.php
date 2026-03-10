<?php
include 'connection.php';
session_start();
if(isset($_POST['addclass'])){
	$name = $_POST['name'];
	$name = mysqli_real_escape_string($conn, $name);
	$name = htmlentities($name);
	$email = $_POST['email'];
	$email = mysqli_real_escape_string($conn, $email);
	$email = htmlentities($email);
	$password = $_POST['password'];
	$password = mysqli_real_escape_string($conn, $password);
	$password = htmlentities($password);
    
	
	$insert = "insert into class(year, dept, division) VALUES ('$name','$email','$password')";
	// echo $insert;
	$insert_query = mysqli_query($conn, $insert);
	if($insert_query){
		$_SESSION['class'] = "New class added successfully.";
	}
	else{
		$_SESSION['classnot'] = "Error!! New class not added.";
	}
	header("Location: visit.php");
}

?>