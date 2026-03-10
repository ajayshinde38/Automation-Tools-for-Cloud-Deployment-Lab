<?php
include 'connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

$prn = mysqli_real_escape_string($conn, $_POST['prn']);
$visit_date = mysqli_real_escape_string($conn, $_POST['visit_date']);
$farmer_name = mysqli_real_escape_string($conn, $_POST['farmer_name']);
$district = mysqli_real_escape_string($conn, $_POST['district']);
$taluka = mysqli_real_escape_string($conn, $_POST['taluka']);
$village = mysqli_real_escape_string($conn, $_POST['village']);

$soil_condition = mysqli_real_escape_string($conn, $_POST['soil_condition']);

$soil_temp = mysqli_real_escape_string($conn, $_POST['soil_temp']);
$soil_moisture = mysqli_real_escape_string($conn, $_POST['soil_moisture']);
$irrigation = mysqli_real_escape_string($conn, $_POST['irrigation']);

$fertilizer = mysqli_real_escape_string($conn, $_POST['fertilizer']);
$deficiency = implode(',', $_POST['deficiency'] ?? []);

$pest_attack = mysqli_real_escape_string($conn, $_POST['pest_attack']);
$disease_symptoms = mysqli_real_escape_string($conn, $_POST['disease_symptoms']);

$krushik = mysqli_real_escape_string($conn, $_POST['krushik']);
$reason = mysqli_real_escape_string($conn, $_POST['reason']);


$disease_image = $_FILES['disease_image']['name'];
move_uploaded_file($_FILES['disease_image']['tmp_name'], "uploads/".$disease_image);

$spray = mysqli_real_escape_string($conn, $_POST['spray']);
$health = mysqli_real_escape_string($conn, $_POST['health']);

$germination = mysqli_real_escape_string($conn, $_POST['germination']);
$tillers = mysqli_real_escape_string($conn, $_POST['tillers']);
$height = mysqli_real_escape_string($conn, $_POST['height']);
$girth = mysqli_real_escape_string($conn, $_POST['girth']);

$geo_tag_image = $_FILES['geo_tag_image']['name'];
move_uploaded_file($_FILES['geo_tag_image']['tmp_name'], "uploads/".$geo_tag_image);

$observations = mysqli_real_escape_string($conn, $_POST['observations']);
$remark = mysqli_real_escape_string($conn, $_POST['remark']);

$sql = "INSERT INTO farmer_visits
(prn, visit_date, farmer_name, district, taluka, village, soil_condition,
soil_temp, soil_moisture, irrigation, fertilizer, deficiency, pest_attack,
disease_symptoms,krushik,reason, disease_image, spray, health, germination, tillers,
height, girth, geo_tag_image, observations, remark)
VALUES
('$prn', '$visit_date','$farmer_name','$district','$taluka','$village','$soil_condition',
'$soil_temp','$soil_moisture','$irrigation',
'$fertilizer','$deficiency','$pest_attack','$disease_symptoms', '$krushik', '$reason',
'$disease_image','$spray','$health','$germination','$tillers',
'$height','$girth','$geo_tag_image','$observations','$remark')";

$result = mysqli_query($conn, $sql);

if ($result) {
    echo "<script>alert('Visit Saved Successfully'); window.location.href='visit.php';</script>";
} else {
    echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
}
    exit();
}
?>
