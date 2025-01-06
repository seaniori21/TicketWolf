<?php
session_start();
if (isset($_SESSION['primary_id']) && isset($_SESSION['username'])) {
include('../functions/conn_db.php');
// echo "Connected successfully!<br><br>";

// Get form_id from website link
$form_id = isset($_GET['form_id']) ? $_GET['form_id'] : null;
$user_id = $_SESSION['primary_id'];
$username = $_SESSION['username'];

if ($form_id) {
    // Fetch counter data from counter table
    $stmt = $conn->prepare("SELECT * FROM counter WHERE form_id = ?");
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("i", $form_id);
    $stmt->execute();

    // Fetch the result for counter
    $counter_result = $stmt->get_result();

    if ($counter_result->num_rows > 0) {
        // Fetch the row as an associative array
        $counter_data = $counter_result->fetch_assoc();



        $queryAllFiles = "
            SELECT 'insurance' AS file_group, insurance_files.* FROM insurance_files WHERE form_id = ?
            UNION ALL
            SELECT 'title' AS file_group, title_files.* FROM title_files WHERE form_id = ?
            UNION ALL
            SELECT 'license' AS file_group, license_files.* FROM license_files WHERE form_id = ?
            UNION ALL
            SELECT 'registration' AS file_group, registration_files.* FROM registration_files WHERE form_id = ?
            UNION ALL
            SELECT 'additional' AS file_group, additional_files.* FROM additional_files WHERE form_id = ?
        ";

        // Fetch all files related to this form_id
        $stmt2 = $conn->prepare($queryAllFiles);
        if (!$stmt2) {
            die("SQL Error: " . $conn->error);
        }

        $stmt2->bind_param("iiiii", $form_id, $form_id, $form_id, $form_id, $form_id);
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
}else{
    header("Location: index.php");     
    exit();
}
?>


<?php
// Include shared components
include '../includes/header.php';
?>

<?php
// Function to format phone number
function formatPhoneNumber($phone) {
    // Remove any non-numeric characters
    $phone = preg_replace('/\D/', '', $phone);

    // Format the phone number as (XXX) - XXX - XXXX
    if (strlen($phone) == 10) {
        return '(' . substr($phone, 0, 3) . ') - ' . substr($phone, 3, 3) . ' - ' . substr($phone, 6);
    }

    // If the phone number is not valid (less or more than 10 digits), return the original
    return $phone;
}
?>
<?php include '../includes/navbar.php'?>
<div class='white-container'>

    <div class='counter-details-container'>
        
        <?php if ($counter_data): ?>
            <div class='title-text'>Counter Number: <?php echo htmlspecialchars($counter_data['counter_today'])?> </div>

            <div class='counter-string-details'>

                <div class='info-container'>
                    <div class='subtitle-text'>Customer Details</div>
                        <li><strong>First Name:</strong> <?php echo htmlspecialchars($counter_data['first_name']); ?></li>
                        <li><strong>Last Name:</strong> <?php echo htmlspecialchars($counter_data['last_name']); ?></li>
                        <li><strong>Email:</strong> <?php echo htmlspecialchars($counter_data['email']); ?></li>
                        <li><strong>Phone:</strong> <?php echo formatPhoneNumber(htmlspecialchars($counter_data['phone'])); ?></li>
                        <li><strong>Is Owner:</strong> <?php echo htmlspecialchars($counter_data['is_owner']); ?></li>
                </div>

                <div class='info-container' >
                    <div class='subtitle-text'>Vehicle Details</div>
                    <ul>
                        <li><strong>VIN:</strong> <?php echo htmlspecialchars($counter_data['vin']); ?></li>
                        <li><strong>License Plate:</strong> <?php echo htmlspecialchars($counter_data['license_plate']); ?></li>
                        <li><strong>Registered in NY:</strong> <?php echo htmlspecialchars($counter_data['registered_in_ny']); ?></li>
                        <li><strong>Manufacturer:</strong> <?php echo htmlspecialchars($counter_data['manufacturer']); ?></li>
                        <li><strong>Vehicle Type:</strong> <?php echo htmlspecialchars($counter_data['vehicle_type']); ?></li>
                        <li><strong>Model Year:</strong> <?php echo htmlspecialchars($counter_data['model_year']); ?></li>
                        <li><strong>Make:</strong> <?php echo htmlspecialchars($counter_data['make']); ?></li>
                        <li><strong>Model:</strong> <?php echo htmlspecialchars($counter_data['model']); ?></li>
                        <li><strong>Body Class:</strong> <?php echo htmlspecialchars($counter_data['body_class']); ?></li>

                    </ul>
                </div>

                <div class='info-container'>
                    <div class='subtitle-text'>Important Details</div>
                    <ul>
                        <li><strong>Have Registration:</strong> <?php echo htmlspecialchars($counter_data['have_registration']); ?></li>
                        <li><strong>Have Insurance:</strong> <?php echo htmlspecialchars($counter_data['have_insurance']); ?></li>
                        <li><strong>Have Title:</strong> <?php echo htmlspecialchars($counter_data['have_title']); ?></li>
                        <li><strong>Have License:</strong> <?php echo htmlspecialchars($counter_data['have_owner_license']); ?></li>
                        <li><strong>Type of Tow:</strong> <?php echo htmlspecialchars($counter_data['type_of_tow']); ?></li>
                    </ul>
                </div>


                <div class='info-container' style='flex:1.5'>
                    <div class='subtitle-text'>Form Details</div>
                    <ul>
                        <li><strong>Form ID:</strong> <?php echo htmlspecialchars($counter_data['form_id']); ?></li>
                        <li><strong>Record Date:</strong> 
                                <?php
                                    $date = new DateTime(htmlspecialchars($counter_data['uploaded_at']), new DateTimeZone('UTC'));
                                    $date->setTimezone(new DateTimeZone('America/New_York'));
                                    echo $date->format('m/d/Y h:i A');
                                ?>
                        </li>
                    </ul>
                </div>

                
            </div>


        <?php else: ?>
            <p>No counter data found for this form ID.</p>
        <?php endif; ?>


        <?php 
        $file_types = ['insurance', 'title', 'license', 'registration', 'additional']; // List of file types
        foreach ($file_types as $type): ?>
            <div class='line' style='margin-top:5%;'></div>
            <h2><?php echo ucfirst($type); ?> Files</h2>
            <?php if (isset($all_files[$type]) && count($all_files[$type]) > 0): ?>
                <table class="counter-table">
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
                                        <?php if (strpos($file['file_type'], 'image') !== false): ?>
                                            <img src="data:image/jpeg;base64,<?php echo $file['file_data']; ?>" alt="<?php echo ucfirst($type); ?> Image" width="100" />
                                        <?php elseif (strpos($file['file_type'], 'pdf') !== false): ?>
                                            <object data="data:application/pdf;base64,<?php echo $file['file_data']; ?>" type="application/pdf" width="50%" height="auto">
                                                <!-- <a href="data:application/pdf;base64,<?php //echo $file['file_data']; ?>">Download PDF</a> -->
                                            </object>
                                        <?php elseif (strpos($file['file_type'], 'msword') !== false || strpos($file['file_type'], 'word') !== false): ?>
                                            <object data="data:application/vnd.openxmlformats-officedocument.wordprocessingml.document;base64,<?php echo $file['file_data']; ?>" type="application/vnd.openxmlformats-officedocument.wordprocessingml.document" width="100%" height="600px">
                                                <p>Sorry, your browser does not support embedded documents.</p>
                                            </object>
                                        <?php else: ?>
                                            Unsupported file type
                                        <?php endif; ?>
                                    <?php else: ?>
                                        No file available
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <div class="file-actions">
                                        <?php if (isset($file['file_data'])): ?>
                                            <!-- View Link -->
                                            <a href="javascript:void(0);" onclick="viewFile('<?php echo htmlspecialchars($file['file_data']); ?>',
                                                 '<?php echo htmlspecialchars($file['file_name']); ?>', '<?php echo htmlspecialchars($file['file_type']);  ?>' )" class="file-action-link">
                                                    View
                                            </a>
                                            <span> / </span> <!-- Separator -->
                                            <!-- Print Link -->
                                            <a href="javascript:void(0);" onclick="printFile('<?php echo htmlspecialchars($file['file_data']); ?>',
                                                 '<?php echo htmlspecialchars($file['file_name']); ?>', '<?php echo htmlspecialchars($file['file_type']);  ?>' )" class="file-action-link">
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

    <div class="comment-form-container" id="comment_section">
        <h2>Leave a Comment</h2>
        <form id="commentForm" method="POST">
            <input type="hidden" name="form_id" value="<?php echo htmlspecialchars($form_id); ?>">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            <textarea id="comment" name="comment" rows="4" placeholder="Write your comment here..." required></textarea>
            <button type="submit" class="submit-btn">Submit Comment</button>
        </form>
    </div>
    <div id="comment_confirmation" style="display:none;">
        <h2>Your Comment Has Been Recieved</h2>
    </div>


    <div style="margin-top:20px;">
    </div>

</div>



<script>
    function viewFile(fileData, fileName, fileType) {
        // New window to disply print tab
        var printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.write('<html><head><title>Print File: ' + fileName + '</title></head><body>');
        
        // Handle different file types
        if (fileType.includes('image')) {
            printWindow.document.write('<img src="data:' + fileType + ';base64,' + fileData + '" alt="' + fileName + '" style="width:100%; height:auto;"/>');
        } else if (fileType.includes('pdf')) {
            printWindow.document.write('<object data="data:' + fileType + ';base64,' + fileData + '" type="application/pdf" width="100%" height="100%"></object>');
        } else if (fileType.includes('msword') || fileType.includes('word')) {
            printWindow.document.write('<object data="data:' + fileType + ';base64,' + fileData + '" type="application/pdf" width="100%" height="100%"></object>');
        } else {
            printWindow.document.write('<p>File type not supported for preview.</p>');
        }

        printWindow.document.write('</body></html>');
    }

    function printFile(fileData, fileName, fileType) {
        // New window to disply print tab
        var printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.write('<html><head><title>Print File: ' + fileName + '</title></head><body>');
        
        // Handle different file types
        if (fileType.includes('image')) {
            printWindow.document.write('<img src="data:' + fileType + ';base64,' + fileData + '" alt="' + fileName + '" style="width:100%; height:auto;"/>');
        } else if (fileType.includes('pdf')) {
            printWindow.document.write('<object data="data:' + fileType + ';base64,' + fileData + '" type="application/pdf" width="100%" height="100%"></object>');
        } else if (fileType.includes('msword') || fileType.includes('word')) {
            printWindow.document.write('<object data="data:' + fileType + ';base64,' + fileData + '" type="application/pdf" width="100%" height="100%"></object>');
        } else {
            printWindow.document.write('<p>File type not supported for preview.</p>');
        }

        printWindow.document.write('</body></html>');
        printWindow.document.close();

        // Delay print to allow time for file to convert for preview
        setTimeout(function() {
            printWindow.print();
        }, 1000);  
    }

    $(document).ready(function() {
        $('#commentForm').submit(function(event) {
            event.preventDefault();  // Prevents the form from submitting the traditional way (page refresh)

            // Get form data
            var formData = $(this).serialize();  // Serializes the form data to send

            // Perform the AJAX request
            $.ajax({
                url: '../functions/submit_comment.php',  // The PHP script to handle the comment submission
                type: 'POST',  // HTTP method (POST in this case)
                data: formData, 
                success: function(response) {
                    
                    commentSection = document.getElementById("comment_section");
                    commentSection.style.display = "none";

                    commentConfirmation = document.getElementById("comment_confirmation");
                    commentConfirmation.style.display = "block";
                },
                error: function(xhr, status, error) {
                    // Handle errors (you can display an error message here)
                    alert('Error submitting the comment. Please try again.');
                    console.error('Error:', error);
                }
            });
        });
    });
</script>


<?php
include('../includes/footer.php');
?>