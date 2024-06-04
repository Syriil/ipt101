<?php

include "db.php";


session_start();


$email = $_GET['email'] ?? '';
$verification_code = $_GET['code'] ?? '';


$email = urldecode($email);
$verification_code = urldecode($verification_code);


if (!empty($email) && !empty($verification_code)) {

    $sql = "SELECT * FROM user WHERE Email=? AND verification_code=?";
    $stmt = mysqli_stmt_init($conn);
    
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "SQL statement failed";
    } else {
      
        mysqli_stmt_bind_param($stmt, "ss", $email, $verification_code);
   
        mysqli_stmt_execute($stmt);
      
        $result = mysqli_stmt_get_result($stmt);
        
        
        if (mysqli_num_rows($result) > 0) {
           
            $sql_update = "UPDATE user SET verified = 1 WHERE Email=? AND verification_code=?";
            $stmt_update = mysqli_stmt_init($conn);
            
          
            if (!mysqli_stmt_prepare($stmt_update, $sql_update)) {
                echo "SQL statement failed";
            } else {
                
                mysqli_stmt_bind_param($stmt_update, "ss", $email, $verification_code);
                
                mysqli_stmt_execute($stmt_update);
                ?>
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Email Verified</title>
                    <!-- Include Bootstrap CSS -->
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                </head>
                <body class="d-flex justify-content-center align-items-center vh-100 bg-light">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="card shadow">
                                    <div class="card-header text-center">
                                        <h1 class="h4">Email Verification</h1>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="alert alert-success" role="alert">
                                            Your email are verified.
                                        </div>
                                        <a href="index.php" class="btn btn-info w-50">Sign in</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Include Bootstrap JS -->
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
                    <script>
                    
                    if (window.history.replaceState) {
                        const url = new URL(window.location);
                        url.searchParams.delete('email');
                        url.searchParams.delete('code');
                        window.history.replaceState({ path: url.href }, '', url.href);
                    }
                    </script>
                </body>
                </html>
                <?php
             
                exit;
            }
        } else {
            
            echo "Invalid verification code.";
        }
    }
} else {
    
    echo "Email or verification code is missing.";
}


mysqli_close($conn);
?>
