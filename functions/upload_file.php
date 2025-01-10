<?php
include('conn_db.php');

session_start();
$user_id = isset($_SESSION['primary_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_id = isset($_POST['form_id']) ? $_POST['form_id'] : null;
    $file_group = isset($_POST['file_group']) ? $_POST['file_group'] : null;
    $upload_type = isset($_POST['upload_type']) ? $_POST['upload_type'] : null;

    if(!isset($_FILES['file'])){
        echo json_encode(['status' => 'error', 'message' => 'wrong file format']);
    }else 
    if ($form_id && $file_group) {
        $file_name = $_FILES['file']['name'];
        $file_type = $_FILES['file']['type'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $uploaded_at = date('Y-m-d H:i:s');

        // Read the file content
        $file_data = file_get_contents($file_tmp);

        // Determine the table based on the file group
        $table = $file_group . "_files"; // e.g., "insurance_files", "title_files"

        // Insert the file data into the appropriate table
        $stmt = $conn->prepare("INSERT INTO $table (form_id, file_name, file_type, file_data, uploaded_at, user_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssi", $form_id, $file_name, $file_type, $file_data, $uploaded_at, $user_id);

        if ($stmt->execute()) {
            if($upload_type === "file"){
                $stmt->close();
                header('Location: ../crm/editform.php?form_id=' . $form_id);
                exit();
            }
            echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully.', 'form_id' => $form_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload file.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing form_id and form_group']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();



?>
