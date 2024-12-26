<?php
include('conn_db.php');
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

$sql_create_user_table = "
CREATE TABLE IF NOT EXISTS users (
    primary_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);
";
// Execute the table creation query
if ($conn->query($sql_create_user_table) === TRUE) {
    echo "Table 'users' created successfully or already existed!<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

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
    echo "Table 'tickets' created successfully or already existed!<br>";
} else {
    echo "Error creating table: " . $conn->error;
}


// $sql_add_registration_column = "
// ALTER TABLE tickets
// ADD have_registration ENUM('yes', 'no') NOT NULL DEFAULT 'no';
// ";
// // Execute the ALTER TABLE query
// if ($conn->query($sql_add_registration_column) === TRUE) {
//     echo "Column 'have_registration' added successfully!<br>";
// } else {
//     echo "Error adding column: " . $conn->error;
// }


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
    echo "Table 'insurance_files' created successfully or already existed!<br>";
} else {
    echo "Error creating insurance_files table: " . $conn->error;
}

$sql_create_registration_files_table = "
CREATE TABLE IF NOT EXISTS registration_files (
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(255) NOT NULL,
    file_data LONGBLOB NOT NULL, -- Store binary file data
    form_id INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (form_id) REFERENCES tickets(form_id) ON DELETE CASCADE ON UPDATE CASCADE
);
";
// Execute the table creation query
if ($conn->query($sql_create_registration_files_table) === TRUE) {
    echo "Table 'registration_files' created successfully or already existed!<br>";
} else {
    echo "Error creating registration_files table: " . $conn->error;
}


$sql_create_title_files_table = "
CREATE TABLE IF NOT EXISTS title_files (
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(255) NOT NULL,
    file_data LONGBLOB NOT NULL, -- Store binary file data
    form_id INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (form_id) REFERENCES tickets(form_id) ON DELETE CASCADE ON UPDATE CASCADE
);
";
// Execute the table creation query
if ($conn->query($sql_create_title_files_table) === TRUE) {
    echo "Table 'title_files' created successfully or already existed!<br>";
} else {
    echo "Error creating title_files table: " . $conn->error;
}



$sql_create_license_files_table = "
CREATE TABLE IF NOT EXISTS license_files (
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(255) NOT NULL,
    file_data LONGBLOB NOT NULL, -- Store binary file data
    form_id INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (form_id) REFERENCES tickets(form_id) ON DELETE CASCADE ON UPDATE CASCADE
);
";
// Execute the table creation query
if ($conn->query($sql_create_license_files_table) === TRUE) {
    echo "Table 'license_files' created successfully or already existed!<br>";
} else {
    echo "Error creating license_files table: " . $conn->error;
}


$conn->close();

?>