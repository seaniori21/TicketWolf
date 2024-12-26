<?php
include('conn_db.php');

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

if ($form_id) {
    $stmt = $conn->prepare("
        UPDATE tickets 
        SET first_name = ?, last_name = ?, email = ?, phone = ?, vin = ?, license_plate = ?, 
            registered_in_ny = ?, have_registration = ?, have_insurance = ?, have_title = ?
        WHERE form_id = ?
    ");
    
    $stmt->bind_param(
        "ssssssssssi",
        $first_name, $last_name, $email, $phone, $vin, $license_plate, 
        $registered_in_ny, $have_registration, $have_insurance, $have_title, $form_id
    );

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Ticket updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update ticket.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid form ID.']);
}

$conn->close();

header('Location: ../EditForm.php?form_id=' . $form_id);

?>
