<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';




function sendEmailCustomer($first_name, $last_name, $email, $phone, $vin, $drivers_license, $license_plate, $is_owner, 
$registered_in_ny, $have_insurance, $have_title, $have_owner_license, $ticket_number,  $insuranceFiles = [], $titleFiles = [], $licenseFiles = []) {
    $mail = new PHPMailer;

    // Enable SMTP debugging for testing purposes
    // $mail->SMTPDebug = 2; // Or SMTP::DEBUG_SERVER
    // $mail->Debugoutput = 'html';

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
        $mail->addAddress($email); // Recipient email
        $mail->Subject = 'Your Ticket Number Is: '. $ticket_number;  // Adjust subject based on the form

        // HTML email body
        $mail->isHTML(true);  // Enable HTML in the body
        $mail->Body = "
            <p style='margin: 0; padding: 0;'>This is your response to the Ticket request form!!</p>
            <p style='margin: 0; padding: 0;'><strong>Your Ticket Number Is:</strong> $ticket_number</p>

            <p style='margin-bottom: 0; padding-bottom: 0;'>Please note the following are required documents. If any are missing, the vehicle may not be released. Please ensure you have all proper documents ready when your ticket number is called:</p>
            <ol style='margin-top: 0; padding-left: 20px;'>
                <li>Vehicle registration card</li>
                <li>Vehicle owner's driver's license ID card</li>
                <li>Vehicle insurance card or title document</li>
            </ol>

            <p>Thank you for submitting your request. We will notify you when your ticket number is ready for processing.</p>

            <ul>";

        // Send email
        if ($mail->send()) {
            return true;
        } else {
            return false;//"ERROR: " . $mail->ErrorInfo;
        }
        
    } catch (Exception $e) {
        return ;//"Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}


function sendEmailClerk($admin_email,$first_name, $last_name, $email, $phone, $vin, $drivers_license, $license_plate, $is_owner, 
$registered_in_ny, $have_insurance, $have_title, $have_owner_license, $ticket_number,  
$insuranceFiles = [], $titleFiles = [], $licenseFiles = [], $registrationFiles = []  ) {
    $mail = new PHPMailer;

    // Enable SMTP debugging for testing purposes
    // $mail->SMTPDebug = 2; // Or SMTP::DEBUG_SERVER
    // $mail->Debugoutput = 'html';

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
        $mail->addAddress($admin_email); // Recipient email
        $mail->Subject = 'Your Ticket Number Is: '. $ticket_number;  // Adjust subject based on the form

        // HTML email body
        $mail->isHTML(true);  // Enable HTML in the body
        $mail->Body = "
            <p><strong>First Name:</strong> $first_name</p>
            <p><strong>Last Name:</strong> $last_name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>VIN:</strong> $vin</p>
            <p><strong>Driver's License:</strong> $drivers_license</p>
            <p><strong>License Plate:</strong> $license_plate</p>
            <p><strong>Is Owner:</strong> $is_owner</p>
            <p><strong>Registered in NY:</strong> $registered_in_ny</p>
            <p><strong>Have Insurance:</strong> $have_insurance</p>
            <p><strong>Have Title:</strong> $have_title</p>
            <p><strong>Have Owner's License:</strong> $have_owner_license</p>
            <p><strong>Ticket Number:</strong> $ticket_number</p>

            <h3>Insurance Documents</h3>
            <ul>";

        if (!empty($insuranceFiles)) {
            foreach ($insuranceFiles as $file) {
                $mail->addAttachment($file['tmp_name'], $file['name']); // Attach file
                $mail->Body .= "<li>" . htmlspecialchars($file['name']) . "</li>"; // Display attached file name in the email
            }
        } else {
            $mail->Body .= "<li>No insurance files uploaded.</li>";
        }

        $mail->Body .= "</ul>";

        // Title Files
        $mail->Body .= "<h3>Title Documents</h3><ul>";

        if (!empty($titleFiles)) {
            foreach ($titleFiles as $file) {
                $mail->addAttachment($file['tmp_name'], $file['name']); // Attach file
                $mail->Body .= "<li>" . htmlspecialchars($file['name']) . "</li>"; // Display attached file name in the email
            }
        } else {
            $mail->Body .= "<li>No title files uploaded.</li>";
        }

        $mail->Body .= "</ul>";

        // License Files
        $mail->Body .= "<h3>License Documents</h3><ul>";

        if (!empty($licenseFiles)) {
            foreach ($licenseFiles as $file) {
                $mail->addAttachment($file['tmp_name'], $file['name']); // Attach file
                $mail->Body .= "<li>" . htmlspecialchars($file['name']) . "</li>"; // Display attached file name in the email
            }
        } else {
            $mail->Body .= "<li>No license files uploaded.</li>";
        }

        $mail->Body .= "</ul>";

        // License Files
        $mail->Body .= "<h3>Registration Documents</h3><ul>";

        if (!empty($registrationFiles)) {
            foreach ($registrationFiles as $file) {
                $mail->addAttachment($file['tmp_name'], $file['name']); // Attach file
                $mail->Body .= "<li>" . htmlspecialchars($file['name']) . "</li>"; // Display attached file name in the email
            }
        } else {
            $mail->Body .= "<li>No Registration files uploaded.</li>";
        }

        $mail->Body .= "</ul>";

        // Send email
        if ($mail->send()) {
            return true;
        } else {
            return false;//"ERROR: " . $mail->ErrorInfo;
        }
        
    } catch (Exception $e) {
        return ;//"Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>