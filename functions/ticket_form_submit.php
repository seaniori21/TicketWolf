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
    $have_insurance = htmlspecialchars($_POST['have_insurance']);
    $have_title = htmlspecialchars($_POST['have_title']);
    $have_owner_license = htmlspecialchars($_POST['have_owner_license']);

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO tickets 
        (first_name, last_name, email, phone, vin, drivers_license, license_plate, is_owner,
         registered_in_ny, have_insurance, have_title, have_owner_license) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("ssssssssssss", $first_name, $last_name, $email, $phone, $vin, $drivers_license, 
                $license_plate, $is_owner, $registered_in_ny, $have_insurance, $have_title, $have_owner_license);


    // if (isset($_FILES['insurance_files'])) {
    //     foreach ($_FILES['insurance_files']['error'] as $error) {
    //         if ($error != UPLOAD_ERR_OK) {
    //             echo "Error during file upload: $error";
    //         }
    //     }
    // }

    // echo 'FILES: <pre>';
    // print_r($_FILES);
    // echo '</pre>';

    // Execute the query
    if ($stmt->execute()) {

        //Then we upload insurance files into the insurance files table

        $form_id = $stmt->insert_id;  // Get the inserted form_id for the ticket
        // echo "Form submitted successfully! Form ID: " . $form_id . "<br>";

        // Handle file upload if insurance files are provided
        if (isset($_FILES['insurance_files']) && !empty($_FILES['insurance_files']['name'][0])) {
            // echo "Uploading insurance files<br>";
            $files = $_FILES['insurance_files'];

            for ($i = 0; $i < count($files['name']); $i++) {
                $file_name = $files['name'][$i];
                $file_type = $files['type'][$i];
                $file_tmp = $files['tmp_name'][$i];

                // Read the file's binary content
                $file_data = file_get_contents($file_tmp);

                // Prepare SQL statement for insurance table
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
                    echo "Insurance file uploaded successfully: " . htmlspecialchars($file_name) . "<br>";
                } else {
                    echo "Error uploading insurance file: " . $stmt2->error . "<br>";
                }

                $stmt2->close();
            }
        }else {
            // echo "No insurance files uploaded.";
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();


} else {
    echo "Invalid request.";
}

$conn->close();

// header("Location: https://example.com");
// exit();
// header("Location: ../pages/ThankYou.php?form_id=" . $form_id);
// exit(); // Ensure no further code runs after the redirect



// Redirect to a new page
// header("Location: ../pages/TicketForm.php");//?form_id=" . $form_id); // Redirect to a success page with the form_id as a query parameter
// exit(); // Ensure no further code runs after the redirect


?>