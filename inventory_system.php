<?php
// Start session
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if($_SESSION['isclient']){
    header("Location: clientbalance.php");	
}

$userIdDb = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';
$fullname = isset($_SESSION['login_session']) ? $_SESSION['login_session'] : '';
// $username= isset($_SESSION['useremail'];
$username = isset($_SESSION['useremail']) ? $_SESSION['useremail'] : '';

// // Debug by outputting the values
// echo "User ID: " . htmlspecialchars($userIdDb) . "<br>";
// echo "Full Name: " . htmlspecialchars($fullname) . "<br>";
// echo "user  Name: " . htmlspecialchars($username) . "<br>";
// Check if session variables are empty
if (empty($userIdDb)) {
    echo "User ID is missing from the session.<br>";
}
if (empty($fullname)) {
    echo "Full Name is missing from the session.<br>";
}
?> 


<!DOCTYPE html>
<html>
<head>

    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Table</title> 
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css"> -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.9.4/css/jquery.dataTables.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
     <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"> -->
     <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css"> 
   
 
   
    <?php include("head.php"); ?>
     <style>
.table {
    width: 100%;
    border-collapse: collapse;
    margin: 25px 0;
    font-size: 0.9em;
    font-family: sans-serif;
    min-width: 400px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}

.table th,
.table td {
    padding: 12px 15px;
    text-align: left;
    border: 1px solid #dddddd;
}

.table th {
    background-color: #8fce91;
    color: #000000;
    text-align: left;
}

.table tbody tr {
    border-bottom: 1px solid #dddddd;
}

.table tbody tr:nth-of-type(even) {
    background-color: #f3f3f3;
}

.table tbody tr:last-of-type {
    border-bottom: 2px  #8fce91;
}

.table tbody tr.active-row {
    font-weight: bold;
    color: #8fce91;
}
.button-container {
    display: flex;
    gap: 10px; /* Adjust the space between buttons */
}

.button-container button {
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.button-container .accept {
    background-color: #4CAF50; /* Green */
    color: white;
}

.button-container .reject {
    background-color: #f44336; /* Red */
    color: white;
}
</style>


</head>
<body>
    <?php include("navigation.php"); ?>
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="editModalLabel">Edit Item</h1>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>×</span>
                    </button>
                </div>
                <div class="modal-body">
                <form id="editForm" action="https://billing.protech.com.al/billing-system/api/v1/Inventory_system/update_item.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="editId" name="id">
                        <div class="form-group">
        <label for="editItem">Item</label>
        <input type="text" class="form-control" id="editItem" name="item" placeholder="Enter item name">
    </div>
                        <div class="form-group">
        <label for="editPrice">Price</label>
        <input type="number" class="form-control" id="editPrice" name="price" placeholder="Enter price">
    </div>
                        <div class="form-group">
        <label for="editForWho">For Who</label>
        <input type="text" class="form-control" id="editForWho" name="for_who" placeholder="Enter person name">
    </div>
                        <div class="form-group">
        <label for="editQuantity">Quantity</label>
        <input type="number" class="form-control" id="editQuantity" name="quantity" placeholder="Enter quantity">
    </div>
                        <div class="form-group">
        <label for="editSupplier">Supplier</label>
        <input type="text" class="form-control" id="editSupplier" name="supplier" placeholder="Enter supplier name">
    </div>
                        <div class="form-group">
        <label for="editData">Date</label>
        <input type="date" class="form-control" id="editData" name="data" placeholder="Select date">
    </div>
                        <div class="form-group">
        <label for="editFromWho">From Who</label>
        <input type="text" class="form-control" id="editFromWho" name="from_who" placeholder="Enter your name">
    </div>
                        <div class="form-group">
        <label for="editComment">Comment</label>
        <input type="text" class="form-control" id="editComment" name="comment" placeholder="Enter comment">
    </div>
                        <div class="form-group">
        <label for="editSerialNumber">Serial Number</label>
        <input type="number" class="form-control" id="editSerialNumber" name="serial_number" placeholder="Enter serial number">
    </div>
                        <div class="form-group">
        <label for="editUsedBy">Used By</label>
        <input type="text" class="form-control" id="editUsedBy" name="used_by" placeholder="Enter the user" required>
    </div>
                        <div class="form-group">
        <label for="editAmount">Amount</label>
        <input type="number" class="form-control" id="editAmount" name="amount">
    </div>
                        <div class="form-group">
        <label for="editInvoiceNumber">Invoice Number</label>
        <input type="number" class="form-control" id="editInvoiceNumber" name="invoice_number" placeholder="Enter invoice number">
    </div>
    <div class="form-group">
    <label for="editPaid">Paid</label>
    <select class="form-control" id="editPaid" name="paid">
       
        <option value="Yes">Yes</option>
        <option value="No">No</option>
    </select>
</div>
                        <input type="hidden" class="form-control" id="status" name="status" value="<?php echo htmlspecialchars($fullname); ?>">
                        <input type="hidden" class="form-control" id="userId" name="user_id" value="<?php echo htmlspecialchars($userIdDb); ?>">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
  <div class="container mt-8" style="text-align: center;">
    <h2 class="far fa-clipboard"> Inventory Table</h2>
    <button id="openModalBtn" class="fas fa-info-circle" title="Click on Show Instructions button" style="font-size: 20px; cursor: pointer; right: 50px;"></button>
 <!-- Instructions Modal -->
 <div id="instructionsModal" class="modal" style="display:none;">
        <div class="modal-content" style="max-width: 70%; margin: auto; padding: 25px; border-radius: 10px; background-color: #fff; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
            <span id="closeModal" class="close" style="font-size: 30px; color: #333; cursor: pointer;">&times;</span>
            <h1 style="font-size: 28px; color: #333; font-weight: bold;">Instructions</h1>
            <p style="font-size: 16px; line-height: 1.6; color: #555;">
                <strong> Welcome to the Inventory Table! Here are some guidelines to help you understand and make the most of the available features:</strong>
            </p>
            
            <ul style="font-size: 16px; line-height: 1.8; color: #555;">
            <li>The Inventory Table provides comprehensive information about all products, including details such as the requester, the intended recipient, and additional relevant data.</li>
            <li>The table supports advanced search functionalities, allowing you to filter results by individual column headers or perform a general search across all data.</li>
            <li>Each row includes Edit and Delete options, enabling you to modify or remove entries as needed. The <strong>Used By</strong> column specifies where the requested product will be allocated (e.g., 1st, 2nd, or 3rd floor).</li>
            <li>Editing can be done by any person who logs into this system.</li>
            <li>The Choose column includes Accept and Reject buttons. These actions can <strong>only</strong> be performed by the recipient designated in the Request Form email. Once the recipient accepts or rejects the request, an automated email is sent to the requester (the individual responsible for managing the system) notifying them of the decision.</li>
            <li>To export the table data, simply click the Export to Excel button, which allows you to download and save the current table data in Excel format.</li>
            <li>The view changes button shows all edits and changes made to the table for each row.</li>
        </ul>

            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; margin-top: 30px;">
                <div style="width: 45%; font-size: 16px; color: #555;">
                    <h2 style="font-size: 22px; color: #333; font-weight: bold;">Closing the Modal</h2>
                    <p>You can close this instructions modal by clicking the <strong>"×"</strong> button in the top-right corner.</p>
                </div>

                <div style="width: 45%; font-size: 16px; color: #555;">
                    <h2 style="font-size: 22px; color: #333; font-weight: bold;">Additional Help</h2>
                    <p>If you have any questions or need further assistance, please refer to the documentation or contact your system administrator for more guidance.</p>
                </div>
            </div>
        </div>
    </div>
        <!-- <div style="display: flex; gap: 50px; align-items: center; margin-bottom: 1rem; justify-content: flex-start;"> -->
   
        <button id="exportButton" class="btn btn-success" style="margin-left: 70px;">Export to Excel</button>
        </div> 

        <div class="table-container">
        <table class="table" id="myTable">
        <thead>
    <tr>
        <th>ID</th>
        <th>Item</th>
        <th>Price</th>
        <th>For Who</th>
        <th>Quantity</th>
        <th>Supplier</th>
        <th>Data</th>
        <th>From Who</th>
        <th>Image</th>
        <th>Comment</th>
        <th>Serial Number</th>
        <th>Status</th>
        <th>Used By</th>
        <th>Amount</th>
        <th>Invoice Number</th>
        <th>Paid</th>
        <th>Actions</th>
        <th>Decision</th>
        <th>Choose</th>
    </tr>
</thead>
<thead>
    <tr>
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search ID"></th>
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search Item"></th>
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search Price"></th>
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search For Who"></th>
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search Quantity"></th>
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search Supplier"></th>
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search Data"></th>
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search From Who"></th>
       
        <th></th> <!-- No search for Image -->
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search Comment"></th>
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search Serial Number"></th>
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search Status"></th>
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search Used By"></th>
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search Amount"></th>
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search Invoice Number"></th>
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search Paid"></th>
        <th></th> <!-- No search for Actions -->
        <th><input type="text" class="form-control form-control-sm search-input" placeholder="Search Decision"></th>
        <th></th> <!-- No search for Choose -->
    </tr>
</thead>

            <tbody>
            <?php
        
 // Database connection
 $dbHost = "localhost";
$dbUsername = "erald";
$dbPassword = "erald1232!";
$dbName = "asterisk";

// Create database connection
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Fetch all data from the inventory table
$sql = "SELECT id, item, price, for_who, quantity, supplier, data, from_who, image, comment, serial_number,  used_by, amount, invoice_number, paid,  decision FROM inventory";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $changedBy = isset($row['changed_by']) ? $row['changed_by'] : 'N/A';
        $changeDate = isset($row['change_date']) ? $row['change_date'] : 'N/A';
        $changeTime = isset($row['change_time']) ? $row['change_time'] : 'N/A';
      $currentPage = isset($_GET['page']) ? $_GET['page'] : 3;

        // Combine these into the status
        $status = "{$changedBy}<br>{$changeDate}<br>{$changeTime}";

        echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['item']}</td>
        <td>{$row['price']}</td>
        <td>{$row['for_who']}</td>
        <td>{$row['quantity']}</td>
        <td>{$row['supplier']}</td>
        <td>{$row['data']}</td>
        <td>{$row['from_who']}</td>
        <td>";

 
    if (!empty($row['image'])) {
        echo "<a href='{$row['image']}' target='_blank'>
                <img src='{$row['image']}' alt='Image of {$row['item']}' style='width: 100px; height: auto;' title = 'Click this image'>
              </a>";
    } else {
      
        echo " ";
    }

    echo "</td>
        <td>{$row['comment']}</td>
        <td>{$row['serial_number']}</td>
        <td>
            <button class='btn btn-info btn-sm' onclick='viewChanges({$row['id']})' title='Click view changes'>
                View Changes
            </button>
        </td>
        <td>{$row['used_by']}</td>
        <td>{$row['amount']}</td>
        <td>{$row['invoice_number']}</td>
        <td>{$row['paid']}</td>
     <td>
                                <div class='action-buttons'>
                                    <br><button class='btn btn-warning btn-sm' onclick='openEditModal({$row['id']})' title='Edit this item'>
                                        <i class='fas fa-edit'></i>
                                    </button></br>
                                    <br><a href='api/v1/Inventory_system/delete.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this item?\");' title='Delete this item'>
                                        <i class='fas fa-trash-alt'></i>
                                    </a></br>
                                </div>
                            </td>
        <td class='decision-column'>{$row['decision']}</td>
        <td class='action-buttons'>
            <br><a href='#' class='btn btn-success btn-sm accept-btn' data-id='{$row['id']}' style='margin-right: 5px;' title='Click Accept' >
                <i class='fas fa-check'></i> Accept
            </a></br>
            <br><a href='#' class='btn btn-danger btn-sm reject-btn' data-id='{$row['id']}' title='Click Reject'>
                <i class='fas fa-times'></i> Reject
            </a></br>
        </td>
    </tr>";

    }
} else {
    echo "<tr><td colspan='17'>No records found.</td></tr>";
}

$conn->close();
            ?>
            </tbody>
        </table>
        </div>
        </div>



       <div id="changeHistoryModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="changeHistoryModalLabel" inert="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="changeHistoryModalLabel">Change History</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeChangeHistoryModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Changed By</th>
                            <th>Change Date</th>
                            <th>Change Time</th>
                        </tr>
                    </thead>
                    <tbody id="changeHistoryBody">
                        <!-- Rows will be dynamically added here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeChangeHistoryModal()">Close</button>
            </div>
        </div>
    </div>
</div>



  
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> 

  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  
  
 
 <script>

window.onload = function() {
  updatePerformanceSectionVisibility(); // Call the function to initialize visibility
};
// JavaScript to handle modal opening and closing
 document.getElementById('openModalBtn').onclick = function() {
        document.getElementById('instructionsModal').style.display = "block";
    }

    document.getElementById('closeModal').onclick = function() {
        document.getElementById('instructionsModal').style.display = "none";
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target == document.getElementById('instructionsModal')) {
            document.getElementById('instructionsModal').style.display = "none";
        }
    }



    $(document).ready(function() {
    // Initialize DataTables
    var table = $('#myTable').DataTable({
        "paging": true,
        "responsive": true,
        "lengthMenu": [10, 25, 50, 100],
        "pageLength": 10,
        "ordering": true,
        "info": true,
        "searching": true,
        "autoWidth": false,
        "order": [[6, 'desc']], // Ensure column 6 (date) sorts in descending order initially
        "columnDefs": [
            {
                "targets": 6, // Change this to your actual date column index
                "type": "date" // Ensure it's recognized as a date column
            }
        ]
    });

    // Restore the saved page number from local storage
    var savedPage = localStorage.getItem('currentPage');
    if (savedPage) {
        table.page(parseInt(savedPage)).draw(false); // Set the DataTable to the saved page
        localStorage.removeItem('currentPage'); // Clear the saved page number
    }

    // Custom search for each column using DataTables API
    $('#myTable thead input').on('input', function() {
        var index = $(this).closest('th').index(); // Get column index
        var searchValue = this.value.trim();

        // For ID search, apply exact match
        if (index === 0) { // Assuming ID is in the first column (index 0)
            table.column(index).search('^' + searchValue + '$', true, false).draw(); // Exact match
        } else {
            table.column(index).search(searchValue).draw(); // Regular search for other columns
        }
    });
});
// Define the openEditModal function in the global scope
function openEditModal(id) {
            console.log("Fetching data for item ID:", id);

            if (!id || isNaN(id)) {
                console.error("Invalid ID:", id);
                return;
            }

            $.ajax({
                url: 'https://billing.protech.com.al/billing-system/api/v1/Inventory_system/get_item.php',
                type: 'GET',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    // Validate the response
                    if (!response || typeof response !== 'object') {
                        console.error("Invalid response format:", response);
                        alert("Unexpected response from the server. Please try again.");
                        return;
                    }

                    if (!response.success) {
                        console.error("Server Error: " + (response.message || "Unknown error."));
                        alert(response.message || "Failed to fetch item details. Please try again.");
                        return;
                    }

                    // Validate and populate data
                    const data = response.data;
                    if (data && data.id) {
                        $('#editId').val(data.id || '');
                        $('#editItem').val(data.item || '');
                        $('#editPrice').val(data.price || '');
                        $('#editForWho').val(data.for_who || '');
                        $('#editQuantity').val(data.quantity || '');
                        $('#editSupplier').val(data.supplier || '');
                        $('#editData').val(data.data || '');
                        $('#editFromWho').val(data.from_who || '');
                        $('#editComment').val(data.comment || '');
                        $('#editSerialNumber').val(data.serial_number || '');
                        $('#editUsedBy').val(data.used_by || '');
                        $('#editAmount').val(data.amount || '');
                        $('#editInvoiceNumber').val(data.invoice_number || '');
                        $('#editPaid').val(data.paid || '');

                        // Show the modal if it exists
                        const modal = $('#editModal');
                        if (modal.length) {
                            modal.modal('show');
                        } else {
                            console.error("Edit modal not found in the DOM.");
                        }
                    } else {
                        console.error("Invalid or incomplete data received:", response);
                        alert("Failed to retrieve item details. Please check the server response.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", error);
                    console.error("Response:", xhr.responseText);
                    alert("An error occurred while fetching item details. Please try again.");
                }
            });
        }
        $(document).ready(function () {
    $('#editForm').submit(function (event) {
        event.preventDefault(); // Prevent normal form submission

        // Gather the data from the form
        const formData = new FormData(this); // Use FormData to handle file uploads and form data

        $.ajax({
            url: 'https://billing.protech.com.al/billing-system/api/v1/Inventory_system/update_item.php',
            type: 'POST',
            data: formData,
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Prevent jQuery from setting the content-type header automatically
            dataType: 'json', // Expect JSON response
            beforeSend: function () {
                // Optional: Show loading indicator before the request is sent
                $('#loadingIndicator').show();
            },
            success: function (response) {
                // Check for successful response
                if (response.success) {
                    alert('Item updated successfully!');
                    $('#editModal').modal('hide'); // Close the modal on success
                    location.reload(); // Reload the page to see updates
                } else {
                    // Handle any errors returned from the server
                    alert('Error: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                // Log error details and display a simple alert
                console.error("AJAX error details:", xhr.responseText);
                alert('Error updating item. Please try again.');
            },
            complete: function () {
                // Optional: Hide loading indicator after the request is completed
                $('#loadingIndicator').hide();
            }
        });
    });
});


// Handle status change form submission
$('#saveStatusChanges').on('click', function () {
    var formData = $('#statusChangeForm').serialize();

    $.ajax({
        url: 'api/v1/Inventory_system/update_status.php', // A PHP script to handle updating the status
        type: 'POST',
        data: formData,
        success: function (response) {
            alert('Status updated successfully!');
            location.reload(); // Reload the page to reflect changes
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
});
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#myTable').DataTable();

    // Delete button functionality
    $(document).on('click', '.delete-btn', function () {
        var button = $(this); // Store reference to the button
        var id = button.data('id'); // Get the ID from data attribute

        if (confirm("Are you sure you want to delete this record?")) {
            $.ajax({
                url: 'api/v1/Inventory_system/delete.php', // Your delete handler script
                type: 'POST', // Use POST for deletion
                data: { id: id }, // Send the ID of the record to delete
                success: function (response) {
                    // Remove the row from the DataTable
                    var row = button.closest('tr'); // Get the closest row
                    table.row(row).remove().draw(false); // Remove the row without changing the page

                    alert('Record deleted successfully!');
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    alert("Error deleting record.");
                }
            });
        }
    });
});

  
      // Function to export table data to Excel
$('#exportButton').on('click', function() {
    // Fetch all data from the server
    $.ajax({
        url: 'api/v1/Inventory_system/export.php', // URL of the new PHP script
        method: 'GET',
        success: function(data) {
            // Convert the fetched data to a format suitable for XLSX
            var worksheet = XLSX.utils.json_to_sheet(data);
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, worksheet, "Inventory Data");
            // Export to Excel
            XLSX.writeFile(wb, "inventory_data.xlsx");
        },
        error: function(xhr, status, error) {
            console.error("Error fetching data: ", error);
            alert("An error occurred while fetching the data.");
        }
    });
});
  // Search functionality
  $('.search-input').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            var index = $(this).parent().index();
            $('tbody tr').filter(function() {
                $(this).toggle($(this).find('td').eq(index).text().toLowerCase().indexOf(value) > -1);
            });
        });

    
        function viewChanges(itemId) {
    // Ensure a valid item ID is provided
    if (!itemId || isNaN(itemId)) {
        console.error("Invalid item ID:", itemId);
        alert("Invalid item ID. Please try again.");
        return;
    }

    // Make an AJAX request to fetch the change history
    $.ajax({
        url: 'https://billing.protech.com.al/billing-system/api/v1/Inventory_system/change_history.php',
        type: 'GET',
        data: { id: itemId },
        dataType: 'json',
        success: function(response) {
            var changeHistoryBody = $('#changeHistoryBody');
            changeHistoryBody.empty(); // Clear previous entries

            if (response.length > 0) {
                // Populate the modal with change history data
                response.forEach(function(change) {
                    changeHistoryBody.append(`
                        <tr>
                            <td>${change.changed_by || 'N/A'}</td>
                            <td>${change.change_date || 'N/A'}</td>
                            <td>${change.change_time || 'N/A'}</td>
                        </tr>
                    `);
                });
            } else {
                // Display a message if no change history is available
                changeHistoryBody.append(`
                    <tr>
                        <td colspan="3" class="text-center">No change history available.</td>
                    </tr>
                `);
            }

            // Show the modal and set inert attribute
            $('#changeHistoryModal').removeAttr('inert').modal('show');
        },
        error: function(xhr, status, error) {
            console.error("Error fetching change history:", error);
            console.error("Response:", xhr.responseText);
            alert('Failed to fetch change history. Please try again later.');
        }
    });
}

// Function to hide the modal and set inert attribute
function closeChangeHistoryModal() {
    $('#changeHistoryModal').modal('hide').attr('inert', 'true');
}

$('.accept-btn, .reject-btn').click(function(event) {
    event.preventDefault();

    // Get the item ID and action type
    let id = $(this).data('id');
    let action = $(this).hasClass('accept-btn') ? 'accept' : 'reject';

    // Show loading indicator
    let button = $(this);
    button.prop('disabled', true).text('Processing...');

    $.ajax({
        url: 'https://billing.protech.com.al/billing-system/api/v1/Inventory_system/update_decision.php',
        type: 'POST',
        dataType: 'json',
        data: { id: id, action: action },
        success: function(response) {
            if (response.success) {
                // Update the decision column in the row
                let decisionCell = button.closest('tr').find('.decision-column');
                decisionCell.text(action === 'accept' ? 'Accepted' : 'Rejected');
                
                alert('Action completed successfully');
            } else {
                alert('Error: ' + response.error);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
            alert('An unexpected error occurred: ' + xhr.responseText);
        },
        complete: function() {
            // Re-enable the button and reset text
            button.prop('disabled', false).text(action.charAt(0).toUpperCase() + action.slice(1));
        }
    });
});
      

    </script>
       <footer>
        <?php include("footer.php"); ?>
    </footer>
</body>
</html>
