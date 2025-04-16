<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1); 
// Database connection
$dbHost = "";
$dbUsername = "";
$dbPassword = "";
$dbName = "";      

$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if required data is set
if (isset($_POST['id']) && isset($_POST['changed_by']) && isset($_POST['change_date']) && isset($_POST['change_time'])) {
    $itemId = $conn->real_escape_string($_POST['id']);
    $changedBy = $conn->real_escape_string($_POST['changed_by']);
    $changeDate = $conn->real_escape_string($_POST['change_date']);
    $changeTime = $conn->real_escape_string($_POST['change_time']);

    // Insert the change record into the 'change_history' table
    $sql = "INSERT INTO  (item_id, changed_by, change_date, change_time)
            VALUES ('$itemId', '$changedBy', '$changeDate', '$changeTime')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Required fields are missing.']);
}

$conn->close();
?>

