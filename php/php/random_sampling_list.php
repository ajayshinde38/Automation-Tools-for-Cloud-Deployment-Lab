<?php
include 'connection.php';

$result = mysqli_query($conn, "SELECT * FROM map_feedback ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>All Random Sampling Records</title>

<style>
body { 
    font-family: Arial; 
    background: #f4f6f4; 
}

/* Header */
.header {
    background: linear-gradient(to left, #1b5e20, #4caf50);
    color: white;
    padding: 15px;
}

.header h2 {
    margin: 0;
}

/* Table */
table { 
    width: 100%; 
    border-collapse: collapse; 
    background: white; 
    margin-top:20px;
}
th, td { 
    padding: 10px; 
    border: 1px solid #ccc; 
    text-align: center; 
}
th { 
    background: #2e7d32; 
    color: white; 
}

a.image-link { 
    color: #2e7d32; 
    text-decoration: underline; 
    cursor: pointer; 
}

/* Delete Button */
.btn-delete {
    padding: 6px 12px;
    background: #d32f2f;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
}
.btn-delete:hover {
    background: #b71c1c;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    padding-top: 60px;
    left: 0; top: 0;
    width: 100%; height: 100%;
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

<div class="header">
    <h2>All Random Sampling Records</h2>
</div>

<table>
<tr>
    <th>ID</th>
    <th>PRN</th>
    <th>Farmer</th>
    <th>Mobile</th>
    <th>Plantation Date</th>
    <th>District</th>
    <th>Taluka</th>
    <th>Village</th>
     <th>NDVI</th>
     <th>Interpretation</th>
    <th>Feedback</th>
    <th>EVI</th>
     <th>Interpretation</th>
    <th>Feedback</th>

    <th>Crop</th>
    <th>Interpretation</th>
    <th>Feedback</th>
    <th>Water</th>
    <th>Interpretation</th>
    <th>Feedback</th>
    <th>Growth</th>
    <th>Interpretation</th>
    <th>Feedback</th>
    <th>VRA</th>
    <th>Interpretation</th>
    <th>Feedback</th>
    <th>MMC</th>
    <th>Interpretation</th>
    <th>Feedback</th>
    <th>Fasal</th>
    <th>Interpretation</th>
    <th>Feedback</th>
    <th>Remark</th>
    <th>Action</th>
    
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= $row['id']; ?></td>
    <td><?= $row['prn']; ?></td>
    <td><?= $row['farmer_name']; ?></td>
    <td><?= $row['mobile']; ?></td>
    <td><?= $row['plantation_date']; ?></td>
    <td><?= $row['district']; ?></td>
    <td><?= $row['taluka']; ?></td>
    <td><?= $row['village']; ?></td>
   


   
   <!-- NDVI -->
<td>
<?php
if(!empty($row['ndvi_image'])){
    echo "<a class='image-link' onclick=\"openModal('".$row['ndvi_image']."')\">View</a>";
} else {
    echo "No Image";
}
?>
</td>
<td><?= $row['ndvi_interpretation']; ?></td>
<td><?= $row['ndvi_feedback']; ?></td>

<!-- EVI -->
<td>
<?php
if(!empty($row['evi_image'])){
    echo "<a class='image-link' onclick=\"openModal('".$row['evi_image']."')\">View</a>";
} else {
    echo "No Image";
}
?>
</td>
<td><?= $row['evi_interpretation']; ?></td>
<td><?= $row['evi_feedback']; ?></td>

<!-- CROP -->
<td>
<?php
if(!empty($row['crop_image'])){
    echo "<a class='image-link' onclick=\"openModal('".$row['crop_image']."')\">View</a>";
} else {
    echo "No Image";
}
?>
</td>
<td><?= $row['crop_interpretation']; ?></td>
<td><?= $row['crop_feedback']; ?></td>

<!-- WATER -->
<td>
<?php
if(!empty($row['water_image'])){
    echo "<a class='image-link' onclick=\"openModal('".$row['water_image']."')\">View</a>";
} else {
    echo "No Image";
}
?>
</td>
<td><?= $row['water_interpretation']; ?></td>
<td><?= $row['water_feedback']; ?></td>

<!-- GROWTH -->
<td>
<?php
if(!empty($row['growth_image'])){
    echo "<a class='image-link' onclick=\"openModal('".$row['growth_image']."')\">View</a>";
} else {
    echo "No Image";
}
?>
</td>
<td><?= $row['growth_interpretation']; ?></td>
<td><?= $row['growth_feedback']; ?></td>

<!-- VRA -->
<td>
<?php
if(!empty($row['vra_image'])){
    echo "<a class='image-link' onclick=\"openModal('".$row['vra_image']."')\">View</a>";
} else {
    echo "No Image";
}
?>
</td>
<td><?= $row['vra_interpretation']; ?></td>
<td><?= $row['vra_feedback']; ?></td>

<!-- MMC -->
<td>
<?php
if(!empty($row['mmc_image'])){
    echo "<a class='image-link' onclick=\"openModal('".$row['mmc_image']."')\">View</a>";
} else {
    echo "No Image";
}
?>
</td>
<td><?= $row['mmc_interpretation']; ?></td>
<td><?= $row['mmc_feedback']; ?></td>

<!-- FASAL -->
<td>
<?php
if(!empty($row['fasal_image'])){
    echo "<a class='image-link' onclick=\"openModal('".$row['fasal_image']."')\">View</a>";
} else {
    echo "No Image";
}
?>
</td>
<td><?= $row['fasal_interpretation']; ?></td>
<td><?= $row['fasal_feedback']; ?></td>
    

    <td><?= $row['remark']; ?></td>

    <td>
        <a class="btn-delete" 
           href="delete_random.php?id=<?= $row['id']; ?>" 
           onclick="return confirm('Delete this record?');">
           Delete
        </a>
    </td>
</tr>
<?php } ?>

</table>

<!-- Image Modal -->
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