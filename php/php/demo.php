<?php
session_start();
include 'connection.php';

/* ================= FETCH DATA USING PRN (LIVE SEARCH) ================= */
if(isset($_POST['fetch_prn'])){

    $prn = $_POST['fetch_prn'];

    $stmt = $conn->prepare("SELECT farmer_name, district, taluka, village 
                            FROM farmer_visits 
                            WHERE prn = ? 
                            ORDER BY id DESC LIMIT 1");

    $stmt->bind_param("s", $prn);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode([]);
    }
    exit();
}


/* ================= INSERT FULL DATA ================= */
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['fetch_prn'])) {

    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Upload Disease Image
    $disease_image = "";
    if (!empty($_FILES['disease_image']['name'])) {
        $disease_image = time() . "_" . $_FILES['disease_image']['name'];
        move_uploaded_file($_FILES['disease_image']['tmp_name'], $uploadDir . $disease_image);
    }

    // Upload Geo Tag Image
    $geo_tag_image = "";
    if (!empty($_FILES['geo_tag_image']['name'])) {
        $geo_tag_image = time() . "_" . $_FILES['geo_tag_image']['name'];
        move_uploaded_file($_FILES['geo_tag_image']['tmp_name'], $uploadDir . $geo_tag_image);
    }

    // Convert deficiency array to string
    $deficiency = isset($_POST['deficiency']) ? implode(",", $_POST['deficiency']) : "";

    $sql = "INSERT INTO farmer_visits 
    (prn, visit_date, farmer_name, district, taluka, village, rainfall, temperature, humidity,
    soil_temp, soil_moisture, irrigation, fertilizer, deficiency, pest_attack,
    disease_symptoms, disease_image, spray, health, germination, tillers,
    height, girth, geo_tag_image, observations, remark)
    VALUES 
    ('$_POST[prn]','$_POST[visit_date]','$_POST[farmer_name]','$_POST[district]',
    '$_POST[taluka]','$_POST[village]','$_POST[rainfall]','$_POST[temperature]',
    '$_POST[humidity]','$_POST[soil_temp]','$_POST[soil_moisture]','$_POST[irrigation]',
    '$_POST[fertilizer]','$deficiency','$_POST[pest_attack]','$_POST[disease_symptoms]',
    '$disease_image','$_POST[spray]','$_POST[health]','$_POST[germination]',
    '$_POST[tillers]','$_POST[height]','$_POST[girth]',
    '$geo_tag_image','$_POST[observations]','$_POST[remark]')";

    mysqli_query($conn, $sql);

    echo "<script>alert('Visit Report Submitted Successfully'); window.location='visit_ref.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Farmer Visit Form</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    background: #f2f6f3;
}

header {
    background: linear-gradient(to left, #1b5e20, #4caf50);
    color: white;
    padding: 15px;
}

nav a {
    color: white;
    margin-right: 20px;
    text-decoration: none;
    font-weight: bold;
}

.container {
    width: 85%;
    margin: 30px auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px #ccc;
}

h3 {
    background: #2e8b57;
    color: white;
    padding: 8px;
    border-radius: 5px;
}

label {
    font-weight: bold;
}

input, select, textarea {
    width: 100%;
    padding: 8px;
    margin: 5px 0 15px 0;
    border-radius: 5px;
    border: 1px solid #ccc;
}

button {
    background: #2e8b57;
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    background: #1b5e20;
}
</style>
</head>

<body>

<header>
<h1>Farmers Visit Website</h1>
<nav>
<a href="Home.php">Home</a>
<a href="visit.php">Visits</a>
<a href="complaints.php">Complaints</a>
<a href="admin_login.php">Admin</a>
</nav>
</header>

<div class="container">

<form method="post" enctype="multipart/form-data">

<h3>Basic Information</h3>

<label>PRN Number</label>
<input type="text" name="prn" id="prn" required>

<label>Visit Date</label>
<input type="date" name="visit_date" required>

<label>Farmer Name</label>
<input type="text" name="farmer_name" id="farmer_name" required>

<label>District</label>
<input type="text" name="district" id="district" required>

<label>Taluka</label>
<input type="text" name="taluka" id="taluka" required>

<label>Village</label>
<input type="text" name="village" id="village" required>


<h3>Weather & Climate</h3>

<select name="rainfall">
<option value="">--Select Rainfall--</option>
<option>Last 24 Hours</option>
<option>Past Week</option>
<option>No Rainfall</option>
</select>

<input type="number" name="temperature" placeholder="Temperature (°C)">
<input type="number" name="humidity" placeholder="Humidity (%)">


<h3>Soil & Water</h3>

<select name="soil_temp">
<option>Cool</option>
<option>Moderate</option>
<option>Hot</option>
</select>

<select name="soil_moisture">
<option>Dry</option>
<option>Adequate</option>
<option>Excess</option>
</select>

<textarea name="irrigation" placeholder="Last Irrigation Details"></textarea>


<h3>Pest & Disease</h3>

<input type="radio" name="pest_attack" value="Yes"> Yes
<input type="radio" name="pest_attack" value="No"> No <br><br>

<input type="file" name="disease_image">


<h3>Observations</h3>

<textarea name="observations"></textarea>
<textarea name="remark"></textarea>

<br>
<button type="submit">Submit Visit Report</button>

</form>
</div>
<script>
let timeout = null;

document.getElementById("prn").addEventListener("keyup", function(){

    clearTimeout(timeout);
    let prnValue = this.value.trim();

    if(prnValue.length == 0){
        document.getElementById("farmer_name").readOnly = false;
        document.getElementById("district").readOnly = false;
        document.getElementById("taluka").readOnly = false;
        document.getElementById("village").readOnly = false;
        return;
    }

    timeout = setTimeout(function(){

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "", true);  // same page
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function(){
            if(xhr.readyState === 4 && xhr.status === 200){

                let data = JSON.parse(xhr.responseText);

                if(Object.keys(data).length > 0){

                    document.getElementById("farmer_name").value = data.farmer_name;
                    document.getElementById("district").value = data.district;
                    document.getElementById("taluka").value = data.taluka;
                    document.getElementById("village").value = data.village;

                    document.getElementById("farmer_name").readOnly = true;
                    document.getElementById("district").readOnly = true;
                    document.getElementById("taluka").readOnly = true;
                    document.getElementById("village").readOnly = true;

                } else {

                    document.getElementById("farmer_name").value = "";
                    document.getElementById("district").value = "";
                    document.getElementById("taluka").value = "";
                    document.getElementById("village").value = "";

                    document.getElementById("farmer_name").readOnly = false;
                    document.getElementById("district").readOnly = false;
                    document.getElementById("taluka").readOnly = false;
                    document.getElementById("village").readOnly = false;
                }
            }
        };

        xhr.send("fetch_prn=" + encodeURIComponent(prnValue));

    }, 400);
});
</script>

</body>
</html>