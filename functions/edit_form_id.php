<?php
include('conn_db.php');
session_start();
// Retrieve current user information
if (isset($_SESSION['primary_id']) && isset($_SESSION['username'])) {
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
    $user_id = isset($_SESSION['primary_id']) ? $_SESSION['primary_id'] : null;
    echo "User_id: " . $user_id . "<br>";
}else{
    $user_id=1;
    echo "BOOOOOO: " . $user_id . "<br>";
}


// Retrieve form data
$form_id = isset($_GET['form_id']) ? $_GET['form_id'] : null;
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$vin = $_POST['vin'];
$license_plate = $_POST['license_plate'];
$registered_in_ny = $_POST['registered_in_ny'];
$have_registration = $_POST['have_registration'];
$have_insurance = $_POST['have_insurance'];
$have_title = $_POST['have_title'];
$type_of_tow = $_POST['type_of_tow'];
$manufacturer = $_POST['manufacturer'];
$vehicle_type = $_POST['vehicle_type'];
$model_year = $_POST['model_year'];
$make = $_POST['make'];
$model = $_POST['model'];
$body_class = $_POST['body_class'];


// Check if form_id is valid
if ($form_id) {
    // Retrieve current data for the form
    $stmt = $conn->prepare("SELECT * FROM counter WHERE form_id = ?");
    $stmt->bind_param("i", $form_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_data = $result->fetch_assoc();
    $stmt->close();

    if (!$current_data) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid form ID.']);
        exit;
    }

    //Prepare the UPDATE statement
    $stmt = $conn->prepare("
        UPDATE counter 
        SET first_name = ?, last_name = ?, email = ?, phone = ?, vin = ?, license_plate = ?, 
            registered_in_ny = ?, have_registration = ?, have_insurance = ?, have_title = ?, type_of_tow = ?,
            manufacturer = ?, vehicle_type = ?, model_year = ?, make = ?, model = ?, body_class = ?
        WHERE form_id = ?
    ");

    $stmt->bind_param(
        "sssssssssssssssssi",
        $first_name, $last_name, $email, $phone, $vin, $license_plate, 
        $registered_in_ny, $have_registration, $have_insurance, $have_title, $type_of_tow,
        $manufacturer, $vehicle_type, $model_year, $make, $model, $body_class,
        $form_id
    );


    if ($stmt->execute()) {
        // Log changes
        $changes = ''; 
        foreach (['first_name', 'last_name', 'email', 'phone', 'vin', 'license_plate', 'registered_in_ny', 'have_registration', 'have_insurance', 'have_title',
                    'type_of_tow', 'manufacturer', 'vehicle_type', 'model_year', 'make', 'model', 'body_class'] as $field) {
            if ($current_data[$field] !== $_POST[$field]) {
                $changes .= $field . '=' . $current_data[$field] . '; ' ;
            }
        }


        if (!empty($changes)) {
            // Insert into system_logs table
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $browser_info = $_SERVER['HTTP_USER_AGENT'];
            $change_details = $changes;
            $event_type = "Edit";

            $log_stmt = $conn->prepare("
                INSERT INTO system_logs (form_id, user_id, changed_at, event, ip_address, browser_info, event_type)
                VALUES (?, ?, NOW(), ?, ?, ?, ?)
            ");

            $log_stmt->bind_param("iissss", $form_id, $user_id, $change_details, $ip_address, $browser_info, $event_type);
            if (!$log_stmt->execute()) {
                echo "Error: " . $log_stmt->error;
                exit;
            }
            // $log_stmt->execute();
            $log_stmt->close();
        }

        echo json_encode(['status' => 'success', 'message' => 'Ticket updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update counter.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid form ID.']);
}

$conn->close();

// Redirect back to the form page
header('Location: ../crm/editform.php?form_id=' . $form_id);

?>




<?php
// include('conn_db.php');

// // Retrieve current user information
// $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
// $user_id = isset($_SESSION['primary_id']) ? $_SESSION['primary_id'] : null;

// // Retrieve form data
// $form_id = isset($_GET['form_id']) ? $_GET['form_id'] : null;
// $first_name = $_POST['first_name'];
// $last_name = $_POST['last_name'];
// $email = $_POST['email'];
// $phone = $_POST['phone'];
// $vin = $_POST['vin'];
// $license_plate = $_POST['license_plate'];
// $registered_in_ny = $_POST['registered_in_ny'];
// $have_registration = $_POST['have_registration'];
// $have_insurance = $_POST['have_insurance'];
// $have_title = $_POST['have_title'];

// if ($form_id) {
//     $stmt = $conn->prepare("
//         UPDATE counter 
//         SET first_name = ?, last_name = ?, email = ?, phone = ?, vin = ?, license_plate = ?, 
//             registered_in_ny = ?, have_registration = ?, have_insurance = ?, have_title = ?
//         WHERE form_id = ?
//     ");
    
//     $stmt->bind_param(
//         "ssssssssssi",
//         $first_name, $last_name, $email, $phone, $vin, $license_plate, 
//         $registered_in_ny, $have_registration, $have_insurance, $have_title, $form_id
//     );

//     if ($stmt->execute()) {
//         echo json_encode(['status' => 'success', 'message' => 'Ticket updated successfully.']);
//     } else {
//         echo json_encode(['status' => 'error', 'message' => 'Failed to update counter.']);
//     }

//     $stmt->close();
// } else {
//     echo json_encode(['status' => 'error', 'message' => 'Invalid form ID.']);
// }

// $conn->close();

// header('Location: ../crm/editform.php?form_id=' . $form_id);

?>
