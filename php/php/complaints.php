<?php
include 'connection.php';
session_start();

/* ================= FETCH DATA USING PRN (LIVE SEARCH) ================= */
if(isset($_POST['fetch_prn'])){

    $prn = $_POST['fetch_prn'];

    $stmt = $conn->prepare("SELECT farmer_name, mobile, district, taluka, village, sugar_factory_name 
                            FROM complaints 
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


/* ================= COMPLAINT FORM SUBMIT ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['fetch_prn'])) {

    $prn                = $_POST['prn'] ?? "";
    $farmer_name        = $_POST['farmer_name'] ?? "";
    $complaint_date     = $_POST['complaint_date'] ?? "";
    $sugar_factory_name = $_POST['sugar_factory_name'] ?? "";
    $mobile             = $_POST['mobile'] ?? "";
    $district           = $_POST['district'] ?? "";
    $taluka             = $_POST['taluka'] ?? "";
    $village            = $_POST['village'] ?? "";
    $complaint_type     = $_POST['complaint_type'] ?? "";
    $complaint          = $_POST['complaint'] ?? "";
    $solve_status       = "Pending";

    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $_SESSION['error'] = "Mobile number must be exactly 10 digits!";
        header("Location: complaints.php");
        exit();
    }
     /* ===== UPLOAD FOLDER ===== */
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    /* ===== IMAGE UPLOAD ===== */
    $image_name = "";
    if (!empty($_FILES['image']['name'])) {

        $allowed_images = ['jpg','jpeg','png','gif'];
        $img_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (in_array($img_ext, $allowed_images)) {

            $image_name = time() . "_img." . $img_ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image_name);

        } else {
            $_SESSION['error'] = "Invalid image format!";
            header("Location: complaints.php");
            exit();
        }
    }

    

    $stmt = $conn->prepare("INSERT INTO complaints 
        (prn, farmer_name, complaint_date, sugar_factory_name, mobile, district, taluka, village, complaint_type, complaint, image,  solve_status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,  ?)");

    $stmt->bind_param("ssssssssssss",
        $prn,
        $farmer_name,
        $complaint_date,
        $sugar_factory_name,
        $mobile,
        $district,
        $taluka,
        $village,
        $complaint_type,
        $complaint,
        $image_name,

        $solve_status
    );
    if ($stmt->execute()) {
        $_SESSION['success'] = "Complaint Registered Successfully";
    } else {
        $_SESSION['error'] = $stmt->error;
    }

    $stmt->close();
    header("Location: complaints.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Farmer Complaint Registration</title>

<style>
body { margin: 0; font-family: Arial; background: #f4f6f4; }
header { background: linear-gradient(to left, #1b5e20, #4caf50); color: white; padding: 15px 30px; }
nav a { color: white; text-decoration: none; margin-right: 20px; font-weight: bold; }
.form-box { max-width: 520px; margin: 30px auto; background: white; padding: 20px 25px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
label { display: block; margin-top: 12px; font-weight: bold; }
input, textarea { width: 100%; padding: 8px; margin-top: 6px; }
textarea { min-height: 90px; }
button { width: 100%; margin-top: 18px; background: #2e8b57; color: white; padding: 10px; border: none; font-size: 16px; cursor: pointer; }
</style>
</head>
<body>

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

</header>

<div class="form-box">

<form method="POST" enctype="multipart/form-data" id="complaintForm">

<h2>Farmer Complaint Form</h2>

<label>PRN Number</label>
<input type="text" name="prn" id="prn" required autocomplete="off">

<label>Farmer Name</label>
<input type="text" name="farmer_name" id="farmer_name" required>

<label>Date of Complaint</label>
<input type="date" name="complaint_date" required>

<label>Sugar Factory Name</label>
<input type="text" name="sugar_factory_name" id="sugar_factory_name" required>

<label>Mobile Number</label>
<input type="text" name="mobile" id="mobile" maxlength="10" required>

<label>District</label>
<input type="text" name="district" id="district" required>

<label>Taluka</label>
<input type="text" name="taluka" id="taluka" required>

<label>Village</label>
<input type="text" name="village" id="village" required>

<label>Type of Complaint</label>
<label><input type="radio" name="complaint_type" value="Krushak app"> Krushak App</label>
<label><input type="radio" name="complaint_type" value="Crop maps"> MMC(satellite maps)</label>
<label><input type="radio" name="complaint_type" value="Fasal/IoT"> Fasal / IoT</label>
<label><input type="radio" name="complaint_type" value="Other"> Other</label>

<label>Complaint Details</label>
<textarea name="complaint" ></textarea>

<label>Upload Image</label>
    <input type="file" name="image" accept="image/*">



<button type="submit">Submit Complaint</button>

</form>
</div>

<script>

/* LIVE PRN FETCH */
let timeout = null;

document.getElementById("prn").addEventListener("keyup", function(){

    clearTimeout(timeout);
    let prnValue = this.value.trim();
    if(prnValue.length < 2) return;

    timeout = setTimeout(function(){

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "complaints.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function(){

            if(xhr.readyState === 4 && xhr.status === 200){

                let data = JSON.parse(xhr.responseText);

                if(Object.keys(data).length > 0){

                    farmer_name.value = data.farmer_name;
                    mobile.value = data.mobile;
                    district.value = data.district;
                    taluka.value = data.taluka;
                    village.value = data.village;
                    sugar_factory_name.value = data.sugar_factory_name;

                    farmer_name.readOnly = true;
                    mobile.readOnly = true;
                    district.readOnly = true;
                    taluka.readOnly = true;
                    village.readOnly = true;
                    sugar_factory_name.readOnly = true;

                } else {

                    farmer_name.value = "";
                    mobile.value = "";
                    district.value = "";
                    taluka.value = "";
                    village.value = "";
                    sugar_factory_name.value = "";

                    farmer_name.readOnly = false;
                    mobile.readOnly = false;
                    district.readOnly = false;
                    taluka.readOnly = false;
                    village.readOnly = false;
                    sugar_factory_name.readOnly = false;
                }
            }
        };

        xhr.send("fetch_prn=" + encodeURIComponent(prnValue));

    }, 500);
});
</script>

</body>
</html>