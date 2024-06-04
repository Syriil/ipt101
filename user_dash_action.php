<?php

include "db.php";


session_start();


$username_session = $_SESSION['username'];

$sql_user = "SELECT user_id, username, password, Last_name, First_name, Middle_name, Email FROM user WHERE username = '$username_session'";
$result_user = mysqli_query($conn, $sql_user);

if ($result_user && mysqli_num_rows($result_user) > 0) {
    $row_user = mysqli_fetch_assoc($result_user);
    $user_id = $row_user['user_id'];
    $current_password = $row_user['password'];

    // Retrieve user inputs from the form
    $new_password = $_POST['password'];

    // Regular expressions for validation
    function validatePassword($password)
    {
        return preg_match('/^(?=.*[A-Za-z])(?=.*[\d@#$*])[A-Za-z\d@#$*]+$/', $password);
    }

    $errors = array();

    // Validate inputs
    if (empty($new_password) || strlen($new_password) < 6 || !validatePassword($new_password)) {
        $errors[] = "Password must be at least 6 characters long and contain at least one letter, one digit, and a symbol.";
    }

    if ($new_password == $current_password) {
        $errors[] = "The new password cannot be the same as the current password.";
    }

    // Handle errors
    if (!empty($errors)) {
        $error_message = implode(", ", $errors);
        $_SESSION['user_pass_error'] = $error_message; 
        header("Location: dashboard.php"); 
        exit();
    }

    $sql = "UPDATE user SET 
             password = '$new_password'
             WHERE user_id = $user_id";

    // Execute the SQL query
    if (mysqli_query($conn, $sql)) {
        
        header("Location: dashboard.php?success=Your password has been updated successfully");
    } else {
        
        $error_message = mysqli_error($conn); 
        $error_message = urlencode("Your password could not be updated: $error_message");
        header("Location: dashboard.php?error=$error_message");
        exit();
    }
}
?>
