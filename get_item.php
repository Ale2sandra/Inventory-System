<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Database connection credentials
$conn = new mysqli('', '', '', '');

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Get the item ID from the request
$itemId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validate the item ID
if ($itemId <= 0) {
    echo json_encode(['success' => false, 'message' => "Invalid item ID"]);
    exit;
}

// Prepare and execute the SQL query
$stmt = $conn->prepare("SELECT id, item, price, for_who, quantity, supplier, data, from_who, comment, serial_number, used_by, amount, invoice_number, paid FROM inventory WHERE id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => "SQL Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $itemId);
$stmt->execute();

// Bind result variables
$stmt->bind_result(
    $id,
    $item,
    $price,
    $for_who,
    $quantity,
    $supplier,
    $data,
    $from_who,
    $comment,
    $serial_number,
    $used_by,
    $amount,
    $invoice_number,
    $paid
);

if ($stmt->fetch()) {
    // Create an associative array of the result
    $result = [
        'id' => $id,
        'item' => $item,
        'price' => $price,
        'for_who' => $for_who,
        'quantity' => $quantity,
        'supplier' => $supplier,
        'data' => $data,
        'from_who' => $from_who,
        'comment' => $comment,
        'serial_number' => $serial_number,
        'used_by' => $used_by,
        'amount' => $amount,
        'invoice_number' => $invoice_number,
        'paid' => $paid
    ];
    echo json_encode(['success' => true, 'data' => $result]);
} else {
    echo json_encode(['success' => false, 'message' => "Item not found."]);
}

// Close connections
$stmt->close();
$conn->close();
?>
