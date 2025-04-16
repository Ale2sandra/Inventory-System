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

// Log successful connection
error_log("Connected successfully");

// Check if the POST variables are set
if (isset($_POST['id']) && isset($_POST['datetime'])) {
    $id = $_POST['id'];
    
    // Get user ID and full name from session
    $userIdDb = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';
    $fullname = isset($_SESSION['login_session']) ? $_SESSION['login_session'] : '';
    
    // Log user ID and full name for debugging
    error_log("User ID: " . $userIdDb);
    error_log("Full Name: " . $fullname);
    
    // If the user ID or full name is not set, handle that case
    if (empty($userIdDb) || empty($fullname)) {
        echo 'error: User not logged in or user details missing';
        exit;
    }

    // Use the full name as the changed_by value
    $changedBy = $fullname; // You can also use $userIdDb if needed
    $dateTime = $_POST['datetime'];

    // Log incoming POST data
    error_log("POST data: " . print_r($_POST, true));

    // Update query
    // $sql = "UPDATE inventory SET changed_by = ?, updated_at = ? WHERE id = ?";
    $sql = "UPDATE change_history SET changed_by = ?, change_time = ?, change_date = ? WHERE item_id = ?";
    
    // Prepare and bind
    $stmt = $conn->prepare($sql);
    
    // Check if prepare was successful
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }
    
    // Bind the parameters
    $stmt->bind_param("ssi", $changedBy, $dateTime, $id);

    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo 'success';
        } else {
            echo 'error: No rows updated. Check if the ID exists.';
        }
    } else {
        echo 'error: ' . htmlspecialchars($stmt->error);
    }

    $stmt->close();
} else {
    echo 'error: Missing POST parameter';
}

// Close the database connection
$conn->close();
?>
