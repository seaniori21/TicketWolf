<?php
session_start();
if (isset($_SESSION['primary_id']) && isset($_SESSION['username'])) {
    header("Location: listing.php");  
}else{   
    include '../includes/header.php';
    include '../functions/functions.php';
} 

/* Sign in*/
?>



<div class='main-container'>

    <div class='login-container'>

        <div class='left-right-container' >
            <div class='img-cont'>
                <img src="../assets/img/tw_logo.jpg" alt="Logo" class='left-img'>
            </div>

            <div class='right-container'>
                <div class='header-container'>
                    <div style='display:flex; flex:1; padding-top:0px'></div>
                    <div class='title-text' >
                    Sign In
                    </div>
                    <div class='ben-nino' style='flex:1; padding-top:0px'>
                        
                        <span style="color:#4169e1">Ben</span><span style="color:#ff6347">&</span><span style="color:#4169e1">Nino</span>
                    </div>
                </div>
                
        
                <form action="../functions/login_submit.php" method="post" class='form-login'>
                    <div class="form-group">
                        <label for="username" style='font-size:12px;'>USERNAME<span class="required">*</span></label>
                        <input type="text" id="username" 
                        name="username" 
                        required>
                        
                    </div>

                    <div class="form-group">
                        <label for="password" style='font-size:12px;'>PASSWORD<span class="required">*</span></label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class='error' id=errorMessage>
                        <!-- dfsd -->
                    </div>

                    <div class="form-group full-width">
                        <button type="submit" id="submit-btn">Sign In</button>
                    </div>
                </form>

            </div>
            

        </div>

    </div>

</div>
<?php
if (isset($_GET['error'])) {
    // Get the error message from the URL (GET request)
    $error = $_GET['error']; 

    // Pass the error message to JavaScript and update the HTML content
    echo '
    <script>
        var errorMessage = "' . errorMessage(addslashes($error)) . '";
        document.getElementById("errorMessage").innerText = errorMessage;
        console.log(errorMessage)
    </script>
    ';
}
?>



<?php
include('../includes/footer.php');
?>