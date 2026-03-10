<?php
session_start();
include 'connection.php';
if(isset($_POST['dashboard.php']))

/* ---------- TOTAL VISITORS ---------- */
$visitorQuery = "SELECT COUNT(*) AS total_visitors FROM farmer_visits";
$visitorResult = mysqli_query($conn, $visitorQuery);
$visitorRow = mysqli_fetch_assoc($visitorResult);
$totalVisitors = $visitorRow['total_visitors'];

/* ---------- REGISTERED FARMERS ---------- */
$farmersQuery = "SELECT COUNT(*) AS total_farmers FROM register";
$farmersResult = mysqli_query($conn, $farmersQuery);
$farmersRow = mysqli_fetch_assoc($farmersResult);
$totalFarmers = $farmersRow['total_farmers'];

/* ---------- COMPLAINTS ---------- */
$complaintQuery = "SELECT COUNT(*) AS total_complaints FROM complaints";
$complaintResult = mysqli_query($conn, $complaintQuery);
$complaintRow = mysqli_fetch_assoc($complaintResult);
$totalComplaints = $complaintRow['total_complaints'];
?>