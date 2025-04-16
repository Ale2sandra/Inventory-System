<?php
// Include the EmailService class
include("../../../Classes/EmailService.php");
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if($_SESSION['isclient']){
    header("Location:");	
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

var_dump($_POST);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve data from the form
    $item       = mysqli_real_escape_string($conn, $_POST['item']);
    $price      = floatval($_POST['price']); // Ensure it's numeric
    $for_who    = mysqli_real_escape_string($conn, $_POST['for_who']);
    $quantity   = intval($_POST['quantity']); // Ensure it's numeric
    $supplier   = mysqli_real_escape_string($conn, $_POST['supplier']);
    $data       = mysqli_real_escape_string($conn, $_POST['data']);
    $from_who   = mysqli_real_escape_string($conn, $_POST['from_who']);
    $from_who_email   = mysqli_real_escape_string($conn, $_POST['from_who_email']);
    $comment   = mysqli_real_escape_string($conn, $_POST['comment']);
    
    // Check if 'send_to_id' exists in the POST data
    $send_to_id = isset($_POST['send_to_id']) ? intval($_POST['send_to_id']) : 0; // Default to 0 if not set
    
    // Handle 'send_to' as an array
    $sendTo = [];
    if (isset($_POST['send_to']) && is_array($_POST['send_to'])) {
        foreach ($_POST['send_to'] as $recipient) {
            if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                $sendTo[] = mysqli_real_escape_string($conn, $recipient);
            }
        }
    }
    $sendToFormatted = implode(", ", $sendTo);

    // Optional: Handle image upload (if applicable)
    $uploadedFilePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = ''; // Change this to your actual local upload directory
        $uploadFile = $uploadDir . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $uploadedFilePath = 'https://' . basename($_FILES['image']['name']);
        } else {
            $uploadedFilePath = ''; // Handle the error appropriately
        }
    }

    
    // SQL insert statement
    $insertQuery = "INSERT INTO inventory (item, price, for_who, quantity, supplier, data, from_who, from_who_email, send_to, send_to_id, image, comment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Prepare and bind
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sdsssssssiss", $item, $price, $for_who, $quantity, $supplier, $data, $from_who, $from_who_email, $sendToFormatted, $send_to_id, $uploadedFilePath, $comment);

    // Execute the insert
    if ($stmt->execute()) {
        $email = "your_css_email@example.com";
        $type = '';
        $CCs = []; // Define CCs as an empty array if not used
        $subject_email = "";

        // Build the URL for the review link
        $reviewLink = 'https://';
        // Define accept and reject URLs
        $imageLink = $uploadedFilePath;

        // Enhanced email body with inline CSS for button styling
        $emailBody = "
        <html>
        <head>
        <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0; }
        .container { width: 80%; margin: auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 10px; border-bottom: 2px solid #007bff; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 20px; }
        .content p { margin: 10px 0; }
        .table { width: 60%; border-collapse: collapse; margin: 20px 0; }
        .table th, .table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .footer { background-color: #f8f9fa; padding: 10px; text-align: center; }
        .button { padding: 10px 15px; background-color: white; color: white; text-decoration: none; border-radius: 5px; }
        .button:hover { background-color: white; } 
        </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Request Notification</h1>
                </div>
                <div class='content'>
                    <p>Dear Sir/Madam,</p>
                    <p>This email is to notify you that the following request has been submitted.</p>
                    <p><strong>Request Details:</strong></p>
                    <table class='table'>
                        <tr>
                            <th>Item</th>
                            <td>" . htmlspecialchars($item) . "</td>
                        </tr>
                        <tr>
                            <th>Price</th>
                            <td>" . htmlspecialchars($price) . "</td>
                        </tr>
                        <tr>
                            <th>For Who</th>
                            <td>" . htmlspecialchars($for_who) . "</td>
                        </tr>
                        <tr>
                            <th>Quantity</th>
                            <td>" . htmlspecialchars($quantity) . "</td>
                        </tr>
                        <tr>
                            <th>Supplier</th>
                            <td>" . htmlspecialchars($supplier) . "</td>
                        </tr>
                        <tr>
                            <th>Data</th>
                            <td>" . htmlspecialchars($data) . "</td>
                        </tr>
                        <tr>
                            <th>From Who</th>
                            <td>" . htmlspecialchars($from_who) . "</td>
                        </tr>
                        <tr>
                            <th>Comment</th>
                            <td>" . htmlspecialchars($comment) . "</td>
                        </tr>
                        " . ($uploadedFilePath ? "<tr><th>Image</th><td><a href='" . $imageLink . "'>View Image</a></td></tr>" : "") . "
                    </table>
                    <p><strong>Sent To:</strong> " . htmlspecialchars($sendToFormatted) . "</p> 
                </div>
                <div class='footer'>
                                    <p>Please make a decision Accept/Reject.</p>
                  <a href='https://' class='button'>Go to Inventory System</a>
                </div>
            </div>
        </body>
        </html>";

        require_once ''; 
        
        $emailSent = true; // Initialize as true
        foreach ($sendTo as $recipient) {
            $emailSent = $emailSent && EmailService::sendEmail($type, $recipient, $CCs, $subject_email, $emailBody);
        }

        if ($emailSent) {
            header("Location:inventory_index.php?success=" . urlencode("Request submitted and emails sent successfully."));
            exit(); 
        } else {
            header("Location:inventory_index.php?error=" . urlencode("Failed to send email to all recipients."));
            exit();
        }
    } else {
        header("Location:inventory_index.php?error=" . urlencode("Failed to submit request to the database: " . $stmt->error));
        exit();
    }
} else {
    header("Location:inventory_index.php?error=" . urlencode("Invalid request method."));
    exit();
}
?>
