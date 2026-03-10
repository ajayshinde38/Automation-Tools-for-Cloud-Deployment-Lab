<?php
include 'connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $farmer_name        = mysqli_real_escape_string($conn, $_POST['farmer_name']);
    $complaint_date     = mysqli_real_escape_string($conn, $_POST['complaint_date']);
    $sugar_factory_name = mysqli_real_escape_string($conn, $_POST['sugar_factory_name']);
    $mobile             = mysqli_real_escape_string($conn, $_POST['mobile']);
    $district           = mysqli_real_escape_string($conn, $_POST['district']);
    $taluka             = mysqli_real_escape_string($conn, $_POST['taluka']);
    $village            = mysqli_real_escape_string($conn, $_POST['village']);
    $complaint_type     = mysqli_real_escape_string($conn, $_POST['complaint_type']);
    $complaint          = mysqli_real_escape_string($conn, $_POST['complaint']);

    /* -------- IMAGE UPLOAD -------- */
    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$image);
    }

    /* -------- VOICE UPLOAD -------- */
    $voice = "";
    if (!empty($_FILES['voice']['name'])) {
        $voice = $_FILES['voice']['name'];
        move_uploaded_file($_FILES['voice']['tmp_name'], "uploads/".$voice);
    }

    $solve_status = "Pending";

    $sql = "INSERT INTO complaints
    (farmer_name, complaint_date, sugar_factory_name, mobile, district, taluka, village,
     complaint_type, complaint, image, voice, solve_status)
    VALUES
    ('$farmer_name','$complaint_date','$sugar_factory_name','$mobile','$district',
     '$taluka','$village','$complaint_type','$complaint','$image','$voice','$solve_status')";

    if (mysqli_query($conn, $sql)) {
        echo "Complaint saved successfully";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
