<?php
session_start();
if (isset($_SESSION['primary_id']) && isset($_SESSION['username'])) {
    header("Location: ListingPage.php");  
}else{   
    include 'includes/header.php';
} 
?>



<div class='main-container'>

    <div class='login-container'>

        <div class='left-right-container' >
            <div class='img-cont'>
                <img src="assets/img/tw_logo.png" alt="Logo" class='left-img'>
            </div>

            
            <form action="functions/login_submit.php" method="post" class='right-container'>
                <div class="form-group">
                <?php
                    if (isset($_GET['error'])) {
                        $error = isset($_GET['error']);
                        // echo '<script> </script>'
                        echo '<script>alert("error: wrong username or password")</script>';
                    }
                ?>
                    <label for="username">Username <span class="required">*</span></label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group full-width">
                    <button type="submit" id="submit-btn">Sign In</button>
                </div>
            </form>
            

        </div>

    </div>

</div>