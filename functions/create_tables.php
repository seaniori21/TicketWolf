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
// // SQL to drop the counter table
// $sql_drop_counter_table = "DROP TABLE IF EXISTS counter";
// // Execute the table drop query
// if ($conn->query($sql_drop_counter_table) === TRUE) {
//     echo "Table 'counter' dropped successfully!<br>";
// } else {
//     echo "Error dropping table: " . $conn->error;
// }




/*
Make user roles table
One for view only and edit only
add to user_table the num days cant access files 

add column for type of tow

add other files in form...add random files whatevs
*/


// $sql_alter_table = "
// ALTER TABLE counter
// ADD type_of_tow VARCHAR(255)
// ";
// // Execute the table creation query
// if ($conn->query($sql_alter_table) === TRUE) {
//     echo "Altered table successfully!<br>";
// } else {
//     echo "Error altering table: " . $conn->error;
// }
$sql_create_comments_table = "
CREATE TABLE IF NOT EXISTS comments (
    primary_id INT AUTO_INCREMENT PRIMARY KEY,
    comment VARCHAR(255) NOT NULL,
    form_id INT NOT NULL,
    user_id INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (form_id) REFERENCES counter(form_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(primary_id) ON DELETE CASCADE ON UPDATE CASCADE
);
";
// Execute the table creation query
if ($conn->query($sql_create_comments_table) === TRUE) {
    echo "Table 'comments' created successfully or already existed!<br>";
} else {
    echo "Error creating table: " . $conn->error;
}


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

$sql_create_system_logs_table = "
CREATE TABLE IF NOT EXISTS system_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    user_id INT NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    change_details TEXT NOT NULL,
    FOREIGN KEY (form_id) REFERENCES counter(form_id),
    FOREIGN KEY (user_id) REFERENCES users(primary_id)
);
";
// Execute the table creation query
if ($conn->query($sql_create_system_logs_table) === TRUE) {
    echo "Table 'system_logs' created successfully or already existed!<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

$sql_create_counter_table = "
CREATE TABLE IF NOT EXISTS counter (
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
if ($conn->query($sql_create_counter_table) === TRUE) {
    echo "Table 'counter' created successfully or already existed!<br>";
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
    FOREIGN KEY (form_id) REFERENCES counter(form_id) ON DELETE CASCADE ON UPDATE CASCADE
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
    FOREIGN KEY (form_id) REFERENCES counter(form_id) ON DELETE CASCADE ON UPDATE CASCADE
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
    FOREIGN KEY (form_id) REFERENCES counter(form_id) ON DELETE CASCADE ON UPDATE CASCADE
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
    FOREIGN KEY (form_id) REFERENCES counter(form_id) ON DELETE CASCADE ON UPDATE CASCADE
);
";
// Execute the table creation query
if ($conn->query($sql_create_license_files_table) === TRUE) {
    echo "Table 'license_files' created successfully or already existed!<br>";
} else {
    echo "Error creating license_files table: " . $conn->error;
}

$sql_create_additional_files_table = "
CREATE TABLE IF NOT EXISTS additional_files (
    primary_id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(255) NOT NULL,
    file_data LONGBLOB NOT NULL, -- Store binary file data
    form_id INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT,
    FOREIGN KEY (form_id) REFERENCES counter(form_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(primary_id) ON DELETE CASCADE ON UPDATE CASCADE
);
";
// Execute the table creation query
if ($conn->query($sql_create_additional_files_table) === TRUE) {
    echo "Table 'additional_files' created successfully or already existed!<br>";
} else {
    echo "Error creating additional_files table: " . $conn->error;
}


$conn->close();

?>