<?php
session_start();
include('conn_db.php');

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = strtolower(validate($_POST['username']));
    $password = validate($_POST['password']);

    if (empty($username)) {        
        header("Location: ../crm/index.php?error=1"); 
        exit();    
    }
    else if(empty($password)){        
        header("Location: ../crm/index.php?error=1");        
        exit();    
    }
    else{
        $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";       
        $result = mysqli_query($conn, $sql); 
        if (mysqli_num_rows($result) === 1) {            
            $row = mysqli_fetch_assoc($result); 
            if ($row['username'] === $username && $row['password'] === $password) {                
                echo "Logged in!";                
                $_SESSION['username'] = $row['username'];                
                $_SESSION['name'] = $row['name'];              
                $_SESSION['primary_id'] = $row['primary_id'];     
                echo  "we are here "  .   $_SESSION['username']   ;
                header("Location: ../crm/listing.php");                
                exit();            
            }    
            else{                
                header("Location: ../crm/index.php?error=3");                
                exit();            
            }  
        }
    }
    header("Location: ../crm/index.php?error=3"); 
} else {
    header("Location: ../crm/index.php?error=3"); 
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    $conn->close();
}

function validate($data){       
    $data = trim($data);       
    $data = stripslashes($data);       
    $data = htmlspecialchars($data);      
    return $data;    
}
?>