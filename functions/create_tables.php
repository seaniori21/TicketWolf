<?php
$servername = "auth-db1619.hstgr.io"; 
$username = "u760648682_towwolf_app";         
$password = "BaGoLax1*7";             
$dbname = "u760648682_app";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully! <br>";

$conn->select_db($dbname);



// $sql_drop_file_table = "DROP TABLE IF EXISTS insurance_files";
// // Execute the table drop query
// if ($conn->query($sql_drop_file_table) === TRUE) {
//     echo "Table 'insurance_files' dropped successfully!<br>";
// } else {
//     echo "Error dropping table: " . $conn->error;
// }
// // SQL to drop the tickets table
// $sql_drop_tickets_table = "DROP TABLE IF EXISTS tickets";
// // Execute the table drop query
// if ($conn->query($sql_drop_tickets_table) === TRUE) {
//     echo "Table 'tickets' dropped successfully!<br>";
// } else {
//     echo "Error dropping table: " . $conn->error;
// }



$sql_create_tickets_table = "
CREATE TABLE IF NOT EXISTS tickets (
    form_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    vin VARCHAR(20) NOT NULL,
    drivers_license VARCHAR(255),
    license_plate VARCHAR(255),
    is_owner ENUM('yes', 'no') NOT NULL,
    registered_in_ny ENUM('yes', 'no') NOT NULL,
    have_insurance ENUM('yes', 'no') NOT NULL,
    have_title ENUM('yes', 'no') NOT NULL,
    have_owner_license ENUM('yes', 'no') NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";
// Execute the table creation query
if ($conn->query($sql_create_tickets_table) === TRUE) {
    echo "Table 'tickets' created successfully!<br>";
} else {
    echo "Error creating table: " . $conn->error;
}


$sql_create_insurance_files_table = "
CREATE TABLE IF NOT EXISTS insurance_files (
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(255) NOT NULL,
    file_data LONGBLOB NOT NULL, -- Store binary file data
    form_id INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (form_id) REFERENCES tickets(form_id) ON DELETE CASCADE ON UPDATE CASCADE
);
";
// Execute the table creation query
if ($conn->query($sql_create_insurance_files_table) === TRUE) {
    echo "Table 'insurance_files' created successfully!";
} else {
    echo "Error creating insurance_files table: " . $conn->error;
}


$conn->close();

?>