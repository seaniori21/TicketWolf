<?php
include('conn_db.php');
// echo "Connected successfully!<br>";
require 'send_email.php'; 




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
    $have_insurance = isset($_POST['have_insurance']) ? htmlspecialchars($_POST['have_insurance']) : null;
    $have_title = htmlspecialchars($_POST['have_title']);
    $have_owner_license = htmlspecialchars($_POST['have_owner_license']);

    //VIN
    $manufacturer = htmlspecialchars($_POST['manufacturer']);
    $vehicle_type = htmlspecialchars($_POST['vehicle_type']);
    $model_year = htmlspecialchars($_POST['model_year']);
    $make = htmlspecialchars($_POST['make']);
    $model = htmlspecialchars($_POST['model']);
    $body_class = htmlspecialchars($_POST['body_class']);



    $ip_address = $_SERVER['REMOTE_ADDR'];
    $browser_info = $_SERVER['HTTP_USER_AGENT'];


    // Calculate the next TicketToday value dynamically
    $counter_today_query = "SELECT IFNULL(MAX(counter_today), 0) + 1 AS next_counter_today 
    FROM counter 
    WHERE DATE(uploaded_at) = CURDATE()";
    $result = $conn->query($counter_today_query);

    if ($result) {
        $row = $result->fetch_assoc();
        $counter_today = $row['next_counter_today'];
    } else {
        die("SQL Error calculating counter_today: " . $conn->error);
    }


    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO counter 
    (first_name, last_name, email, phone, vin, drivers_license, license_plate, is_owner,
     registered_in_ny, have_insurance, have_title, have_owner_license, counter_today, ip_address, browser_info, 
     manufacturer, vehicle_type, model_year, make, model, body_class) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sssssssssssssssssssss", $first_name, $last_name, $email, $phone, $vin, $drivers_license, 
                $license_plate, $is_owner, $registered_in_ny, $have_insurance, $have_title, $have_owner_license, $counter_today, $ip_address, 
                $browser_info, $manufacturer, $vehicle_type, $model_year, $make, $model, $body_class);

    
    $insuranceFiles = [];
    $licenseFiles = [];
    $titleFiles = [];
    $registrationFiles = [];




    if ($stmt->execute()) {

        //Then we upload insurance files into the insurance files table

        $form_id = $stmt->insert_id;  // Get the inserted form_id for the counter

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
                    //echo "Error uploading insurance file: " . $stmt2->error . "<br>";
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
                    //echo "Error uploading title file: " . $stmt2->error . "<br>";
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
                    //echo "Error uploading license file: " . $stmt2->error . "<br>";
                }
                $stmt2->close();
            }
        }
        if (isset($_FILES['registration_files']) && !empty($_FILES['registration_files']['name'][0])) {
            // echo "Uploading registration files<br>";
            $files = $_FILES['registration_files'];
        
            for ($i = 0; $i < count($files['name']); $i++) {
                $file_name = $files['name'][$i];
                $file_type = $files['type'][$i];
                $file_tmp = $files['tmp_name'][$i];
                $file_data = file_get_contents($file_tmp);
        
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $registrationFiles[] = [
                        'tmp_name' => $files['tmp_name'][$i], // Temporary file path
                        'name' => $files['name'][$i],         // Original file name
                        'type' => $files['type'][$i],         // File MIME type
                        'data' => file_get_contents($files['tmp_name'][$i]) // File content (optional, if needed)
                    ];
                } 
        
                // Prepare SQL statement for registration table
                $stmt2 = $conn->prepare("INSERT INTO registration_files 
                    (form_id, file_name, file_type, file_data) 
                    VALUES (?, ?, ?, ?)");
        
                if (!$stmt2) {
                    die("SQL Error: " . $conn->error);
                }
        
                // Bind parameters for registration table
                $stmt2->bind_param("isss", $form_id, $file_name, $file_type, $file_data);
        
                // Execute the query to insert registration file data
                if ($stmt2->execute()) {
                    //echo "registration file uploaded successfully: " . htmlspecialchars($file_name) . "<br>";
                } else {
                    //echo "Error uploading registration file: " . $stmt2->error . "<br>";
                }
                $stmt2->close();
            }
        }
        
        $emailArray = ["release@benandninoauto.com", "support@towwolf.com"];

        for ($i = 0; $i < count($emailArray); $i++) {
            // Call the sendEmail function after the form is processed, email_result is a boolean of success
            sendEmailClerk($emailArray[$i],$first_name, $last_name, $email, $phone, $vin, $drivers_license,
            $license_plate, $is_owner, $registered_in_ny, $have_insurance, $have_title, $have_owner_license, $counter_today, 
            $insuranceFiles, $titleFiles, $licenseFiles,$registrationFiles);
        }

        sendEmailCustomer($first_name, $last_name, $email, $phone, $vin, $drivers_license,
        $license_plate, $is_owner, $registered_in_ny, $have_insurance, $have_title, $have_owner_license, $counter_today, 
        $insuranceFiles, $titleFiles, $licenseFiles);




        echo json_encode(['status' => 'success', 'counter' => $counter_today]);


    } else {
        //echo "Error: " . $stmt->error;
    }

    $stmt->close();


} else {
    //echo "Invalid request.";
}

$conn->close();
exit();


?>
