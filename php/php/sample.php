<?php
include 'connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST')


/* ================= SAVE FORM ================= */
if(isset($_POST['submit'])){

function upload($name){
    if(!is_dir("uploads")) mkdir("uploads");

    if($_FILES[$name]['error'] == 0){
        $file = time()."_".$_FILES[$name]['name'];
        move_uploaded_file($_FILES[$name]['tmp_name'], "uploads/".$file);
        return "uploads/".$file;
    }
    return "";
}

/* Farmer Basic Details */
$prn = mysqli_real_escape_string($conn, $_POST['prn']);
$name = mysqli_real_escape_string($conn, $_POST['farmer_name']);
$mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
$plantation_date = mysqli_real_escape_string($conn, $_POST['plantation_date']);
$district = mysqli_real_escape_string($conn, $_POST['district']);
$taluka = mysqli_real_escape_string($conn, $_POST['taluka']);
$village = mysqli_real_escape_string($conn, $_POST['village']);

/* Mobile Validation */
if(!preg_match('/^[0-9]{10}$/', $mobile)){
    echo "<script>alert('Mobile number must be exactly 10 digits!');</script>";
    exit();
}

/* Upload Images */
$ndvi = upload("ndvi_image");
$evi = upload("evi_image");
$crop = upload("crop_stress_image");
$water = upload("water_watch_image");
$growth = upload("early_growth_image");
$vra = upload("vra_image");
$mmc = upload("irrigation_mmc_image");
$fasal = upload("irrigation_fasal_image");

/* Insert Query */
$sql="INSERT INTO map_feedback VALUES(
NULL,
'$prn',
'$farmer_name','$mobile','$plantation_date','$district','$taluka','$village',
'$ndvi','".$_POST['ndvi_interpretation']."','".$_POST['ndvi_feedback']."',
'$evi','".$_POST['evi_interpretation']."','".$_POST['evi_feedback']."',
'$crop','".$_POST['crop_stress_interpretation']."','".$_POST['crop_stress_feedback']."',
'$water','".$_POST['water_watch_interpretation']."','".$_POST['water_watch_feedback']."',
'$growth','".$_POST['early_growth_interpretation']."','".$_POST['early_growth_feedback']."',
'$vra','".$_POST['vra_interpretation']."','".$_POST['vra_feedback']."',
'$mmc','".$_POST['mmc_interpretation']."','".$_POST['mmc_feedback']."',
'$fasal','".$_POST['fasal_interpretation']."','".$_POST['fasal_feedback']."',
'".$_POST['remark']."'
)";

$result = mysqli_query($conn,$sql);

if ($result) {
    echo "<script>alert('Saved Successfully');</script>";
} else {
    echo "<script>alert('Error: ".mysqli_error($conn)."');</script>";
}

}