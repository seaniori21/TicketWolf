<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

function sendEmail($first_name, $last_name, $email, $phone, $vin, $drivers_license, $license_plate, $is_owner, 
$registered_in_ny, $have_insurance, $have_title, $have_owner_license, $ticket_number, $files = []) {
    $mail = new PHPMailer;

    // Enable SMTP debugging for testing purposes
    $mail->SMTPDebug = 2; // Or SMTP::DEBUG_SERVER
    $mail->Debugoutput = 'html';

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl'; // SSL encryption
        $mail->SMTPAuth = true;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465; // Port for SSL
        $mail->Username = 'towwolf1@gmail.com'; // Replace with your email
        $mail->Password = 'ylym ugjo hzqd dhad'; // Replace with your App Password

        // Email settings
        $mail->setFrom('towwolf1@gmail.com', 'Contact Form'); // Sender email and name
        $mail->addAddress('seaniblade@gmail.com'); // Recipient email
        $mail->Subject = 'Your Ticket Number Is: '. $ticket_number;  // Adjust subject based on the form

        // Create the email body using the passed values
        $mail->Body = "
            First Name: $first_name\n
            Last Name: $last_name\n
            Email: $email\n
            Phone: $phone\n
            VIN: $vin\n
            Driver's License: $drivers_license\n
            License Plate: $license_plate\n
            Is Owner: $is_owner\n
            Registered in NY: $registered_in_ny\n
            Have Insurance: $have_insurance\n
            Have Title: $have_title\n
            Have Owner's License: $have_owner_license\n
            Ticket Number: $ticket_number
        ";

        // Add attachments if any files are uploaded
        if (!empty($files)) {
            foreach ($files as $file) {
                // Ensure the file exists before adding it as an attachment
                if (file_exists($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
                    $mail->addAttachment($file['tmp_name'], $file['name']); // Attach file
                }
            }
        }

        // Send email
        if ($mail->send()) {
            return true;
        } else {
            return "ERROR: " . $mail->ErrorInfo;
        }
        
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>