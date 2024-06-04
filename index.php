<?php

session_start();


include "db_conn.php";


if (isset($_POST['username']) && isset($_POST['password'])) { 
    
    
    function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    
    $username = validate($_POST['username']);   
    $password = validate($_POST['password']);

    
    if (empty($username)) {
        header("Location: loginform.php?error=Username is required");
        exit();
    }
    
    else if (empty($password)){
        header("Location: loginform.php?error=Password is required");
        exit();
    }
   
    else {
        
        $sql = "SELECT * FROM user WHERE username = ? AND password = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // To check if there is a matching user record
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            // To check if the email is verified
            if ($row['verified'] == 0) {
                // Redirect user to a verification page or display a message
                header("Location: loginform.php?error=Please verify your email first. Check your email for the verification link");
                exit();
            }

            // To check if username and password match
            if ($row['username'] === $username && $row['password'] === $password) {
                echo "Logged in!";
                
                // Set session variables for user
                $_SESSION['username'] = $row['username'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['role'] = $row['role'];

                // Update Active status to 'online'
                $update_sql = "UPDATE user SET Active = 'Online' WHERE user_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "i", $row['user_id']);
                mysqli_stmt_execute($update_stmt);

                
                    header("Location: dashboard.php");
                
                exit();
            }
            
            else {
                header("Location: loginform.php?error=Incorrect Username or password");
                exit();
            }
        }
        
        else {
            header("Location: loginform.php?error=Incorrect Username or password");
            exit();
        }
    }
}

else {
    header("Location: loginform.php");
    exit();
}
?>
