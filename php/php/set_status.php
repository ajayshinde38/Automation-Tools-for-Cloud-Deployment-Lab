<?php
include 'connection.php';

if(isset($_GET['id']) && isset($_GET['status'])){

    $id = intval($_GET['id']);
    $status = $_GET['status'];

    mysqli_query($conn, "UPDATE complaints SET solve_status='$status' WHERE id=$id");

}

header("Location:complaints_list.php"); // change to your page name
exit();
?>
