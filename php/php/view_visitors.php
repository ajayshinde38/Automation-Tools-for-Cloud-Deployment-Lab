<?php
session_start();
include 'connection.php';

$query = "SELECT * FROM farmer_visits ORDER BY visit_date ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html>
<head>
<title>All Visitors</title>

<style>
body { 
    font-family: Arial; 
    background: #f4f6f4; 
}

/* Table */
table { 
    width: 100%; 
    border-collapse: collapse; 
    background: white; 
    font-size: 13px;
}
th, td { 
    padding: 8px; 
    border: 1px solid #ccc; 
    text-align: center; 
}
th { 
    background-color: #4CAF50; 
    color: white; 
}

/* Image link */
a.image-link { 
    color: #2e7d32; 
    text-decoration: underline; 
    cursor: pointer; 
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    padding-top: 60px;
    left: 0; 
    top: 0;
    width: 100%; 
    height: 100%;
    background-color: rgba(0,0,0,0.8);
}

.modal-content {
    margin: auto;
    display: block;
    max-width: 80%;
    max-height: 80%;
    border-radius: 10px;
}

.close {
    position: absolute;
    top: 30px;
    right: 50px;
    color: #fff;
    font-size: 40px;
    cursor: pointer;
}
</style>
</head>

<body>

<h2 align="center">All Visitors</h2>

<table>
<tr>
    <th>ID</th>
    <th>Visit Date</th>
    <th>Name</th>
    <th>District</th>
    <th>Taluka</th>
    <th>Village</th>
    <th>soil condition</th>
    <th>Soil Temp</th>
    <th>Soil Moisture</th>
    <th>Irrigation</th>
    <th>Fertilizer</th>
    <th>Deficiency</th>
    <th>Pest</th>
    <th>Disease</th>
     <th>krushik</th>
    <th>Reason</th>

    <th>Disease Image</th>
    <th>Spray</th>
    <th>Health</th>
    <th>Germination</th>
    <th>Tillers</th>
    <th>Height</th>
    <th>Girth</th>
    <th>Geo Tag Image</th>
    <th>Observation</th>
    <th>Remark</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>

<td><?= $row['id']; ?></td>
<td><?= $row['visit_date']; ?></td>
<td><?= $row['farmer_name']; ?></td>
<td><?= $row['district']; ?></td>
<td><?= $row['taluka']; ?></td>
<td><?= $row['village']; ?></td>
<td><?= $row['soil_condition']; ?></td>
<td><?= $row['soil_temp']; ?></td>
<td><?= $row['soil_moisture']; ?></td>
<td><?= $row['irrigation']; ?></td>
<td><?= $row['fertilizer']; ?></td>
<td><?= $row['deficiency']; ?></td>
<td><?= $row['pest_attack']; ?></td>
<td><?= $row['disease_symptoms']; ?></td>
<td><?= $row['krushik']; ?></td>
<td><?= $row['reason']; ?></td>



<!-- ✅ Disease Image -->
<td>
<?php if($row['disease_image']) { 
    $filepath = "uploads/" . basename($row['disease_image']); 
?>
    <a class="image-link" onclick="openModal('<?= $filepath; ?>')">View Image</a>
<?php } else { echo "No Image"; } ?>
</td>

<td><?= $row['spray']; ?></td>
<td><?= $row['health']; ?></td>
<td><?= $row['germination']; ?></td>
<td><?= $row['tillers']; ?></td>
<td><?= $row['height']; ?></td>
<td><?= $row['girth']; ?></td>

<!-- ✅ Geo Tag Image -->
<td>
<?php if($row['geo_tag_image']) { 
    $geoPath = "uploads/" . basename($row['geo_tag_image']); 
?>
    <a class="image-link" onclick="openModal('<?= $geoPath; ?>')">View Image</a>
<?php } else { echo "No Image"; } ?>
</td>

<td><?= $row['observations']; ?></td>
<td><?= $row['remark']; ?></td>

</tr>
<?php } ?>

</table>

<!-- ✅ Modal -->
<div id="imageModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modalImg">
</div>

<script>
function openModal(src) {
    document.getElementById("modalImg").src = src;
    document.getElementById("imageModal").style.display = "block";
}

function closeModal() {
    document.getElementById("imageModal").style.display = "none";
}

window.onclick = function(event) {
    var modal = document.getElementById("imageModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

</body>
</html> 