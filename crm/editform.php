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


        <div class='title-text'>Counter Number: <?php echo htmlspecialchars($counter_data['counter_today'])?> </div>
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
                        <li><strong>Manufacturer:</strong>
                            <input type="text" name="manufacturer" value="<?php echo htmlspecialchars($counter_data['manufacturer']); ?>" />
                        </li>
                        <li><strong>Vehicle Type:</strong>
                            <input type="text" name="vehicle_type" value="<?php echo htmlspecialchars($counter_data['vehicle_type']); ?>" />
                        </li>
                        <li><strong>Model Year:</strong>
                            <input type="text" name="model_year" value="<?php echo htmlspecialchars($counter_data['model_year']); ?>" />
                        </li>
                        <li><strong>Make:</strong>
                            <input type="text" name="make" value="<?php echo htmlspecialchars($counter_data['make']); ?>" />
                        </li>
                        <li><strong>Model:</strong>
                            <input type="text" name="model" value="<?php echo htmlspecialchars($counter_data['model']); ?>" />
                        </li>
                        <li><strong>Body Class:</strong>
                            <input type="text" name="body_class" value="<?php echo htmlspecialchars($counter_data['body_class']); ?>" />
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
                        <li><strong>Type of Tow:</strong>
                            <select name="type_of_tow" required>
                                <option value="" disabled selected>Select Type of Tow</option>ROTOW/DARP/PRIVATE
                                <option value="ROTOW" <?php echo ($counter_data['type_of_tow'] == 'ROTOW') ? 'selected' : ''; ?>>ROTOW</option>
                                <option value="DARP" <?php echo ($counter_data['type_of_tow'] == 'DARP') ? 'selected' : ''; ?>>DARP</option>
                                <option value="PRIVATE" <?php echo ($counter_data['type_of_tow'] == 'PRIVATE') ? 'selected' : ''; ?>>PRIVATE</option>
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
            $file_types = ['insurance', 'title', 'license', 'registration', 'additional']; // List of file types
            
            foreach ($file_types as $type): ?>
            <div class='line'></div>
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
                                            <object data="data:application/vnd.openxmlformats-officedocument.wordprocessingml.document;base64,
                                            <?php echo $file['file_data']; ?>" type="application/vnd.openxmlformats-officedocument.wordprocessingml.document" width="100%" height="600px">
                                                <p>Sorry, Word Docs Cannot be previewed</p>
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
                                                <a href="data:image/jpeg;base64,<?php echo $file['file_data']; ?>" 
                                                download="<?php echo htmlspecialchars($file['file_name']); ?>" class="file-action-link">
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

                

                <button type="button" class="showCameraButton" data-type="<?php echo $type; ?>">Take A Photo</button>
                <button type="button" class="showFileSectionButton" data-type="<?php echo $type; ?>">Attach A File</button>
                <button type="button" class="showScanSectionButton" data-type="<?php echo $type; ?>">Use Scanner</button>


                <!--  Taking A Photo Section  --> 
                <form class="edit-form-file-upload-section" method="POST" action="../functions/upload_file.php" enctype="multipart/form-data">
                    <input type="hidden" name="form_id" value="<?php echo htmlspecialchars($form_id); ?>">
                    <input type="hidden" name="file_group" value="<?php echo htmlspecialchars($type); ?>">
                    <input type="hidden" name="file" id="file_<?php echo $type; ?>" required>

                    <div class="cameraSection" id="cameraSection_<?php echo $type; ?>" style="display: none;">
                        <video id="video_<?php echo $type; ?>" autoplay></video>
                        <button type="button" id="capture_<?php echo $type; ?>">Capture Photo</button>
                        <button type="button" class="goBackCam" data-type="<?php echo $type; ?>">Go Back</button>
                        <canvas id="canvas_<?php echo $type; ?>" style="display:none;"></canvas>
                    </div>

                    <div class="previewSection" id="previewSection_<?php echo $type; ?>" style="display: none;">
                        <h3>Preview:</h3>
                        <img id="previewImage_<?php echo $type; ?>" src="" alt="Captured Image Preview" width="100%">
                        <button type="button" class="retakePhotoButton" data-type="<?php echo $type; ?>">Retake Photo</button>
                        <button type="button" class="submitPhotoButton" data-type="<?php echo $type; ?>" >Upload Photo</button>
                    </div>

                </form>

                <!--  Uploading File Section  --> 
                <form 
                    class="edit-form-file-upload-section" 
                    method="POST" 
                    action="../functions/upload_file.php" 
                    enctype="multipart/form-data" 
                    style="display: none; margin-bottom:20px" 
                    id="uploadFileSection_<?php echo $type ?>">
                    
                    <input type="hidden" name="form_id" value="<?php echo htmlspecialchars($form_id); ?>">
                    <input type="hidden" name="file_group" value="<?php echo htmlspecialchars($type); ?>">
                    <input type="hidden" name="upload_type" value="file">
                    
                    <div>
                        <label for="file_<?php echo $type; ?>">Upload a <?php echo ucfirst($type); ?> File:</label>
                        <input type="file" name="file" id="file_<?php echo $type; ?>" required>
                        <button type="submit">Upload</button>
                        <button type="button" class="goBackFile" data-type="<?php echo $type; ?>">Go Back</button>
                        
                    </div>
                </form>


                <!--  Uploading Scan Section  --> 
                <form 
                    class="edit-form-file-upload-section" 
                    method="POST" 
                    action="../functions/upload_file.php" 
                    enctype="multipart/form-data" 
                    style="display:none; margin-bottom:20px" 
                    id="uploadScanSection_<?php echo $type ?>">
                    
                    <input type="hidden" name="form_id" value="<?php echo htmlspecialchars($form_id); ?>">
                    <input type="hidden" name="file_group" value="<?php echo htmlspecialchars($type); ?>">
                    <input type="hidden" name="upload_type" value="file">
                    
                    <div>
                        <label for="file_<?php echo $type; ?>"></label>
                        <input type="hidden" name="file" id="file_<?php echo $type; ?>" required>

                        <p id="scanLoading" style="display:none;">Scanning in progress...</p>
                        <div id="scanPreview"></div>

                        <div class="row-flex">
                            <button type="button" id="scanButton_<?php echo $type; ?>">Scan</button>
                            
                            <button type="button" style="display:none;" id="scanUpload_<?php echo $type; ?>">Upload</button>
                            <button type="button" class="goBackScan" data-type="<?php echo $type; ?>">Go Back</button>
                        </div>
                    </div>
                </form>

                <?php endforeach; ?>

            
        </div>
        <div class='line'></div>
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

</div>



<script>
    document.querySelectorAll(".showScanSectionButton").forEach(button => {
        button.addEventListener("click",  function() {
            const type = this.getAttribute("data-type");

            const submitPhotoButton = document.querySelector(`.showCameraButton[data-type="${type}"]`);
            submitPhotoButton.style.display = 'none';
            const showFileSectionButton = document.querySelector(`.showFileSectionButton[data-type="${type}"]`);
            showFileSectionButton.style.display = 'none';
            const showScanSectionButton = document.querySelector(`.showScanSectionButton[data-type="${type}"]`);
            showScanSectionButton.style.display = 'none';


            const scanSection = document.getElementById("uploadScanSection_" + type);
            scanSection.style.display = "flex";

            //AFTER SHOWING SCAN SECTION
            const scanButton = document.getElementById("scanButton_" + type);

            scanButton.addEventListener("click", function() {
                const container = this.closest("form"); 

                const scanLoadingElement = container.querySelector('#scanLoading');
                const scanPreviewElement = container.querySelector('#scanPreview');
                const fileInput = container.querySelector("input[name='file']");

                scanLoadingElement.style.display = 'flex';
                scanButton.style.display = 'none';


                var xhr = new XMLHttpRequest();
                xhr.open('GET', '../functions/scan_file.php', true);

                xhr.onload = function () {
                    if (xhr.status === 200) {
                        // Hide loading message
                        scanLoadingElement.style.display = 'none';
                        var response = JSON.parse(xhr.responseText);


                        var filePath = response.filename;
                        console.log("RESPONSE TEXT FROM SCAN_FILE:" + response);
                        scanPreviewElement.innerHTML = '<object data="' + filePath + '" type="application/pdf" width="600" height="400"></object>';
                        fileInput.value = filePath; 

            
                        console.log("PDF file preview is working. File path: " + filePath);
                    }else{
                        console.log("ERROR:"+ xhr.status +  " - " + xhr.statusText);
                    }
                };
                // Send the request
                xhr.send();

                


                scanUpload = document.getElementById("scanUpload_" + type);
                scanUpload.style.display = "flex";


                scanUpload.addEventListener("click", function() {
                    event.preventDefault(); //TODO
                    const fileGroupInput = container.querySelector("input[name='file_group']");
                    const formIdInput = container.querySelector("input[name='form_id']");
                    const pdfFilePath = fileInput.value;
                    
                    

                    if (pdfFilePath) {
                        fetch(pdfFilePath)
                        .then(response => response.blob())
                        .then(blob => {
                            const reader = new FileReader();
                            reader.onloadend = function() {
                                const base64data = reader.result;
                                const byteString = atob(base64data.split(',')[1]); // Decode Base64 string
                                const arrayBuffer = new ArrayBuffer(byteString.length);
                                const uint8Array = new Uint8Array(arrayBuffer);

                                for (let i = 0; i < byteString.length; i++) {
                                    uint8Array[i] = byteString.charCodeAt(i);
                                }

                                // Create a new Blob from the file data
                                const pdfBlob = new Blob([uint8Array], { type: "application/pdf" });

                                const formData = new FormData();

                                // Append fields to respective form
                                formData.append("file", blob, "scanned_file.pdf");
                                formData.append("form_id", formIdInput.value);
                                formData.append("file_group", fileGroupInput.value);

                                formData.append("file", pdfBlob, "scanned_file.pdf");

                                console.log("REACHED THE END",formData);

                                //Now send the FormData to the server using fetch
                                fetch("../functions/upload_file.php", {
                                    method: "POST",
                                    body: formData
                                })
                                .then(response => response.json()) 
                                .then(data => {
                                    if (data.status === "success") {
                                        window.location.href = "editform.php?form_id=" + data.form_id; // TODO
                                    } else {
                                        alert("Upload failed: " + data.message);
                                    }
                                })
                                .catch(error => {
                                    alert("Error uploading file: " + error);
                                });
                            };
                            reader.readAsDataURL(blob);
                        })
                        .catch(error => {
                            console.error('Error loading the file:', error);
                        });                      

                    } else {
                        alert("No image to upload!");
                    }


                });
            });
        });
    });
    
    
    document.querySelectorAll(".goBackCam").forEach(button => {
        button.addEventListener("click",  function() {
            const type = this.getAttribute("data-type");

            const showCameraButton = document.querySelector(`.showCameraButton[data-type="${type}"]`);
            showCameraButton.style.display = 'inline-flex';
            const showFileSectionButton = document.querySelector(`.showFileSectionButton[data-type="${type}"]`);
            showFileSectionButton.style.display = 'inline-flex';
            const showScanSectionButton = document.querySelector(`.showScanSectionButton[data-type="${type}"]`);
            showScanSectionButton.style.display = 'inline-flex';

            const cameraSection = document.getElementById("cameraSection_" + type);
            cameraSection.style.display = 'none'; 
        });
    });
    document.querySelectorAll(".goBackFile").forEach(button => {
        button.addEventListener("click",  function() {
            const type = this.getAttribute("data-type");

            const showCameraButton = document.querySelector(`.showCameraButton[data-type="${type}"]`);
            showCameraButton.style.display = 'inline-flex';
            const showFileSectionButton = document.querySelector(`.showFileSectionButton[data-type="${type}"]`);
            showFileSectionButton.style.display = 'inline-flex';
            const showScanSectionButton = document.querySelector(`.showScanSectionButton[data-type="${type}"]`);
            showScanSectionButton.style.display = 'inline-flex';

            const uploadFileSection = document.getElementById("uploadFileSection_" + type);
            uploadFileSection.style.display = 'none'; 
        });
    });
    document.querySelectorAll(".goBackScan").forEach(button => {
        button.addEventListener("click",  function() {
            const type = this.getAttribute("data-type");

            const showCameraButton = document.querySelector(`.showCameraButton[data-type="${type}"]`);
            showCameraButton.style.display = 'inline-flex';
            const showFileSectionButton = document.querySelector(`.showFileSectionButton[data-type="${type}"]`);
            showFileSectionButton.style.display = 'inline-flex';
            const showScanSectionButton = document.querySelector(`.showScanSectionButton[data-type="${type}"]`);
            showScanSectionButton.style.display = 'inline-flex';

            const uploadScanSection_ = document.getElementById("uploadScanSection_" + type);
            uploadScanSection_.style.display = 'none'; 
        });
    });

    document.querySelectorAll(".showFileSectionButton").forEach(button => {
        button.addEventListener("click",  function() {
            const type = this.getAttribute("data-type");

            const submitPhotoButton = document.querySelector(`.showCameraButton[data-type="${type}"]`);
            submitPhotoButton.style.display = 'none';
            const showFileSectionButton = document.querySelector(`.showFileSectionButton[data-type="${type}"]`);
            showFileSectionButton.style.display = 'none';
            const showScanSectionButton = document.querySelector(`.showScanSectionButton[data-type="${type}"]`);
            showScanSectionButton.style.display = 'none';


            const fileSection = document.getElementById("uploadFileSection_" + type);
            fileSection.style.display = "flex";
        });
    });

    document.querySelectorAll(".retakePhotoButton").forEach(button =>{
        button.addEventListener("click" , function() {
            const type = this.getAttribute("data-type");


            //hide preview section 
            const previewSection = document.getElementById("previewSection_" + type); 
            const previewImage = document.getElementById("previewImage_" + type); 
            previewImage.src = '';
            previewSection.style.display='none';

            //Clear files in camera Section
            const container = this.closest("form"); 
            const fileInput = container.querySelector("input[name='file']");
            fileInput.value = '';
            const video = document.getElementById("video_" + type);
            
            //Display Camera Section
            const showCameraButton = document.querySelector(`.showCameraButton[data-type="${type}"]`);
            if (showCameraButton) {
                
                showCameraButton.click(); 
            }
            
        });
    });

    // Dynamically handle the camera for multiple sections
    document.querySelectorAll(".showCameraButton").forEach(button => {
        button.addEventListener("click", function() {
            const type = this.getAttribute("data-type");

        
            const cameraSection = document.getElementById("cameraSection_" + type);
            const video = document.getElementById("video_" + type);
            const captureButton = document.getElementById("capture_" + type);
            const canvas = document.getElementById("canvas_" + type);
            const fileInput = document.getElementById("file_" + type); 
            const previewSection = document.getElementById("previewSection_" + type); 
            const previewImage = document.getElementById("previewImage_" + type); 

      

            cameraSection.style.display = "block"; 
            this.style.display = "none";
            const showFileSectionButton = document.querySelector(`.showFileSectionButton[data-type="${type}"]`);
            showFileSectionButton.style.display = 'none';
            const showScanSectionButton = document.querySelector(`.showScanSectionButton[data-type="${type}"]`);
            showScanSectionButton.style.display = 'none';

            // Start the camera stream
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    video.srcObject = stream;
                })
                .catch(function(err) {
                    alert("Error accessing camera: " + err);
                });

            // Capture the photo when the capture button is clicked
            captureButton.addEventListener("click", function() {
            
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                
                const context = canvas.getContext("2d");
                context.drawImage(video, 0, 0, canvas.width, canvas.height); 


                const imageData = canvas.toDataURL("image/jpeg", 1.0); // Higher quality format


                fileInput.value = imageData;
                // console.log(fileInput.value)


                const stream = video.srcObject;
                const tracks = stream.getTracks();
                tracks.forEach(track => track.stop()); 
                video.srcObject = null;


                previewSection.style.display = "block"; 
                previewImage.src = imageData; 

                cameraSection.style.display = "none";
            });
        });
    });

    // Convert the Base64 to a File and upload via FormData
    document.querySelectorAll(".submitPhotoButton").forEach(button => {
        button.addEventListener("click", function() {

            const type = this.getAttribute("data-type");
            const container = this.closest("form"); 
            const fileGroupInput = container.querySelector("input[name='file_group']");
            const formIdInput = container.querySelector("input[name='form_id']");
            const fileInput = container.querySelector("input[name='file']");
            const imageData = fileInput.value;

            if (imageData) {
                // Convert the Base64 string to a Blob (which will be treated as a file)
                const byteString = atob(imageData.split(',')[1]); // Decode Base64 string
                const arrayBuffer = new ArrayBuffer(byteString.length);
                const uint8Array = new Uint8Array(arrayBuffer);

                for (let i = 0; i < byteString.length; i++) {
                    uint8Array[i] = byteString.charCodeAt(i);
                }

                // Create a Blob dataType
                const blob = new Blob([uint8Array], { type: "image/png" });

                // Create a FormData object
                const formData = new FormData();

                // Append fields to respective form
                formData.append("file", blob, "captured_image.png");
                formData.append("form_id", formIdInput.value);
                formData.append("file_group", fileGroupInput.value);
                

                // Now send the FormData to the server using fetch
                fetch("../functions/upload_file.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json()) 
                .then(data => {
                    if (data.status === "success") {
                        window.location.href = "editform.php?form_id=" + data.form_id; // TODO
                    } else {
                        alert("Upload failed: " + data.message);
                    }
                })
                .catch(error => {
                    alert("Error uploading file: " + error);
                });
            } else {
                alert("No image to upload!");
            }
        });
    });


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
        phoneInput.value = cleanValue;  
    }


    document.getElementById('detailsSectionForm').addEventListener('submit', function(event) {
        cleanPhoneNumber();
    });

</script>


<?php
include('../includes/footer.php');
?>