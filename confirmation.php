<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>TowWolf - CounterWolf</title>
    <link rel="icon" href="assets/img/favicon.png" type="logo-img">
</head>
<body>

<?php
if (isset($_GET['counter'])) {
    $counter = $_GET['counter'];
} 
?>

<div class='main-container'>

    <div class='form-container'>
        <h1>Your submission has been filed!</h1>

        <h2>Your Line Placement Number is: <?php echo htmlspecialchars($counter); ?></h2>

        <p>You will get an email copy of this message.<br> A representative will call you shortly.</p>

    </div>

</div>