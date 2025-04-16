<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
if($_SESSION['isclient']){
    header("Location: ");	
}
// Step 2: Database connection details
$dbHost = "";
$dbUsername = "";
$dbPassword = "";
$dbName = "";

// Create database connection
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Step 3: Check database connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Ensure that the session contains the required data
$fullname = isset($_SESSION['login_session']) ? $_SESSION['login_session'] : 'unknown';
$userIdDb = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';

// Check if the user is logged in and has the necessary session values
if (empty($fullname) || empty($userIdDb)) {
    echo json_encode(['success' => false, 'message' => "User not logged in or session data missing."]);
    exit;
}

// Step 4: Collect and sanitize POST data
$data = array_map('trim', $_POST);
$itemId = isset($data['id']) ? intval($data['id']) : 0;

// Step 5: Validate the item ID
if ($itemId <= 0) {
    echo json_encode(['success' => false, 'message' => "Invalid item ID."]);
    exit;
}

// Step 6: Prepare SQL fields to update
$fields = [];
foreach (['item', 'price', 'for_who', 'quantity', 'supplier', 'comment', 'serial_number', 'used_by', 'amount', 'invoice_number', 'data', 'from_who', 'paid', 'decision'] as $field) {
    if (isset($data[$field]) && $data[$field] !== '') {
        $fields[] = "$field = '" . $conn->real_escape_string($data[$field]) . "'";
    }
}

if (empty($fields)) {
    echo json_encode(['success' => false, 'message' => "No fields to update."]);
    exit;
}

// Step 7: Build the update SQL query
$sql = "UPDATE inventory SET " . implode(", ", $fields) . " WHERE id = $itemId";

// Step 8: Execute the update query
if (!$conn->query($sql)) {
    echo json_encode(['success' => false, 'message' => "Update failed: " . $conn->error]);
    exit;
}

// Step 9: Log the change to change_history table
$changed_by = $fullname; // Get the full name from session
$changed_by = $conn->real_escape_string($changed_by); // Sanitize the username

$historySql = "INSERT INTO change_history (item_id, changed_by, change_date, change_time) 
               VALUES ($itemId, '$changed_by', NOW(), NOW())";

// Execute the history query
if (!$conn->query($historySql)) {
    echo json_encode(['success' => false, 'message' => "Failed to insert history record: " . $conn->error]);
    exit;
}

// Step 10: Return success response
echo json_encode(['success' => true, 'message' => "Record updated successfully."]);

// Step 11: Close the connection
$conn->close();

?>
