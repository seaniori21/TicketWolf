<?php
session_start();
include('conn_db.php');

echo "Starting Login Attempt <br>";

$error = 0;

//variables for logs
$event = "Fail";
$ip_address = $_SERVER['REMOTE_ADDR'];
$browser_info = $_SERVER['HTTP_USER_AGENT'];
$event_type = "Login";
$user_id = NULL;


if (isset($_POST['username']) && isset($_POST['password'])) {
    echo "Checkin POST <br>";
    $username = strtolower(validate($_POST['username']));
    $password = validate($_POST['password']);

    if (empty($username)) {  
        echo "empty username <br>";      
        $error = 1;    
    }
    else if(empty($password)){   
        echo "empty password <br>";      
        $error = 2;          
    }
    else{
        echo "LOGIN HERE <br>";
        $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";       
        $result = mysqli_query($conn, $sql); 
        if (mysqli_num_rows($result) === 1) {            
            $row = mysqli_fetch_assoc($result);              
            echo "Logged in!";                
            $_SESSION['username'] = $row['username'];                
            $_SESSION['name'] = $row['name'];              
            $_SESSION['primary_id'] = $row['primary_id'];     
            echo  "we are here "  .   $_SESSION['username'];
            
            //LOGIN SUCCESS logs
            $user_id = $row['primary_id'];
            $event = "Success";
                     
              
             
        }else{
            echo "username or password wrong";            
            $error = 3;           
        } 
    }
    echo "After POST <br>";
} else {
    echo "empty username or password";  
    $error = 3;
    $conn->close();
}

$log_stmt = $conn->prepare("
    INSERT INTO system_logs (user_id, changed_at, event, ip_address, browser_info, event_type)
    VALUES (?, NOW(), ?, ?, ?, ?)
");

$log_stmt->bind_param("issss", $user_id, $event, $ip_address, $browser_info, $event_type);
if (!$log_stmt->execute()) {
    echo "Error: " . $log_stmt->error;
}
$log_stmt->close();


if($error==0){
    header("Location: ../crm/listing.php");  
}else{
    header("Location: ../crm/index.php?error=".$error);    
}
  



function validate($data){       
    $data = trim($data);       
    $data = stripslashes($data);       
    $data = htmlspecialchars($data);      
    return $data;    
}
?>