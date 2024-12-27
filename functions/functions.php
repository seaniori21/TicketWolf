<?php
function errorMessage($e) {
    $errors = [
        "1" => "Wrong Username",
        "2" => "Wrong Password",
        "3" => "Invalid user name / password OR user Inactive."
    ];
    
    // Check if the error code exists in the array
    if (isset($errors[$e])) {
        return $errors[$e]; // Return the error message
    } else {
        return "Unknown error"; // Return a default message if error code is not found
    }
}
?>