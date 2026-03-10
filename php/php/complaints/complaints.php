<?php
include 'connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $farmer_name        = $_POST['farmer_name'];
    $complaint_date     = $_POST['complaint_date'];
    $sugar_factory_name = $_POST['sugar_factory_name'];
    $mobile             = $_POST['mobile'];
    $district           = $_POST['district'];
    $taluka             = $_POST['taluka'];
    $village            = $_POST['village'];
    $complaint_type     = $_POST['complaint_type'];
    $complaint          = $_POST['complaint'];
    $solve_status       = "Pending";

    /* ===== CREATE UPLOAD FOLDER ONCE ===== */
$uploadDir = "uploads/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

/* ===== IMAGE UPLOAD ===== */
$image_name = "";

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

    $image_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", $_FILES['image']['name']);

    $imagePath = $uploadDir . $image_name;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
        die("Image upload failed!");
    }
}

/* ===== VOICE UPLOAD ===== */
$voice_name = "";

if (isset($_FILES['voice']) && $_FILES['voice']['error'] == 0) {

    $voice_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", $_FILES['voice']['name']);

    $voicePath = $uploadDir . $voice_name;

    if (!move_uploaded_file($_FILES['voice']['tmp_name'], $voicePath)) {
        die("Voice upload failed!");
    }
}


    /* ===== INSERT QUERY ===== */
    $sql = "INSERT INTO complaints 
    (farmer_name, complaint_date, sugar_factory_name, mobile, district, taluka, village, complaint_type, complaint, image, voice, solve_status)
    VALUES
    ('$farmer_name','$complaint_date','$sugar_factory_name','$mobile','$district','$taluka','$village','$complaint_type','$complaint','$image_name','$voice_name','$solve_status')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Complaint Registered Successfully";
    } else {
        $_SESSION['error'] = mysqli_error($conn);
    }

    header("Location: complaints.php");
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Farmer Complaint Registration</title>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f6f4;
}

/* ---------- HEADER ---------- */
header {
    background: linear-gradient(to left, #1b5e20, #4caf50);
    color: white;
    padding: 15px 30px;
}

nav a {
    color: white;
    text-decoration: none;
    margin-right: 20px;
    font-weight: bold;
}

/* ---------- FORM CENTER ---------- */
.form-wrapper {
    margin: 30px auto;
}

.form-box {
    max-width: 520px;
    margin: auto;
    background: white;
    padding: 20px 25px;
    border-radius: 8px;
    box-shadow: 0 0 10px #ccc;
}

label {
    display: block;
    margin-top: 12px;
    font-weight: bold;
}

input, textarea {
    width: 100%;
    padding: 8px;
    margin-top: 6px;
}

textarea {
    min-height: 90px;
}

button {
    width: 100%;
    margin-top: 18px;
    background: #2e8b57;
    color: white;
    padding: 10px;
    border: none;
    font-size: 16px;
    cursor: pointer;
}
</style>
</head>

<body>

<!-- ---------- ALERTS (SHOW ONLY ONCE) ---------- -->
<?php
if (isset($_SESSION['success'])) {
    echo "<script>alert('" . $_SESSION['success'] . "');</script>";
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo "<script>alert('" . $_SESSION['error'] . "');</script>";
    unset($_SESSION['error']);
}
?>

<header>
    <h1>Farmers Complaints Website</h1>
    <nav>
        <a href="Home.php">Home</a>
        <a href="about.php">About</a>
        <a href="visit.php">Visits</a>
        <a href="complaints.php">Complaints</a>
        <a href="crop guidence.php">Crop Guidance</a>
        <a href="Contact.php">Contact</a>
        <a href="admin_login.php">Admin</a>
    </nav>
</header>

<div class="form-wrapper">
<div class="form-box">

<form method="POST" enctype="multipart/form-data">
    <h2>Farmer Complaint Form</h2>

    <label>Farmer Name</label>
        <input type="text" name="farmer_name" placeholder="Farmer Name" required>

        <label>Date of complaint</label>
                 <input type="date" name="complaint_date" required>

        <label>Sugar factory Name</label>
        <input type="text" name="sugar_factory_name" placeholder="Sugar factory Name" required>

         <label>Mobile Number</label>
        <input type="text" name="mobile" placeholder="Mobile Number" required>

         <label>district</label>
        <input type="text" name="district" placeholder="District" required>

         <label>taluka</label>
        <input type="text" name="taluka" placeholder="Taluka" required>

         <label>village</label>
        <input type="text" name="village" placeholder="Village" required>
        <div class="radio-group">
            <label>Type of Complaint:</label>

<label><input type="radio" name="complaint_type" value="Krushak app" required> Krushak app</label>
<label><input type="radio" name="complaint_type" value="Crop maps"> Crop maps</label>
<label><input type="radio" name="complaint_type" value="Fasal/IoT"> Fasal/IoT</label>
<label><input type="radio" name="complaint_type" value="MMC"> MMC</label>
<label><input type="radio" name="complaint_type" value="Other"> Other complaints</label>

        </div>
        <br><br>

    <label>Complaint</label>
    <textarea name="complaint" placeholder="Enter Complaint" required></textarea>
    <label>Upload Image</label>
    <input type="file" name="image" accept="image/*">

    <label>Upload Voice</label>
    <input type="file" name="voice" accept="audio/*">

    <button type="submit">Submit Complaint</button>
</form>

</div>
</div>

</body>
</html>
