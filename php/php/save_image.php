<?php
include 'connection.php';

$image = $_FILES['image']['name'];
$tmp = $_FILES['image']['tmp_name'];

$folder = "uploads/" . $image;

move_uploaded_file($tmp, $folder);

$query = "INSERT INTO complaints(image_path) VALUES ('$folder')";
mysqli_query($conn, $query);

header("Location: dashboard.php");
?>
