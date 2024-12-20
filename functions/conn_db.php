<?php
$servername = "auth-db1619.hstgr.io"; 
$username = "u760648682_towwolf_app";         
$password = "BaGoLax1*7";             
$dbname = "u760648682_app";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>