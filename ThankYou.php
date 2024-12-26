<?php
// Include shared components
include 'includes/header.php';


if (isset($_GET['ticket'])) {
    $ticket = $_GET['ticket'];
} 


?>



<div class='main-container'>

    <div class='form-container'>
        <h1>Your response has been filed!</h1>

        <h2>Your ticket number is: <?php echo htmlspecialchars($ticket); ?></h2>

        <p>You will get an email copy of this message.<br> A representative will help you shortly.</p>

    </div>

</div>