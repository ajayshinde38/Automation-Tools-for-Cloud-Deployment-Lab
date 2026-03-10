<?php
include 'connection.php';

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM complaints WHERE id=$id");

header("Location: complaints_list.php");
exit();
