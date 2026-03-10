<?php
include 'connection.php';

if(isset($_POST['submit'])) {

    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $district = $_POST['district'];
    $taluka = $_POST['taluka'];
    $type = $_POST['type'];
    $complaint = $_POST['complaint'];

    $imageName = "";

    if(!empty($_FILES['image']['name'])) {
        $imageName = time()."_".$_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$imageName);
    }

    mysqli_query($conn,"INSERT INTO complaints 
    (farmer_name,mobile,district,taluka,complaint_type,complaint,image,solve_status) 
    VALUES 
    ('$name','$mobile','$district','$taluka','$type','$complaint','$imageName','Pending')");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Farmer Complaint</title>
</head>
<body>

<h2>Submit Complaint</h2>

<form method="POST" enctype="multipart/form-data">

Name: <input type="text" name="name" required><br><br>
Mobile: <input type="text" name="mobile" required><br><br>
District: <input type="text" name="district"><br><br>
Taluka: <input type="text" name="taluka"><br><br>

Type:
<select name="type">
<option>Crop</option>
<option>Water</option>
<option>Fertilizer</option>
</select><br><br>

Complaint:<br>
<textarea name="complaint"></textarea><br><br>

Upload Image:
<input type="file" name="image"><br><br>

<button type="submit" name="submit">Submit</button>

</form>

</body>
</html>
