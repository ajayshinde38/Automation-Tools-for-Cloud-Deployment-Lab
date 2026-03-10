<?php
session_start();
include 'connection.php';

$query = "SELECT * FROM register ORDER BY id ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Registered Farmers</title>

<style>
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}
th, td {
    padding: 8px;
    border: 1px solid #ccc;
    text-align: center;
}
th {
    background-color: #2c5364;
    color: white;
}
</style>
</head>

<body>

<h2>Registered Farmers</h2>

<table>
<tr>
    <th>ID</th>
    <th>Farmer Name</th>
    <th>Email</th>   <!-- NEW COLUMN -->
    <th>Password</th>
    <th>Mobile</th>
    <th>Role</th>
    <th>Created at</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= $row['id']; ?></td>
    <td><?= $row['name']; ?></td>
    <td><?= $row['email']; ?></td>   <!-- EMAIL DATA -->
    <td><?= $row['password']; ?></td>
    <td><?= $row['mobile']; ?></td>
    <td><?= $row['role']; ?></td>
    <td><?= $row['created_at']; ?></td>
</tr>
<?php } ?>

</table>

</body>
</html>
