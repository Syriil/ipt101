<?php

session_start();

include "db_conn.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

// Retrieve form data
$firstname = $_POST['First_name'];
$middlename = $_POST['Middle_name'];
$lastname = $_POST['Lastname'];
$username = $_POST['username'];
$email = $_POST['Email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$status = $_POST['Status'];
$verification_code = bin2hex(random_bytes(16)); 

// Function to validate password format
function validatePassword($password) {
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*[\d@#$*])[A-Za-z\d@#$*]+$/', $password)) {
        return false;
    }
    return true;
}

// Function to validate names containing only letters
function validateLetters($input) {
    // Check if the input contains only letters and spaces
    return preg_match('/^[A-Za-z ]+$/', $input);
}

// Function to validate email format
function validateEmail($email) {
    // Use PHP's filter_var function to validate email format
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Check if required fields are empty
if (empty($username) || empty($email) || empty($password) || empty($status)) {
    // Redirect to registration form with error message if any required field is empty
    header("Location: registrationform.php?error=Please fill in all fields");
    exit();
} else if ($password !== $confirm_password) {
    // Redirect to registration form with error message if passwords do not match
    header("Location: registrationform.php?error=Passwords do not match");
    exit();
} else {
    // Check if username or email already exists in the database
    $sql = "SELECT * FROM user WHERE username='$username' OR Email='$email'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        // Redirect to registration form with error message if username or email already exists
        header("Location: registrationform.php?error=Username or Email already exists");
        exit();
    }

    // Validate last name
    if (!validateLetters($lastname)) {
        // Redirect to registration form with error message if last name contains invalid characters
        header("Location: registrationform.php?error=Last name should contain only letters");
        exit();
    }

    // Validate first name
    if (!validateLetters($firstname)) {
        // Redirect to registration form with error message if first name contains invalid characters
        header("Location: registrationform.php?error=First name should contain only letters");
        exit();
    }

    // Insert user data into database
    $sql = "INSERT INTO user (First_name, Middle_name, Lastname, username, Email, password, verification_code, Status) 
            VALUES ('$firstname', '$middlename', '$lastname', '$username', '$email', '$password', '$verification_code', '$status')";

    if (mysqli_query($conn, $sql)) {
        try {
            // Get the user ID of the newly inserted user
            $user_id = mysqli_insert_id($conn);
            $full_name = $firstname . ' ' . $middlename . ' ' . $lastname;

            // Insert user profile data into the user_profile table
            $profile_sql = "INSERT INTO user_profile (user_id, full_name) VALUES ('$user_id', '$full_name')";
            if (mysqli_query($conn, $profile_sql)) {
                // Insert password history data into the user_password_history table
                $history_sql = "INSERT INTO user_password_history (user_id, password) VALUES ('$user_id', '$password')";
                if (mysqli_query($conn, $history_sql)) {
                    echo "User, profile, and password history inserted successfully.";

                    // Configure PHPMailer to send a verification email
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';  
                    $mail->SMTPAuth = true;
                    $mail->Username = 'dubriacyril@gmail.com'; 
                    $mail->Password = 'dndb hpri futy esau'; 
                    $mail->SMTPSecure = 'tls';          
                    $mail->Port = 587;                  

                    $mail->setFrom('dubriacyril@gmail.com');  
                    $mail->addAddress($email);  
                    $mail->isHTML(true);
                    $mail->Subject = 'Email Verification'; 
                    $mail->Body = 'Please click the "verify" link to verify your email: <a href="http://localhost/Lab3/verified.php?email='.$email.'&code='.$verification_code.'">Verify</a>';  // Email body

                    $mail->send();  

                    header("Location: sent_notice.php?message=Verification email sent. Please check your email to verify your account.");
                } else {
                    echo "Error inserting into password history: " . mysqli_error($conn);
                }
            } else {
                echo "Error inserting profile: " . mysqli_error($conn);
            }
        } catch (Exception $e) {
            header("Location: registrationform.php?error=Failed to send verification email. Please try again later.");
        }
    } else {
        echo "ERROR: Hush! Sorry $sql. " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
