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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = $_POST['item'];
    $price = $_POST['price'];
    $for_who = $_POST['for_who'];
    $quantity = $_POST['quantity'];
    $supplier = $_POST['supplier'];
    $data = $_POST['data'];
    $from_who = $_POST['from_who'];
    $send_to = $_POST['send_to'];
    $comment = $_POST['comment'];

    // Image processing (if needed)
    $image = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/$image");
    }


    // Insert into database
    $stmt = $db->prepare("INSERT INTO inventory (item, price, for_who, quantity, supplier, data, from_who, send_to, comment, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdisssssss", $item, $price, $for_who, $quantity, $supplier, $data, $from_who, $send_to, $comment, $image);

    if ($stmt->execute()) {
        echo "Data submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $db->close();
}
?>
