<?php
include 'connection.php';

/* ================= FETCH FARMER USING PRN ================= */
if(isset($_POST['fetch_prn'])){

    $prn = $_POST['fetch_prn'];

    $stmt = $conn->prepare("SELECT * FROM map_feedback WHERE prn = ?");
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


/* ================= SAFE IMAGE UPLOAD FUNCTION ================= */
function upload($name){

    if(!is_dir("uploads")){
        mkdir("uploads");
    }

    if(isset($_FILES[$name]) && $_FILES[$name]['error'] == 0){

        $file = time()."_".basename($_FILES[$name]['name']);
        $target = "uploads/".$file;

        move_uploaded_file($_FILES[$name]['tmp_name'], $target);

        return $target;
    }

    return "";
}


/* ================= SAVE FORM ================= */
if(isset($_POST['submit'])){

    $prn = $_POST['prn'] ?? '';
    $farmer_name = $_POST['farmer_name'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $plantation_date = $_POST['plantation_date'] ?? '';
    $district = $_POST['district'] ?? '';
    $taluka = $_POST['taluka'] ?? '';
    $village = $_POST['village'] ?? '';
    $remark = $_POST['remark'] ?? '';

    /* Mobile Validation */
    if(!preg_match('/^[0-9]{10}$/', $mobile)){
        echo "<script>alert('Mobile number must be exactly 10 digits!');</script>";
        exit();
    }

    /* Upload Images (FIXED NAMES) */
    $ndvi = upload("ndvi_image");
    $evi = upload("evi_image");
    $crop = upload("crop_stress_image");
    $water = upload("water_watch_image");
    $growth = upload("early_growth_image");
    $vra = upload("vra_image");
    $mmc =  upload("irrigation_mmc_image");    // ✅ FIXED
    $fasal = upload("irrigation_fasal_image");   // ✅ FIXED
   

    /* SAFE INSERT USING PREPARED STATEMENT */
    $stmt = $conn->prepare("INSERT INTO map_feedback (
    prn,farmer_name,mobile,plantation_date,district,taluka,village, ndvi_image,ndvi_interpretation,ndvi_feedback,
    evi_image,evi_interpretation,evi_feedback,
    crop_image,crop_interpretation,crop_feedback,
    water_image,water_interpretation,water_feedback,
    growth_image,growth_interpretation,growth_feedback,
    vra_image,vra_interpretation,vra_feedback,
    mmc_image,mmc_interpretation,mmc_feedback,
    fasal_image,fasal_interpretation,fasal_feedback,
    remark
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

    $stmt->bind_param("ssssssssssssssssssssssssssssssss",
        $prn,$farmer_name,$mobile,$plantation_date,$district,$taluka,$village,
        $ndvi,$_POST['ndvi_interpretation'],$_POST['ndvi_feedback'],
        $evi,$_POST['evi_interpretation'],$_POST['evi_feedback'],
        $crop,$_POST['crop_stress_interpretation'],$_POST['crop_stress_feedback'],
        $water,$_POST['water_watch_interpretation'],$_POST['water_watch_feedback'],
        $growth,$_POST['early_growth_interpretation'],$_POST['early_growth_feedback'],
        $vra,$_POST['vra_interpretation'],$_POST['vra_feedback'],
        $mmc,$_POST['mmc_interpretation'],$_POST['mmc_feedback'],
        $fasal,$_POST['fasal_interpretation'],$_POST['fasal_feedback'],
        $remark
    );

    if($stmt->execute()){
        echo "<script>alert('Saved Successfully'); window.location='random_sampling.php';</script>";
    } else {
        echo "Error: ".$stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Map Feedback</title>

<style>
body{background:#eef6ee;font-family:Arial}
.box{
max-width:900px;
margin:30px auto;
background:white;
padding:25px;
border-radius:12px;
box-shadow:0 0 12px #aaa;
}
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
.section{
border:2px solid #2e8b57;
padding:15px;
border-radius:10px;
margin-bottom:20px;
}
.section h3{color:#2e8b57}
input,textarea{
width:100%;
padding:8px;
margin:6px 0;
}
textarea{height:70px}
button{
background:#2e8b57;
color:white;
padding:12px;
border:none;
width:100%;
font-size:16px;
cursor:pointer;
}
button:hover{
background:#246b45;
}
</style>
</head>
<body>
     <header>
    <h1>Random Sampling</h1>
    <nav>
<a href="Home.php">Home</a>
<a href="about.php">About</a>
<a href="visit.php">Visits</a>
<a href="complaints.php">Complaints</a>
<a href="random_sampling.php">Random Sampling</a>
<a href="crop guidence.php">Crop Guidance</a>
<a href="Contact.php">Contact</a>
<a href="admin_login.php">Admin</a>
</nav>
</header>

<div class="box">
<form method="POST" enctype="multipart/form-data">

<!-- Farmer Details Section -->
<div class="section">
<h3>Farmer Details</h3>

<input type="text" name="prn" id="prn" placeholder="Enter PRN Number" required>

<input type="text" name="farmer_name" id="name" placeholder="Farmer Name" required>

<input type="text"
       name="mobile"
       id="mobile"
       placeholder="Mobile Number"
       pattern="[0-9]{10}"
       maxlength="10"
       required>

<input type="date" name="plantation_date" id="plantation_date" required>
<input type="text" name="district" id="district" placeholder="District" required>
<input type="text" name="taluka" id="taluka" placeholder="Taluka" required>
<input type="text" name="village" id="village" placeholder="Village" required>

</div>

<?php
$maps = [
"NDVI"=>"ndvi",
"EVI"=>"evi",
"Crop Stress"=>"crop_stress",
"Water Watch"=>"water_watch",
"Early Growth"=>"early_growth",
"VRA"=>"vra",
"Irrigation MMC"=>"mmc",
"Irrigation Fasal"=>"fasal"
];

foreach($maps as $title=>$key){
echo "
<div class='section'>
<h3>$title</h3>
<input type='file' name='{$key}_image' >
<input name='{$key}_interpretation' placeholder='Interpretation' >
<textarea name='{$key}_feedback' placeholder='Feedback' ></textarea>
</div>";
}
?>

<h3>Final Remark</h3>
<textarea name="remark" placeholder="Overall remark for all maps" ></textarea>

<br>
<button name="submit">Submit Feedback</button>

</form>
</div>

<!-- JavaScript -->
<script>

/* Mobile Validation */
document.querySelector("form").addEventListener("submit", function(e){
    var mobile = document.getElementById("mobile").value;

    if(!/^[0-9]{10}$/.test(mobile)){
        alert("Mobile number must be exactly 10 digits.");
        e.preventDefault();
    }
});

/* PRN Auto Fetch */
document.getElementById("prn").addEventListener("blur", function(){

    var prn = this.value.trim();

    if(prn !== ""){
        fetch(window.location.href, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "fetch_prn=" + encodeURIComponent(prn)
        })
        .then(response => response.json())
        .then(data => {

            if(Object.keys(data).length !== 0){

                // ✅ FIXED ID HERE
                document.getElementById("name").value = data.farmer_name || "";
                document.getElementById("mobile").value = data.mobile || "";
                document.getElementById("plantation_date").value = data.plantation_date || "";
                document.getElementById("district").value = data.district || "";
                document.getElementById("taluka").value = data.taluka || "";
                document.getElementById("village").value = data.village || "";

            }

        })
        .catch(error => {
            console.log("Fetch error:", error);
        });
    }
});

</script>

</body>
</html>