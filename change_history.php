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

// Get item ID from request safely using prepared statements
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Prepare and execute the statement to get change history
$stmt = $conn->prepare("SELECT changed_by, change_date, change_time FROM change_history WHERE item_id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();

// Bind the result to variables
$stmt->bind_result($changed_by, $change_date, $change_time);

$changes = [];
while ($stmt->fetch()) {
    // Collect all change records
    $changes[] = [
        'changed_by' => $changed_by,
        'change_date' => $change_date,
        'change_time' => $change_time
    ];
}

// Check if no changes were found
if (empty($changes)) {
    error_log("No changes found for item ID: $item_id");
}

// Return JSON response with all changes
header('Content-Type: application/json');
echo json_encode($changes);

$stmt->close();
$conn->close();
?>
