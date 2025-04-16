<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if($_SESSION['isclient']){
    header("Location: ");	
}
// Database connection details
$dbHost     = "";
$dbUsername = "";
$dbPassword = "";
$dbName     = "";

// Create database connection
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all data from the inventory table

$query = "SELECT id, item, price, for_who, quantity, supplier, data, from_who, image, comment, serial_number,  used_by, amount, invoice_number, paid,  decision FROM inventory";
$result = $conn->query($query);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Close the database connection
$conn->close();

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
