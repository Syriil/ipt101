<?php
// Database connection
include "db.php";

// Retrieve profile picture from database
$sql = "SELECT profile_picture FROM user_profile WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
$user_id = 1; // Assuming user_id is 1, you can change this according to your user session
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_bind_result($stmt, $profile_picture);
    mysqli_stmt_fetch($stmt);
    header("Content-type: image/jpeg"); // Assuming JPEG format, change if needed
    echo $profile_picture;
} else {
    // Default profile picture or error handling
    echo "Profile picture not found.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>