<?php
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

// Check if the ID is set in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the delete query
    $sql = "DELETE FROM inventory WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Record deleted successfully
        echo "Record deleted successfully.";
    } else {
        // Error deleting record
        echo "Error deleting record: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "ID not provided.";
}

// Close the database connection
$conn->close();

// Retrieve the current page from localStorage or default to page 1
// $page = isset($_GET['page']) ? $_GET['page'] : 1; // Default to page 1 if not set

// Redirect back to the inventory page with the current page number
header("Location: inventory_system.php"); 
exit;
?>
