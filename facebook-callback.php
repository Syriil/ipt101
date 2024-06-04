<?php
require_once 'vendor/autoload.php'; 
session_start(); 
include "db_conn.php"; 

// Initialize the Facebook SDK with your app credentials
$fb = new \Facebook\Facebook([
  'app_id' => '782353517009684',
  'app_secret' => '4f727a4fadf9441e5b7847fa2ef2a5c6',
  'default_graph_version' => 'v12.0',
]);

$helper = $fb->getRedirectLoginHelper(); 

try {
  $accessToken = $helper->getAccessToken(); 
  if (isset($accessToken)) { 
    $response = $fb->get('/me?fields=id,name,email', $accessToken); 
    $user = $response->getGraphUser();
    $email = $user['email']; 
    $name = $user['name']; 
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
    } else { // If user doesn't exist, register the new user
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
} catch(\Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage(); 
  exit;
} catch(\Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage(); 
}
?>
