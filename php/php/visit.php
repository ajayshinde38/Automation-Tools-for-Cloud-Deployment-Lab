<?php
session_start();
include 'connection.php';

/* ================= FETCH DATA USING PRN (LIVE SEARCH) ================= */
if(isset($_POST['fetch_prn'])){

    $prn = $_POST['fetch_prn'];

    // Fetch farmer details from farmers table
    $stmt = $conn->prepare("SELECT farmer_name, district, taluka, village 
                            FROM farmer_visits 
                            WHERE prn = ? 
                            LIMIT 1");
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


/* ================= INSERT VISIT DATA ================= */
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

    // Get Form Data
    $prn = $_POST['prn'];
    $visit_date = $_POST['visit_date'];
    $farmer_name = $_POST['farmer_name'];
    $district = $_POST['district'];
    $taluka = $_POST['taluka'];
    $village = $_POST['village'];
    $soil_condition = $_POST['soil_condition'];

    $soil_temp = $_POST['soil_temp'];
    $soil_moisture = $_POST['soil_moisture'];
    $irrigation = $_POST['irrigation'];
    $fertilizer = $_POST['fertilizer'];
    $deficiency = isset($_POST['deficiency']) ? implode(",", $_POST['deficiency']) : "";
    $pest_attack = $_POST['pest_attack'];
    $disease_symptoms = $_POST['disease_symptoms'];
     $krushik = $_POST['krushik'];
    $reason = $_POST['reason'];

    $spray = $_POST['spray'];
    $health = $_POST['health'];
    $germination = $_POST['germination'];
    $tillers = $_POST['tillers'];
    $height = $_POST['height'];
    $girth = $_POST['girth'];
    $observations = $_POST['observations'];
    $remark = $_POST['remark'];

    $stmt = $conn->prepare("INSERT INTO farmer_visits
    (prn, visit_date, farmer_name, district, taluka, village,soil_condition,
    soil_temp, soil_moisture, irrigation, fertilizer, deficiency, pest_attack,
    disease_symptoms, krushik, reason, disease_image, spray, health, germination, tillers,
    height, girth, geo_tag_image, observations, remark)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

    $stmt->bind_param("ssssssssssssssssssssssssss",
        $prn, $visit_date, $farmer_name, $district, $taluka, $village,
        $soil_condition, $soil_temp, $soil_moisture,
        $irrigation, $fertilizer, $deficiency, $pest_attack,
        $disease_symptoms, $krushik, $reason, $disease_image, $spray, $health,
        $germination, $tillers, $height, $girth,
        $geo_tag_image, $observations, $remark
    );

    $stmt->execute();
    

if($stmt->execute()){
    echo "<script>alert('Visit Submitted Successfully'); window.location='visit.php';</script>";
}

exit();

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
            font-family: Arial, sans-serif;
            background-color: #f4f6f4;
        }

        /* Header */
        header {
            background: linear-gradient(to left, #1b5e20, #4caf50);
            color: white;
            padding: 15px;
        }

        header h1 {
            margin: 0;
        }

        header p {
            margin-top: 5px;
            font-size: 14px;
        }

        /* Navigation */
        nav {
            margin-top: 15px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        /* Content */
        .container {
            padding: 30px;
        }

body{font-family:Arial;background:#eef2f3}
.container{width:100%;margin:auto;background:#fff;padding:20px}
h3{background:#2e8b57;color:white;padding:8px}
label{font-weight:bold}
input,select,textarea{width:100%;padding:6px;margin:5px 0}
.row{display:flex;gap:15px}
.col{flex:1}
.checkbox-group input{width:auto}
button{background:#2e8b57;color:white;padding:10px 20px;border:none}
</style>
</head>

<body>
    <header>
    <h1>Farmers Visit Website</h1>
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
<div class="container">

<h3>Farmer Visit Observation Form</h3>

<form method="post" enctype="multipart/form-data">
<label>PRN Number</label>
<input type="text" name="prn" id="prn" required>

<label>Visit Date</label>
<input type="date" name="visit_date">

<label>Farmer Name</label>
<input type="text" name="farmer_name" id="farmer_name" required>

<label>District</label>
<input type="text" name="district" id="district" required>

<label>Taluka</label>
<input type="text" name="taluka" id="taluka" required>

<label>Village</label>
<input type="text" name="village" id="village" required>

   

</select>
</div>
</div>

<h3>Soil moisture Conditions</h3>

Are the soil moisture readings matching the actual field condition?

    
    <div class="form-group">
       <br>
        <select name="soil_condition" onchange="showReason(this.value)" required>
            <option value="">-- Select --</option>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
            <option value="No">Not Sure</option>
        </select>
    </div>

   

   


<br></br>


<!-- 5 Soil -->
<h3>Soil & Water Conditions</h3>

<label>Soil Temperature</label>
<select name="soil_temp">
<option>Cool</option>
<option>Moderate</option>
<option>Hot</option>
</select>

<label>Soil Moisture</label>
<select name="soil_moisture">
<option>Dry</option>
<option>Adequate</option>
<option>Excess</option>
</select>

<label>Last Irrigation Details</label>
<textarea name="irrigation"></textarea>

<!-- 6 Crop -->
<h3>Crop & Nutrition Management</h3>

<label>Fertilizer Applied</label>
<textarea name="fertilizer"></textarea>

<label>Nutrient Deficiency Symptoms</label>
<input type="checkbox" name="deficiency[]" value="Yellowing"> Yellowing
<input type="checkbox" name="deficiency[]" value="Short Growth"> Short Growth
<input type="checkbox" name="deficiency[]" value="Other"> other

<!-- 7 Pest -->
<h3>Pest & Disease Status</h3>

<label>Visible Pest Attack?</label>
<input type="radio" name="pest_attack" value="Yes"> Yes
<input type="radio" name="pest_attack" value="No"> No <br>
</select>

<label>Disease Symptoms</label>
<input type="radio" name="disease_symptoms" value="Leaf spots">Leaf spots
<input type="radio" name="disease_symptoms" value="wilting">wilting
<input type="radio" name="disease_symptoms" value="fruit rot">fruit rot
<input type="radio" name="disease_symptoms" value="other">other<br><br>
</select>

Are you following the fertilizer and spraying schedule recommended in the krushik app? 

 <script>
        function showReason(value) {
            var reasonDiv = document.getElementById("reasonBox");
            if (value === "No") {
                reasonDiv.style.display = "block";
            } else {
                reasonDiv.style.display = "none";
            }
        }
    </script>


    
    <div class="form-group">
       <br>
          <select name="krushik" onchange="showReason(this.value)" required>
            <option value="">-- Select --</option>
            <option value="Yes">Yes</option>
             <option value="No">No</option>
        </select>
    </div>

     <div class="form-group" id="reasonBox">
        <label>Enter Reason:</label><br>
        <input type="text" name="reason" placeholder="Write your reason here">
    </div>

   







<br></br>
  <label>Upload Disease Image</label>
<input type="file" name="disease_image" accept="image/*">

<!-- 8 Spray -->
<h3>Spraying & Protection</h3>
<label>Last Spray Details</label>
<textarea name="spray"></textarea>

<!-- 9 Performance -->
<h3>Crop Stress & Performance</h3>

<label>Crop Health</label>
<select name="health">
<option>Healthy</option>
<option>Moderate</option>
<option>Weak</option>
</select>

<label>Germination %</label>
<input type="number" name="germination">

<label>No. of Tillers per Clump</label>
<input type="number" name="tillers">

<label>Cane Height (cm)</label>
<input type="number" name="height">

<label>Cane Girth (cm)</label>
<input type="number" name="girth">
<h3>Ground Truth & Observations</h3>

<label>Upload Geo-Tag Image (Ground Truth)</label>
<input type="file" name="geo_tag_image" accept="image/*"> <!-- NEW FIELD -->

<label>Farmer Observations</label>
<textarea name="observations"></textarea>

<label>Remark</label>
<textarea name="remark"></textarea>

<button type="submit">Submit Visit Report</button>
</form>
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

</div>
</body>
</html>
