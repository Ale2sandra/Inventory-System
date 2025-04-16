<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
    SELECT u.userid, u.firstname, u.lastname, u.email 
    FROM users u
    WHERE u.accesslevel IN ('', '', '')
";

$users = [];
try {
    // Prepare and execute the SQL statement
    $stmt = $conn->query($sql);

    // Fetch all rows
    if ($stmt) {
        while ($row = $stmt->fetch_assoc()) {  // Corrected method to fetch rows
            $users[] = [
                'fullname' => htmlspecialchars($row["firstname"] . " " . $row["lastname"]),
                'email' => htmlspecialchars($row["email"]),
                'userid' => htmlspecialchars($row["userid"])
            ];
        }
    } else {
        // Error handling if the query fails
        echo json_encode(['error' => 'Database query failed']);
        exit;  // Stop further execution
    }
} catch (Exception $e) {
    // Catch any exceptions and log them
    error_log("Query failed: " . $e->getMessage());
    echo json_encode(['error' => 'Error occurred: ' . $e->getMessage()]);
    exit;  // Stop further execution
}

// Return the result as a JSON response
header('Content-Type: application/json');
echo json_encode($users);
?>
