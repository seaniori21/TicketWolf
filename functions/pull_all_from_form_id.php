<?php
$servername = "auth-db1619.hstgr.io"; 
$username = "u760648682_towwolf_app";         
$password = "BaGoLax1*7";             
$dbname = "u760648682_app";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully!<br><br>";

// Get form_id from website link
$form_id = isset($_GET['form_id']) ? $_GET['form_id'] : null;

if ($form_id) {
    // Fetch ticket data from tickets table
    $stmt = $conn->prepare("SELECT * FROM tickets WHERE form_id = ?");
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("i", $form_id);
    $stmt->execute();

    // Fetch the result for ticket
    $ticket_result = $stmt->get_result();

    if ($ticket_result->num_rows > 0) {
        // Fetch the row as an associative array
        $ticket_data = $ticket_result->fetch_assoc();

        // Fetch insurance files related to this form_id
        $stmt2 = $conn->prepare("SELECT * FROM insurance_files WHERE form_id = ?");
        if (!$stmt2) {
            die("SQL Error: " . $conn->error);
        }

        $stmt2->bind_param("i", $form_id);
        $stmt2->execute();

        // Fetch the result for insurance files
        $insurance_result = $stmt2->get_result();

        $insurance_files = [];
        if ($insurance_result->num_rows > 0) {
            // Fetch all insurance files
            while ($file = $insurance_result->fetch_assoc()) {
                // Convert the BLOB to a base64 encoded string
                $file['file_data'] = base64_encode($file['file_data']); // 'file_data' is the column name where BLOB is stored
                $insurance_files[] = $file;
            }
        } else {
            echo "No insurance files found for this form ID.<br>"; // Debugging message if no files are found
        }


    } else {
        echo json_encode(['status' => 'error', 'message' => 'Form ID not found.']);
    }

    $stmt->close();
    $stmt2->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid form ID.']);
}

$conn->close();
?>


<?php
// Include shared components
include '../includes/header.php';
?>

<div class='main-container'>
    <div class='data-container'>
        <h1>The form data is here</h1>
        
        <?php if ($ticket_data): ?>
            <h2>Ticket Information</h2>
            <div class='align-left'>
                <ul>
                    <li><strong>Form ID:</strong> <?php echo htmlspecialchars($ticket_data['form_id']); ?></li>
                    <li><strong>Name:</strong> <?php echo htmlspecialchars($ticket_data['first_name']) . " " . htmlspecialchars($ticket_data['last_name']); ?></li>
                    <li><strong>Email:</strong> <?php echo htmlspecialchars($ticket_data['email']); ?></li>
                    <li><strong>Phone:</strong> <?php echo htmlspecialchars($ticket_data['phone']); ?></li>
                    <li><strong>VIN:</strong> <?php echo htmlspecialchars($ticket_data['vin']); ?></li>
                    <li><strong>License Plate:</strong> <?php echo htmlspecialchars($ticket_data['license_plate']); ?></li>
                    <li><strong>Registered in NY:</strong> <?php echo htmlspecialchars($ticket_data['registered_in_ny']); ?></li>
                    <li><strong>Have Insurance:</strong> <?php echo htmlspecialchars($ticket_data['have_insurance']); ?></li>
                    <li><strong>Have Title:</strong> <?php echo htmlspecialchars($ticket_data['have_title']); ?></li>
                    <li><strong>Uploaded At:</strong> <?php echo htmlspecialchars($ticket_data['uploaded_at']); ?></li>
                </ul>
            </div>
        <?php else: ?>
            <p>No ticket data found for this form ID.</p>
        <?php endif; ?>

        <?php if (count($insurance_files) > 0): ?>
            <h2>Insurance Files</h2>
            <ul>
                <?php foreach ($insurance_files as $file): ?>
                    <div class="display-files-container">
                        <div class='align-left'>
                            <li>
                                <strong>File Name:</strong> <?php echo htmlspecialchars($file['file_name']); ?><br>
                                <strong>File Type:</strong> <?php echo htmlspecialchars($file['file_type']); ?><br>
                                <strong>Uploaded At:</strong> <?php echo htmlspecialchars($file['uploaded_at']); ?><br>
                        </div>
                                <img src="data:image/jpeg;base64,<?php echo $file['file_data']; ?>" alt="Insurance Image" width="200" /><br>
                            </li>
                    </div>
                   
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No insurance files found for this form ID.</p>
        <?php endif; ?>
    </div>
</div>