<?php

// Start session to access session variables
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
if($_SESSION['isclient']){
    header("Location: ");	
}
// Database connection
$dbHost = "";
$dbUsername = "";
$dbPassword = "";
$dbName = "";

// Create database connection
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Adjusted SQL query to use correct column names
$sql = "
    SELECT u.firstname, u.lastname
    FROM users u
    WHERE u.accesslevel IN ('', '', '')
";

$users = [];
try {
    // Prepare and execute the SQL statement
    $stmt = $conn->query($sql);

    // Fetch all rows
    if ($stmt) {
        while ($row = $stmt->fetch_assoc()) {
            $users[] = [
                'fullname' => htmlspecialchars($row["firstname"] . " " . $row["lastname"])
            ];
        }
    } else {
        $errorInfo = $conn->errorInfo();
        error_log("Query failed: " . $errorInfo[2]);
        echo "Error: " . $errorInfo[2];
    }
} catch (PDOException $e) {
    error_log("Query failed: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($users);
?>
