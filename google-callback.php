<?php
require_once 'vendor/autoload.php'; 

session_start(); 
include "db.php"; 

$client = new Google_Client(); 
$client->setClientId('630759677866-dhr2r5noe97a9nna4ssdng5ahcqiuu03.apps.googleusercontent.com'); 
$client->setClientSecret('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'); 
$client->setRedirectUri('http://localhost/Lab3/google-callback.php'); 

if (isset($_GET['code'])) { 
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']); 

    if (array_key_exists('access_token', $token)) { 
        $client->setAccessToken($token['access_token']); 

        $oauth2 = new Google_Service_Oauth2($client); 

        $email = $userInfo->email; 
        $name = $userInfo->name; 

        // Check if user already exists in database
        $sql = "SELECT * FROM user WHERE Email='$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) { 
            $row = mysqli_fetch_assoc($result);
            $_SESSION['username'] = $row['username']; 
            $_SESSION['name'] = $row['First_name'];
            $_SESSION['user_id'] = $row['user_id']; 

            // Update verified status and Active status to 'Online'
            $update_sql = "UPDATE user SET verified = 1, Active = 'Online' WHERE user_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "i", $row['user_id']);
            mysqli_stmt_execute($update_stmt);
        } else { 
            $username = strtolower(str_replace(' ', '_', $name)); 
            $default_password = 'admin'; 
            $insert_sql = "INSERT INTO user (First_name, Lastname, username, Email, password, Status, verified, Active) 
                VALUES ('$name', '', '$username', '$email', '$default_password', '', 1, 'Online')";
            if (mysqli_query($conn, $insert_sql)) {
                $user_id = mysqli_insert_id($conn);
                $_SESSION['username'] = $username; 
                $_SESSION['name'] = $name; 
                $_SESSION['user_id'] = $user_id; 

                // Insert user profile data into the user_profile table
                $profile_sql = "INSERT INTO user_profile (user_id, full_name) VALUES ('$user_id', '$name')";
                if (mysqli_query($conn, $profile_sql)) {
                    // Insert the default password into user_password_history table
                    $history_sql = "INSERT INTO user_password_history (user_id, password) VALUES ('$user_id', '$default_password')";
                    if (mysqli_query($conn, $history_sql)) {
                        echo "User, profile, and password history inserted successfully.";
                    } else {
                        echo "Error inserting into password history: " . mysqli_error($conn);
                    }
                } else {
                    echo "Error inserting profile: " . mysqli_error($conn);
                }
            } else {
                echo "Error: " . mysqli_error($conn);
                exit();
            }
        }

        header('Location: dashboard.php'); 
        exit();
    } else {
        header('Location: loginform.php?error=Failed to retrieve access token'); 
        exit();
    }
} else {
    header('Location: loginform.php?error=Google login failed'); 
    exit();
}
?>
