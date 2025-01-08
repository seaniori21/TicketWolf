<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';




function sendEmailCustomer($first_name, $last_name, $email, $phone, $vin, $drivers_license, $license_plate, $is_owner, 
$registered_in_ny, $have_insurance, $have_title, $have_owner_license, $counter_number,  $insuranceFiles = [], $titleFiles = [], $licenseFiles = []) {
    $mail = new PHPMailer;

    // Enable SMTP debugging for testing purposes
    // $mail->SMTPDebug = 2; // Or SMTP::DEBUG_SERVER
    // $mail->Debugoutput = 'html';

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl'; // SSL encryption
        $mail->SMTPAuth = true;
        $mail->Host = 'smtp.hostinger.com'; 
        $mail->Port = 465; // Port for SSL
        $mail->Username = 'support@towwolf.com'; // Replace with your email
        $mail->Password = 'enter_password'; // Replace with your App Password

        // Email settings
        $mail->setFrom('support@towwolf.com', 'support@towwolf.com'); // Sender email and name
        $mail->addAddress($email); // Recipient email
        $mail->Subject = 'Your Line Placement Number Is: '. $counter_number;  // Adjust subject based on the form

        // HTML email body
        $mail->isHTML(true);  // Enable HTML in the body
        $mail->Body = "
            <p style='margin: 0; padding: 0;'><strong>Your Line Placement Number Is:</strong> $counter_number</p>

            <p style='margin-bottom: 0; padding-bottom: 0;'>Please note the following are required documents. If any are missing, the vehicle will not be released. Please ensure you have all proper documents ready when your Line Placement number is called:</p>
            <ol style='margin-top: 0; padding-left: 20px;'>
                <li>Vehicle registration card</li>
                <li>Vehicle owner's driver's license ID card</li>
                <li>Vehicle insurance card or title document</li>
            </ol>

            <p>Thank you for submitting your request. We will notify you when your Line Placement number is ready for processing.</p>

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
$registered_in_ny, $have_insurance, $have_title, $have_owner_license, $counter_number,  
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
        $mail->Host = 'smtp.hostinger.com'; 
        $mail->Port = 465; // Port for SSL
        $mail->Username = 'support@towwolf.com'; // Replace with your email
        $mail->Password = 'enter_password'; // Replace with your App Password

        // Email settings
        $mail->setFrom('support@towwolf.com', 'support@towwolf.com'); // Sender email and name
        $mail->addAddress($admin_email); // Recipient email
        $mail->Subject = 'Your Line Number Is: '. $counter_number;  // Adjust subject based on the form

        $insuranceFilesCount = count($insuranceFiles);
        $titleFilesCount = count($titleFiles);
        $licenseFilesCount = count($licenseFiles);
        $registrationFilesCount = count($registrationFiles);

        // HTML email body
        $mail->isHTML(true);  // Enable HTML in the body
        $mail->Body = "
            <p><strong>First Name:</strong> $first_name<br>
            <strong>Last Name:</strong> $last_name<br>
            <strong>Email:</strong> $email<br>
            <strong>Phone:</strong> $phone<br>
            <strong>VIN:</strong> $vin<br>
            <strong>Driver's License:</strong> $drivers_license<br>
            <strong>License Plate:</strong> $license_plate</p>

            <p><strong>Is Owner:</strong> $is_owner<br>
            <strong>Registered in NY:</strong> $registered_in_ny<br>
            <strong>Have Insurance:</strong> $have_insurance<br>
            <strong>Have Title:</strong> $have_title<br>
            <strong>Have Owner's License:</strong> $have_owner_license<br>
            <strong>Line Number:</strong> $counter_number</p>

            <p><strong>Number of Insurance Files:</strong> $insuranceFilesCount<br>
            <strong>Number of Title Files:</strong> $titleFilesCount<br>
            <strong>Number of License Files:</strong> $licenseFilesCount<br>
            <strong>Number of Registration Files:</strong> $registrationFilesCount</p>
            ";

// <h3>Insurance Documents</h3>
        // if (!empty($insuranceFiles)) {
        //     foreach ($insuranceFiles as $file) {
        //         $mail->addAttachment($file['tmp_name'], $file['name']); // Attach file
        //         $mail->Body .= "<li>" . htmlspecialchars($file['name']) . "</li>"; // Display attached file name in the email
        //     }
        // } else {
        //     $mail->Body .= "<li>No insurance files uploaded.</li>";
        // }

        // $mail->Body .= "</ul>";

        // // Title Files
        // $mail->Body .= "<h3>Title Documents</h3><ul>";

        // if (!empty($titleFiles)) {
        //     foreach ($titleFiles as $file) {
        //         $mail->addAttachment($file['tmp_name'], $file['name']); // Attach file
        //         $mail->Body .= "<li>" . htmlspecialchars($file['name']) . "</li>"; // Display attached file name in the email
        //     }
        // } else {
        //     $mail->Body .= "<li>No title files uploaded.</li>";
        // }

        // $mail->Body .= "</ul>";

        // // License Files
        // $mail->Body .= "<h3>License Documents</h3><ul>";

        // if (!empty($licenseFiles)) {
        //     foreach ($licenseFiles as $file) {
        //         $mail->addAttachment($file['tmp_name'], $file['name']); // Attach file
        //         $mail->Body .= "<li>" . htmlspecialchars($file['name']) . "</li>"; // Display attached file name in the email
        //     }
        // } else {
        //     $mail->Body .= "<li>No license files uploaded.</li>";
        // }

        // $mail->Body .= "</ul>";

        // // License Files
        // $mail->Body .= "<h3>Registration Documents</h3><ul>";

        // if (!empty($registrationFiles)) {
        //     foreach ($registrationFiles as $file) {
        //         $mail->addAttachment($file['tmp_name'], $file['name']); // Attach file
        //         $mail->Body .= "<li>" . htmlspecialchars($file['name']) . "</li>"; // Display attached file name in the email
        //     }
        // } else {
        //     $mail->Body .= "<li>No Registration files uploaded.</li>";
        // }

        // $mail->Body .= "</ul>";

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