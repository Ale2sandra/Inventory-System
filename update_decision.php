<?php
session_start();

// Enable error reporting for debugging (Remove this in production)
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

$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);  
if ($conn->connect_error) {  
    die("Connection failed: " . $conn->connect_error); // This will provide a clear error message  
}  

// Initialize response
$response = array('success' => false, 'error' => '');

// Get user ID and full name from session
$userIdDb = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';
$fullname = isset($_SESSION['login_session']) ? $_SESSION['login_session'] : '';

// Validate POST request and required parameters
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $action = $_POST['action'];
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);

    // Validate action
    if (!in_array($action, array('accept', 'reject'))) {
        $response['error'] = 'Invalid action';
        echo json_encode($response);
        exit;
    }

    // Validate ID
    if ($id === false || $id <= 0) {
        $response['error'] = 'Invalid item ID';
        echo json_encode($response);
        exit;
    }

    // Fetch item details for the email
    $stmt = $conn->prepare("SELECT * FROM inventory WHERE id = ?");
    if ($stmt === false) {
        $response['error'] = "Failed to prepare query: " . $conn->error;
        error_log("SQL Prepare Error: " . $conn->error); // Log SQL preparation error
        echo json_encode($response);
        exit;
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    // Bind result variables
    $stmt->bind_result($itemId, $itemName, $price, $for_who, $quantity, $supplier, $data, $from_who, $from_who_email, $send_to, $send_to_id, $image, $comment, $serial_number, $used_by, $amount, $invoice_number, $paid, $decision);
    if ($stmt->fetch()) {
        // Create an associative array for the item
        $item = [
            'id' => $itemId,
            'item' => $itemName,
            'price' => $price,
            'for_who' => $for_who,
            'quantity' => $quantity,
            'supplier' => $supplier,
            'data' => $data,
            'from_who' => $from_who,
            'from_who_email' => $from_who_email,
            'send_to' => $send_to,
            'send_to_id' => $send_to_id,
            'image' => $image,
            'comment' => $comment,
            'serial_number' => $serial_number,
            'used_by' => $used_by,
            'amount' => $amount,
            'invoice_number' => $invoice_number,
            'paid' => $paid,
            'decision' => $decision
        ];

        // Initialize variables and handle missing values
        $send_to_id = isset($item['send_to_id']) ? $item['send_to_id'] : '';

        // Authorization check
        if ($userIdDb != $send_to_id) {
            $response['error'] = "You are not authorized to accept/reject this item.";
            echo json_encode($response);
            exit;
        }

        // Check current decision
        $currentDecision = isset($item['decision']) ? $item['decision'] : '';
        if (($action === 'accept' && $currentDecision === 'Accepted') || ($action === 'reject' && $currentDecision === 'Rejected')) {
            $response['error'] = "No change needed. Item is already " . $currentDecision . ".";
            echo json_encode($response);
            exit;
        }

        // Close the SELECT statement after fetching data
        $stmt->close();

        // Prepare SQL statement based on action
        $sql = ($action === 'accept') 
            ? "UPDATE inventory SET decision='Accepted' WHERE id=?" 
            : "UPDATE inventory SET decision='Rejected' WHERE id=?";
        
        $updateStmt = $conn->prepare($sql);
        
        if ($updateStmt === false) {
            $response['error'] = 'Prepare failed: ' . $conn->error;
            error_log("SQL Prepare Error: " . $conn->error); // Log SQL prepare error
            echo json_encode($response);
            exit;
        }

        $updateStmt->bind_param("i", $id);

        if ($updateStmt->execute()) {
            // Send email notification
            $email = isset($item['from_who_email']) ? $item['from_who_email'] : '';
            $CCs = array($email);
            $type = 'sendAlert';
            $subject_email = "IMS-Item Request: " . ($action === 'accept' ? 'Accepted' : 'Rejected');
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
                    <div class='header'><h1>Request Notification</h1></div>
                    <div class='content'>
                        <p>This request has been <strong>" . ($action === 'accept' ? 'Accepted' : 'Rejected') . "</strong>.</p>
                        <table class='table'>
                            <tr><th>ID</th><td>" . htmlspecialchars($item['id']) . "</td></tr>
                            <tr><th>Item</th><td>" . htmlspecialchars($item['item']) . "</td></tr>
                            <tr><th>Price</th><td>" . htmlspecialchars($item['price']) . "</td></tr>
                            <tr><th>For Who</th><td>" . htmlspecialchars($item['for_who']) . "</td></tr>
                            <tr><th>Quantity</th><td>" . htmlspecialchars($item['quantity']) . "</td></tr>
                            <tr><th>Supplier</th><td>" . htmlspecialchars($item['supplier']) . "</td></tr>
                            <tr><th>Data</th><td>" . htmlspecialchars($item['data']) . "</td></tr>
                            <tr><th>From Who Email</th><td>" . htmlspecialchars($item['from_who_email']) . "</td></tr>
                            <tr><th>Comment</th><td>" . htmlspecialchars($item['comment']) . "</td></tr>
                            <tr><th>Image</th><td><img src='" . htmlspecialchars($item['image']) . "' alt='Item Image' style='max-width: 200px; height: auto;'></td></tr>
                        </table>
                    </div>
                </div>
            </body>
            </html>
            ";

            require_once '';
            $emailSent = EmailService::sendEmail($type, $email, $CCs, $subject_email, $emailBody);

            if ($emailSent) {
                $response['success'] = true;
                $response['message'] = "Request submitted and email sent successfully.";
            } else {
                $response['error'] = "Failed to send email.";
                error_log("Email sending failed."); // Log email error
            }
        } else {
            $response['error'] = "Execution failed.";
            error_log("SQL Execute Error: " . $updateStmt->error); // Log SQL execution error
        }

        $updateStmt->close();
    } else {
        $response['error'] = "Item not found.";
        error_log("Item not found for ID: " . $id); // Log item not found error
    }

    $conn->close();
} else {
    $response['error'] = "Invalid request.";
    error_log("Invalid request: POST data not set properly."); // Log invalid request
}

echo json_encode($response);
?>
