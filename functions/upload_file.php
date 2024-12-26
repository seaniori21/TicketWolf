<?php
include('conn_db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_id = isset($_POST['form_id']) ? $_POST['form_id'] : null;
    $file_group = isset($_POST['file_group']) ? $_POST['file_group'] : null;

    if ($form_id && $file_group && isset($_FILES['file'])) {
        $file_name = $_FILES['file']['name'];
        $file_type = $_FILES['file']['type'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $uploaded_at = date('Y-m-d H:i:s');

        // Read the file content
        $file_data = file_get_contents($file_tmp);

        // Determine the table based on the file group
        $table = $file_group . "_files"; // e.g., "insurance_files", "title_files"

        // Insert the file data into the appropriate table
        $stmt = $conn->prepare("INSERT INTO $table (form_id, file_name, file_type, file_data, uploaded_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $form_id, $file_name, $file_type, $file_data, $uploaded_at);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload file.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file upload.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();

header('Location: ../EditForm.php?form_id=' . $form_id);

?>
