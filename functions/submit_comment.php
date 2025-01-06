<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = htmlspecialchars($_POST['user_id']);
    $form_id = htmlspecialchars($_POST['form_id']);
    $comment = htmlspecialchars($_POST['comment']);

    include('conn_db.php');

    $query = "INSERT INTO comments (user_id, form_id, comment) VALUES ('$user_id', '$form_id', '$comment')";
    if ($conn->query($query) === TRUE) {
        echo "Comment submitted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
    $conn->close();
    header('Location: ../crm/viewform.php?form_id=' . $form_id);
}
?>
