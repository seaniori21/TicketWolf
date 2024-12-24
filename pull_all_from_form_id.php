<?php
include('functions/conn_db.php');
// echo "Connected successfully!<br><br>";

// Get form_id from website link
$form_id = isset($_GET['form_id']) ? $_GET['form_id'] : null;

if ($form_id) {
    // Fetch ticket data from tickets table
    $stmt = $conn->prepare("SELECT * FROM tickets WHERE form_id = ?");
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("i", $form_id);
    $stmt->execute();

    // Fetch the result for ticket
    $ticket_result = $stmt->get_result();

    if ($ticket_result->num_rows > 0) {
        // Fetch the row as an associative array
        $ticket_data = $ticket_result->fetch_assoc();



        $queryAllFiles = "
            SELECT 'insurance' AS file_group, insurance_files.* FROM insurance_files WHERE form_id = ?
            UNION ALL
            SELECT 'title' AS file_group, title_files.* FROM title_files WHERE form_id = ?
            UNION ALL
            SELECT 'license' AS file_group, license_files.* FROM license_files WHERE form_id = ?
        ";

        // Fetch all files related to this form_id
        $stmt2 = $conn->prepare($queryAllFiles);
        if (!$stmt2) {
            die("SQL Error: " . $conn->error);
        }

        $stmt2->bind_param("iii", $form_id,$form_id,$form_id);
        $stmt2->execute();

        // Fetch the result for all files
        $all_files_result = $stmt2->get_result();

        $all_files = [];

        if ($all_files_result->num_rows > 0) {
            while ($file = $all_files_result->fetch_assoc()) {
                // Convert the BLOB column ('file_data') to a base64 encoded string, if it exists
                if (isset($file['file_data'])) {
                    $file['file_data'] = base64_encode($file['file_data']);
                }
        
                $all_files[$file['file_group']][] = $file;
            }
        }



        // echo "<pre>";
        // print_r($all_files);
        // echo "</pre>";



    } else {
        echo json_encode(['status' => 'error', 'message' => 'Form ID not found.']);
    }

    $stmt->close();
    $stmt2->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid form ID.']);
}

$conn->close();




?>


<?php
// Include shared components
include 'includes/header.php';
?>

<div class='white-container'>
    <div class='ticket-details-container'>
        
        <?php if ($ticket_data): ?>
            <div class='title-text'>Ticket Number: <?php echo htmlspecialchars($ticket_data['ticket_today'])?> </div>

            <div class='tickets-string-details'>

                <div class='info-container'>
                    <div class='subtitle-text'>Customer Details</div>
                        <li><strong>First Name:</strong> <?php echo htmlspecialchars($ticket_data['first_name']); ?></li>
                        <li><strong>Last Name:</strong> <?php echo htmlspecialchars($ticket_data['last_name']); ?></li>
                        <li><strong>Email:</strong> <?php echo htmlspecialchars($ticket_data['email']); ?></li>
                        <li><strong>Phone:</strong> <?php echo htmlspecialchars($ticket_data['phone']); ?></li>
                </div>


                <div class='info-container' style=''>
                    <div class='subtitle-text'>Form Details</div>
                    <ul>
                        <li><strong>Form ID:</strong> <?php echo htmlspecialchars($ticket_data['form_id']); ?></li>
                        <li><strong>Ticket Number:</strong> <?php echo htmlspecialchars($ticket_data['ticket_today']); ?></li>
                        <li><strong>Have Insurance:</strong> <?php echo htmlspecialchars($ticket_data['have_insurance']); ?></li>
                        <li><strong>Have Title:</strong> <?php echo htmlspecialchars($ticket_data['have_title']); ?></li>
                        <li><strong>Uploaded At:</strong> <?php echo htmlspecialchars($ticket_data['uploaded_at']); ?></li>
                    </ul>
                </div>
                <div class='info-container' style=''>
                    <div class='subtitle-text'>Vehicle Details</div>
                    <ul>
                        <li><strong>VIN:</strong> <?php echo htmlspecialchars($ticket_data['vin']); ?></li>
                        <li><strong>License Plate:</strong> <?php echo htmlspecialchars($ticket_data['license_plate']); ?></li>
                        <li><strong>Registered in NY:</strong> <?php echo htmlspecialchars($ticket_data['registered_in_ny']); ?></li>
                        
                    </ul>
                </div>

                
            </div>


        <?php else: ?>
            <p>No ticket data found for this form ID.</p>
        <?php endif; ?>

        <?php 
        $file_types = ['insurance', 'title', 'license']; // List of file types
        
        foreach ($file_types as $type): ?>
            <?php if (isset($all_files[$type]) && count($all_files[$type]) > 0): ?>
                <h2><?php echo ucfirst($type); ?> Files</h2>
                <table class="ticket-table">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>File Type</th>
                            <th>Uploaded At</th>
                            <th>File Preview</th>
                            <th>Download/Print</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_files[$type] as $file): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($file['file_name']); ?></td>
                                <td><?php echo htmlspecialchars($file['file_type']); ?></td>
                                <td><?php echo htmlspecialchars($file['uploaded_at']); ?></td>
                                <td>
                                    <?php if (isset($file['file_data'])): ?>
                                        <img src="data:image/jpeg;base64,<?php echo $file['file_data']; ?>" alt="<?php echo ucfirst($type); ?> Image" width="100" />
                                    <?php else: ?>
                                        No image available
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <div class="file-actions">
                                        <?php if (isset($file['file_data'])): ?>
                                            <!-- Print Link -->
                                            <a href="javascript:void(0);" onclick="printFile('<?php echo htmlspecialchars($file['file_data']); ?>', '<?php echo htmlspecialchars($file['file_name']); ?>')" class="file-action-link">
                                                Print
                                            </a>
                                            <span> / </span> <!-- Separator -->
                                            <!-- Download Link -->
                                            <a href="data:image/jpeg;base64,<?php echo $file['file_data']; ?>" download="<?php echo htmlspecialchars($file['file_name']); ?>" class="file-action-link">
                                                Download
                                            </a>
                                        <?php else: ?>
                                            No print/download option available
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No <?php echo $type; ?> files found for this form ID.</p>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>



<script>
function printFile(fileData, fileName) {
    // Create a new window or iframe to display the content
    var printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write('<html><head><title>Print File: ' + fileName + '</title></head><body>');
    
    // Display the file based on its type (e.g., image or document)
    printWindow.document.write('<h3>' + fileName + '</h3>');
    printWindow.document.write('<img src="data:image/jpeg;base64,' + fileData + '" alt="' + fileName + '" style="width:100%; height:auto;"/>');
    
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    // Trigger the print dialog
    printWindow.print();
}
</script>