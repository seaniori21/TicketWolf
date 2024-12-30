<?php
session_start();
if (isset($_SESSION['primary_id']) && isset($_SESSION['username'])) {
    include('../functions/conn_db.php');
    // echo "Connected successfully!<br><br>";

    // Get form_id from website link
    $form_id = isset($_GET['form_id']) ? $_GET['form_id'] : null;

    if ($form_id) {
        // Fetch most recent edit user from system_logs table
        $stmt = $conn->prepare("SELECT user_id, changed_at FROM system_logs WHERE form_id = ? ORDER BY changed_at DESC LIMIT 1");

        if (!$stmt) {
            die("SQL Error: " . $conn->error);
        }

        $stmt->bind_param("i", $form_id);
        $stmt->execute();
        $result = $stmt->get_result();


        if ($result->num_rows > 0){
            $recent_edit = $result->fetch_assoc();
            $recent_user_id = $recent_edit['user_id'];
            $recent_changed_at = $recent_edit['changed_at'];

            // Step 3: Use the user_id to fetch the username from the users table
            $user_stmt = $conn->prepare("SELECT username FROM users WHERE primary_id = ?");
            if (!$user_stmt) {
                die("SQL Error: " . $conn->error);
            }
            $user_stmt->bind_param("i", $recent_user_id);
            $user_stmt->execute();

            $user_result = $user_stmt->get_result();
            if ($user_result->num_rows > 0) {
                $user_data = $user_result->fetch_assoc();
                $username = $user_data['username'];
            } else {
                $username = "no username";
            }
        }else{
            $username = "Original Source";
            $recent_changed_at = " ";
        }

        



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
            ";

            // Fetch all files related to this form_id
            $stmt2 = $conn->prepare($queryAllFiles);
            if (!$stmt2) {
                die("SQL Error: " . $conn->error);
            }

            $stmt2->bind_param("iiii", $form_id,$form_id,$form_id, $form_id);
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


<?php
// Include shared components
include '../includes/header.php';
?>
<?php include '../includes/navbar.php'?>
<div class='white-container'>


    <div class='counter-details-container'>
        
    <?php if ($counter_data): ?>


        <div class='title-text'>Ticket Number: <?php echo htmlspecialchars($counter_data['counter_today'])?> </div>
        <div class='info-container' style='display:flex; flex-direction:row; gap:50px; padding:10px; border-bottom:1px solid grey'>
                        <div><strong>Form ID:</strong> <?php echo htmlspecialchars($counter_data['form_id']); ?></div>
                        <div><strong>Record Date:</strong> 
                            <?php
                                $date = new DateTime(htmlspecialchars($counter_data['uploaded_at']), new DateTimeZone('UTC'));
                                $date->setTimezone(new DateTimeZone('America/New_York'));
                                echo $date->format('m/d/Y h:i A');
                            ?>

                        </div>
        </div>




        <div class='detail-section'>
        <form method="POST" id="detailsSectionForm" action="../functions/edit_form_id.php?form_id=<?php echo htmlspecialchars($counter_data['form_id']); ?>">

            <div class='counter-string-details'>

                <div class='info-container'>
                    <div class='subtitle-text'>Customer Details</div>
                    <ul>
                        <li><strong>First Name:</strong> 
                            <input type="text" name="first_name" value="<?php echo htmlspecialchars($counter_data['first_name']); ?>" />
                        </li>
                        <li><strong>Last Name:</strong> 
                            <input type="text" name="last_name" value="<?php echo htmlspecialchars($counter_data['last_name']); ?>" />
                        </li>
                        <li><strong>Email:</strong> 
                            <input type="email" name="email" value="<?php echo htmlspecialchars($counter_data['email']); ?>" />
                        </li>
                        <li><strong>Phone:</strong> 
                            <input type="tel" name="phone" id="phone"
                            value="<?php echo formatPhoneNumber(htmlspecialchars($counter_data['phone'])); ?>" 
                            pattern="^\(\d{3}\)\s?-\s?\d{3}\s?-\s?\d{4}$"
                            oninput="formatPhone(this)" maxlength="18"
                            title="Please enter a 10 digit phone number"/>
                        </li>
                        <li style='margin-top:5px; font-size:14px; color:red;'><strong>Recent Edit By: </strong> 
                            <div>
                                <?php echo $username ." " ;?>
                                <?php
                                    $date = new DateTime(htmlspecialchars($recent_changed_at), new DateTimeZone('UTC'));
                                    $date->setTimezone(new DateTimeZone('America/New_York'));
                                    echo $date->format('m/d/Y h:i A');
                                ?>
                            </div>
                        </li>
                    </ul>
                </div>


                <div class='info-container'>
                    <div class='subtitle-text'>Vehicle Details</div>
                    <ul>
                        <li><strong>VIN:</strong> 
                            <input type="text" name="vin" value="<?php echo htmlspecialchars($counter_data['vin']); ?>" />
                        </li>
                        <li><strong>License Plate:</strong> 
                            <input type="text" name="license_plate" value="<?php echo htmlspecialchars($counter_data['license_plate']); ?>" />
                        </li>
                        <li><strong>Registered in NY:</strong> 
                            <select name="registered_in_ny">
                                <option value="yes" <?php if ($counter_data['registered_in_ny'] == 'yes') echo 'selected'; ?>>Yes</option>
                                <option value="no" <?php if ($counter_data['registered_in_ny'] == 'no') echo 'selected'; ?>>No</option>
                            </select>
                        </li>
                    </ul>
                </div>
                
                <!-- File Details -->
                <div class='info-container'>
                    <div class='subtitle-text'>File Details</div>
                    <ul>
                        <li><strong>Have Registration:</strong>
                            <select name="have_registration">
                                <option value="yes" <?php if ($counter_data['have_registration'] == 'yes') echo 'selected'; ?>>Yes</option>
                                <option value="no" <?php if ($counter_data['have_registration'] == 'no') echo 'selected'; ?>>No</option>
                            </select>
                        </li>
                        <li><strong>Have Insurance:</strong>
                            <select name="have_insurance">
                                <option value="yes" <?php if ($counter_data['have_insurance'] == 'yes') echo 'selected'; ?>>Yes</option>
                                <option value="no" <?php if ($counter_data['have_insurance'] == 'no') echo 'selected'; ?>>No</option>
                            </select>
                        </li>
                        <li><strong>Have Title:</strong>
                            <select name="have_title">
                                <option value="yes" <?php if ($counter_data['have_title'] == 'yes') echo 'selected'; ?>>Yes</option>
                                <option value="no" <?php if ($counter_data['have_title'] == 'no') echo 'selected'; ?>>No</option>
                            </select>
                        </li>
                        <li><strong>Have License:</strong>
                            <select name="have_owner_license">
                                <option value="yes" <?php if ($counter_data['have_owner_license'] == 'yes') echo 'selected'; ?>>Yes</option>
                                <option value="no" <?php if ($counter_data['have_owner_license'] == 'no') echo 'selected'; ?>>No</option>
                            </select>
                        </li>
                    </ul>
                </div>


            </div>

            <button type="submit" class="save-changes-btn">Save Changes</button>


        </form>

        </div>

    <?php else: ?>
        <p>No counter data found for this form ID.</p>
    <?php endif; ?>
    
        <div class='file-section'>
            <?php 
            $file_types = ['insurance', 'title', 'license', 'registration']; // List of file types
            
            foreach ($file_types as $type): ?>
            <div class='line'></div>
                <?php if (isset($all_files[$type]) && count($all_files[$type]) > 0): ?>
                    <h2><?php echo ucfirst($type); ?> Files</h2>
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


                <form class='edit-form-file-upload-section' method="POST" action="../functions/upload_file.php" enctype="multipart/form-data">
                    <input type="hidden" name="form_id" value="<?php echo htmlspecialchars($form_id); ?>">
                    <input type="hidden" name="file_group" value="<?php echo htmlspecialchars($type); ?>">
                    <div class=''>
                        <label for="file_<?php echo $type; ?>">Take a Photo or Upload a <?php echo ucfirst($type); ?> File:</label>
                        <input type="file" name="file" id="file_<?php echo $type; ?>" required>
                    </div>
                    <button type="submit">Upload</button>
                </form>
                <!-- <form class='edit-form-file-upload-section' method="POST" action="../functions/upload_file.php" enctype="multipart/form-data">
                    <input type="hidden" name="form_id" value="<?php echo htmlspecialchars($form_id); ?>">
                    <input type="hidden" name="file_group" value="<?php echo htmlspecialchars($type); ?>">
                    <input type="hidden" name="file" id="file_<?php echo $type; ?>" >
                    

                    <button type="button" id="showCameraButton">Take a Photo</button>


                    <div id="cameraSection" style="display: none;">
                        <video id="video" autoplay></video>
                        <button type="button" id="capture">Capture Photo</button>
                        <canvas id="canvas" style="display:none;"></canvas>
                    </div>
                    
                    <button type="submit">Upload</button>
                </form>

<script>
    const showCameraButton = document.getElementById('showCameraButton');
    const cameraSection = document.getElementById('cameraSection');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureButton = document.getElementById('capture');
    const imageDataInput = document.getElementById('imageData');

    // Show the camera section when the button is clicked
    showCameraButton.addEventListener('click', () => {
        cameraSection.style.display = 'block'; // Show the camera section

        // Access the camera
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                console.error('Error accessing camera: ', err);
            });
    });

    // Capture the image
    captureButton.addEventListener('click', () => {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Convert to base64 and store in hidden input
        const imageData = canvas.toDataURL('image/png');
        imageDataInput.value = imageData;

        console.log("Photo captured successfully!");
        // cameraSection.style.display = 'none';
    });
</script> -->


                

            <?php endforeach; ?>
        </div>

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


    function formatPhone(input) {
        let value = input.value.replace(/\D/g, '');  // Remove non-numeric characters
        if (value.length <= 1) {
            input.value = `${value}`;
        }else if (value.length <= 3) {
            input.value = `(${value}`;
        } else if (value.length <= 6) {
            input.value = `(${value.slice(0, 3)}) - ${value.slice(3)}`;
        } else {
            input.value = `(${value.slice(0, 3)}) - ${value.slice(3, 6)} - ${value.slice(6, 10)}`;
        }
    }

    function cleanPhoneNumber() {
        let phoneInput = document.getElementById('phone');
        let cleanValue = phoneInput.value.replace(/\D/g, '');  // Remove non-numeric characters
        phoneInput.value = cleanValue;  // Set the cleaned value (digits only)
    }


    document.getElementById('detailsSectionForm').addEventListener('submit', function(event) {
        cleanPhoneNumber();
    });
</script>