<?php
include 'connection.php';

/* ONLY PENDING COMPLAINTS */
$result = mysqli_query($conn, 
    "SELECT * FROM complaints 
     WHERE solve_status='Pending' 
     ORDER BY id ASC"
);
?>
<!DOCTYPE html>
<html>
<head>
<title>Pending Complaints</title>

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
}
th, td { 
    padding: 10px; 
    border: 1px solid #ccc; 
    text-align: center; 
}
th { 
    background:  #2e7d32; 

    color: white; 
}

.status-pending { 
    color: orange; 
    font-weight: bold; 
}

/* Buttons */
.btn {
    padding: 7px 14px;
    text-decoration: none;
    border-radius: 6px;
    color: white;
    font-weight: bold;
    display: inline-block;
    margin: 4px;
}

.solved {
    background: #2e7d32;
}
.delete {
    background: #d32f2f;
}

/* Image link */
a.image-link { 
    color: #2e7d32; 
    cursor: pointer; 
    text-decoration: underline;
}

/* Modal */
.modal{
display:none;
position:fixed;
left:0;top:0;
width:100%;height:100%;
background:rgba(0,0,0,0.8);
padding-top:60px
}
.modal-content{
margin:auto;
max-width:80%;
max-height:80%;
border-radius:10px
}
.close{
position:absolute;
top:30px;right:50px;
font-size:40px;color:white;
cursor:pointer
}
</style>
</head>

<body>

<h2 align="center">Pending Complaints</h2>

<table>
<tr>
<th>ID</th>
<th>Farmer</th>
<th>Mobile</th>
<th>District</th>
<th>Taluka</th>
<th>Type</th>
<th>Complaint</th>
<th>Image</th>

<th>Date</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['farmer_name'] ?></td>
<td><?= $row['mobile'] ?></td>
<td><?= $row['district'] ?></td>
<td><?= $row['taluka'] ?></td>
<td><?= $row['complaint_type'] ?></td>
<td><?= $row['complaint'] ?></td>

<td>
<?php if($row['image']){ 
$path = "uploads/".basename($row['image']); ?>
<a class="image-link" onclick="openModal('<?= $path ?>')">View</a>
<?php } else echo "No Image"; ?>
</td>



<td><?= $row['complaint_date'] ?></td>

<td class="status-pending"><?= $row['solve_status'] ?></td>

<td>
<a class="btn solved" href="set_status.php?id=<?= $row['id'] ?>&status=Solved">
 Solved
</a>

<a class="btn delete" href="delete_complaint.php?id=<?= $row['id'] ?>"
onclick="return confirm('Delete this complaint?');">
🗑 Delete
</a>
</td>
</tr>
<?php } ?>
</table>

<!-- IMAGE MODAL -->
<div id="imgModal" class="modal">
<span class="close" onclick="closeModal()">&times;</span>
<img id="modalImg" class="modal-content">
</div>

<script>
function openModal(src){
document.getElementById("modalImg").src=src;
document.getElementById("imgModal").style.display="block";
}
function closeModal(){
document.getElementById("imgModal").style.display="none";
}
window.onclick=function(e){
if(e.target==document.getElementById("imgModal")){
closeModal();
}
}
</script>

</body>
</html>
