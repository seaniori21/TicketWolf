<?php
$servername = "auth-db1619.hstgr.io"; 
$username = "u760648682_towwolf_app";         
$password = "BaGoLax1*7";             
$dbname = "u760648682_app";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully!<br>";

// Include the send-email.php file
require 'send_email.php';  // Make sure the path is correct




// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize inputs
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : null;
    $vin = htmlspecialchars($_POST['vin']);
    $drivers_license = isset($_POST['drivers_license']) ? htmlspecialchars($_POST['drivers_license']) : null;
    $license_plate = isset($_POST['license_plate']) ? htmlspecialchars($_POST['license_plate']) : null;
    $is_owner = htmlspecialchars($_POST['is_owner']);
    $registered_in_ny = htmlspecialchars($_POST['registered_in_ny']);
    // $have_insurance = htmlspecialchars($_POST['have_insurance']);
    $have_insurance = isset($_POST['have_insurance']) ? htmlspecialchars($_POST['have_insurance']) : null;
    $have_title = htmlspecialchars($_POST['have_title']);
    $have_owner_license = htmlspecialchars($_POST['have_owner_license']);


    // Calculate the next TicketToday value dynamically
    $ticket_today_query = "SELECT IFNULL(MAX(ticket_today), 0) + 1 AS next_ticket_today 
    FROM tickets 
    WHERE DATE(uploaded_at) = CURDATE()";
    $result = $conn->query($ticket_today_query);

    if ($result) {
        $row = $result->fetch_assoc();
        $ticket_today = $row['next_ticket_today'];
    } else {
        die("SQL Error calculating ticket_today: " . $conn->error);
    }


    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO tickets 
        (first_name, last_name, email, phone, vin, drivers_license, license_plate, is_owner,
         registered_in_ny, have_insurance, have_title, have_owner_license, ticket_today) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sssssssssssss", $first_name, $last_name, $email, $phone, $vin, $drivers_license, 
                $license_plate, $is_owner, $registered_in_ny, $have_insurance, $have_title, $have_owner_license, $ticket_today);

    
    $insuranceFiles = [];
    $licenseFiles = [];
    $titleFiles = [];




    if ($stmt->execute()) {

        //Then we upload insurance files into the insurance files table

        $form_id = $stmt->insert_id;  // Get the inserted form_id for the ticket

        // Handle file upload if insurance files are provided
        if (isset($_FILES['insurance_files']) && !empty($_FILES['insurance_files']['name'][0])) {
            // echo "Uploading insurance files<br>";
            $files = $_FILES['insurance_files'];

            for ($i = 0; $i < count($files['name']); $i++) {
                $file_name = $files['name'][$i];
                $file_type = $files['type'][$i];
                $file_tmp = $files['tmp_name'][$i];
                $file_data = file_get_contents($file_tmp);
                

                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $insuranceFiles[] = [
                        'tmp_name' => $files['tmp_name'][$i], // Temporary file path
                        'name' => $files['name'][$i],         // Original file name
                        'type' => $files['type'][$i],         // File MIME type
                        'data' => file_get_contents($files['tmp_name'][$i]) // File content (optional, if needed)
                    ];
                } 

                // echo "Insurance Files: ";
                // print_r($insuranceFiles);
                

 
                $stmt2 = $conn->prepare("INSERT INTO insurance_files 
                    (form_id, file_name, file_type, file_data) 
                    VALUES (?, ?, ?, ?)");

                if (!$stmt2) {
                    die("SQL Error: " . $conn->error);
                }

                // Bind parameters for insurance table
                $stmt2->bind_param("isss", $form_id, $file_name, $file_type, $file_data);

                // Execute the query to insert insurance file data
                if ($stmt2->execute()) {
                    //echo "Insurance file uploaded successfully: " . htmlspecialchars($file_name) . "<br>";
                } else {
                    echo "Error uploading insurance file: " . $stmt2->error . "<br>";
                }
                $stmt2->close();
            }
        }


        // Handle file upload if title files are provided
        if (isset($_FILES['title_files']) && !empty($_FILES['title_files']['name'][0])) {
            // echo "Uploading title files<br>";
            $files = $_FILES['title_files'];

            for ($i = 0; $i < count($files['name']); $i++) {
                $file_name = $files['name'][$i];
                $file_type = $files['type'][$i];
                $file_tmp = $files['tmp_name'][$i];
                $file_data = file_get_contents($file_tmp);


                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $titleFiles[] = [
                        'tmp_name' => $files['tmp_name'][$i], // Temporary file path
                        'name' => $files['name'][$i],         // Original file name
                        'type' => $files['type'][$i],         // File MIME type
                        'data' => file_get_contents($files['tmp_name'][$i]) // File content (optional, if needed)
                    ];
                } 

                // Prepare SQL statement for title table
                $stmt2 = $conn->prepare("INSERT INTO title_files 
                    (form_id, file_name, file_type, file_data) 
                    VALUES (?, ?, ?, ?)");

                if (!$stmt2) {
                    die("SQL Error: " . $conn->error);
                }

                // Bind parameters for title table
                $stmt2->bind_param("isss", $form_id, $file_name, $file_type, $file_data);

                // Execute the query to insert title file data
                if ($stmt2->execute()) {
                    //echo "title file uploaded successfully: " . htmlspecialchars($file_name) . "<br>";
                } else {
                    echo "Error uploading title file: " . $stmt2->error . "<br>";
                }
                $stmt2->close();
            }
        }

        // Handle file upload if license files are provided
        if (isset($_FILES['license_files']) && !empty($_FILES['license_files']['name'][0])) {
            // echo "Uploading license files<br>";
            $files = $_FILES['license_files'];

            for ($i = 0; $i < count($files['name']); $i++) {
                $file_name = $files['name'][$i];
                $file_type = $files['type'][$i];
                $file_tmp = $files['tmp_name'][$i];
                $file_data = file_get_contents($file_tmp);



                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $licenseFiles[] = [
                        'tmp_name' => $files['tmp_name'][$i], // Temporary file path
                        'name' => $files['name'][$i],         // Original file name
                        'type' => $files['type'][$i],         // File MIME type
                        'data' => file_get_contents($files['tmp_name'][$i]) // File content (optional, if needed)
                    ];
                } 

                // Prepare SQL statement for license table
                $stmt2 = $conn->prepare("INSERT INTO license_files 
                    (form_id, file_name, file_type, file_data) 
                    VALUES (?, ?, ?, ?)");

                if (!$stmt2) {
                    die("SQL Error: " . $conn->error);
                }

                // Bind parameters for license table
                $stmt2->bind_param("isss", $form_id, $file_name, $file_type, $file_data);

                // Execute the query to insert license file data
                if ($stmt2->execute()) {
                    //echo "license file uploaded successfully: " . htmlspecialchars($file_name) . "<br>";
                } else {
                    echo "Error uploading license file: " . $stmt2->error . "<br>";
                }
                $stmt2->close();
            }
        }


        // Call the sendEmail function after the form is processed, email_result is a boolean of success
        $email_result = sendEmail($first_name, $last_name, $email, $phone, $vin, $drivers_license,
        $license_plate, $is_owner, $registered_in_ny, $have_insurance, $have_title, $have_owner_license, $ticket_today, 
        $insuranceFiles, $titleFiles, $licenseFiles);

        if ($email_result === true) {
            // echo "Email sent successfully";
        } else {
            echo $email_result; // Output error message if email fails
        }


        echo json_encode(['status' => 'success', 'ticket' => $ticket_today]);


    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();


} else {
    echo "Invalid request.";
}

$conn->close();
exit();



// header("Location: https://example.com");
// exit();
// header("Location: ../pages/ThankYou.php?form_id=" . );
// exit(); // Ensure no further code runs after the redirect



// Redirect to a new page
// header("Location: ../pages/TicketForm.php");
// exit();


?>
