<?php
include 'connection.php';

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM map_feedback WHERE id = $id");

header("Location: all_random_sampling.php");
exit();
?>