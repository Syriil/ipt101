<?php
// Start session to manage user session data
session_start();

// Include database connection file
include "db_conn.php";

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Update Active status to 'Offline'
    $update_sql = "UPDATE user SET Active = 'Offline' WHERE user_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($update_stmt); 

    // Destroy the session
    session_unset();
    session_destroy();
}

// Redirect the user to the login page
header("Location: loginform.php");
exit();
?>
