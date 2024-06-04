<?php
session_start(); // Start the session to track the user's login status
include 'db_conn.php'; // Include the file that contains the database connection

// Check if the user is not logged in, then redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: loginform.php");
    exit();
}

// Initialize variables with default values
$full_name = $email = $address = $gender = $contact_info = $date_of_birth = $education = $skills = $social_media = $note = "";
$profile_picture = "AdminLTELogo.png"; // Default profile picture

// Retrieve user information from the "users" table
$username = $_SESSION['username'];

$sql_user = "SELECT user_id, username, password, Lastname, First_name, Middle_name, email FROM user WHERE username = ?";
$stmt_user = $conn->prepare($sql_user); // Prepare the SQL query
$stmt_user->bind_param('s', $username); // Bind parameters to the query
$stmt_user->execute(); // Execute the query
$result_user = $stmt_user->get_result(); // Get the result of the query
$user_data = $result_user->fetch_assoc(); // Fetch the user data as an associative array

if ($user_data) {
    $user_id = $user_data['user_id'];
    $current_db_password = $user_data['password'];

    // Retrieve user profile information from the "user_profile" table
    $sql_profile = "SELECT
        user.username,
        user.password,
        user.Lastname,
        user.First_name,
        user.Middle_name,
        user.Email,
        user.Status,
        user.Active,
        user.verification_code,
        user.verified,
        user_profile.full_name,
        user_profile.email AS profile_email,
        user_profile.phone_number,
        user_profile.address,
        user_profile.date_of_birth,
        user_profile.gender,
        user_profile.profile_picture,
        user_profile.contact_info,
        user_profile.note,
        user_profile.education,
        user_profile.skills,
        user_profile.social_media
    FROM user
    JOIN user_profile ON user.user_id = user_profile.user_id
    WHERE user.user_id = ?";

    $stmt_profile = $conn->prepare($sql_profile);
    $stmt_profile->bind_param('i', $user_id);
    $stmt_profile->execute();
    $result_profile = $stmt_profile->get_result();

    if ($result_profile && $result_profile->num_rows > 0) {
        // If profile exists, retrieve the profile information
        $row_profile = $result_profile->fetch_assoc();

        // Assigning data into variables
        $full_name = $row_profile['full_name'];
        $address = $row_profile['address'];
        $phone = $row_profile['phone_number'];
        $gender = $row_profile['gender'];
        $contact_info = $row_profile['contact_info'];
        $date_of_birth = $row_profile['date_of_birth'];
        $education = $row_profile['education'];
        $note = $row_profile['note'];
        $social_media = $row_profile['social_media'];
        $skills = $row_profile['skills'];
        $profile_picture = $row_profile['profile_picture'] ?: $profile_picture;

        // Additional user information
        $username = $row_profile['username'];
        $lastname = $row_profile['Lastname'];
        $first_name = $row_profile['First_name'];
        $middle_name = $row_profile['Middle_name'];
        $email = $row_profile['Email'];
        $status = $row_profile['Status'];
        $active = $row_profile['Active'];
        $verification_code = $row_profile['verification_code'];
        $verified = $row_profile['verified'];
    } else {
        // If profile doesn't exist, handle it here
    }
} else {
    // Redirect to login page if user information is not found
    header("Location: loginform.php");
    exit();
}

// Handle form submission for password update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify current password
    if ($current_password === $current_db_password) {
        // Check if new password is not the same as the current password
        if ($new_password === $current_db_password) {
            $message = "New password cannot be the same as the current password.";
            $message_type = "danger";
        } else {
            // Check if new password and confirm password match
            if ($new_password === $confirm_password) {
                // Check if new password is not in the password history
                $sql_check_password_history = "SELECT COUNT(*) as count FROM user_password_history WHERE user_id = ? AND password = ?";
                $stmt_check_password_history = $conn->prepare($sql_check_password_history);
                $stmt_check_password_history->bind_param('is', $user_id, $new_password);
                $stmt_check_password_history->execute();
                $result_check_password_history = $stmt_check_password_history->get_result();
                $row_check_password_history = $result_check_password_history->fetch_assoc();

                if ($row_check_password_history['count'] > 0) {
                    $message = "You cannot reuse an old password.";
                    $message_type = "danger";
                } else {
                    // Update the password in the user_password_history table
                    $sql_insert_password_history = "INSERT INTO user_password_history (user_id, password) VALUES (?, ?)";
                    $stmt_insert_password_history = $conn->prepare($sql_insert_password_history);
                    $stmt_insert_password_history->bind_param('is', $user_id, $new_password);

                    if ($stmt_insert_password_history->execute()) {
                        // Password history updated successfully
                    } else {
                        // Failed to update password history
                    }

                    // Update the password in the user table
                    $sql_update_password = "UPDATE user SET password = ? WHERE user_id = ?";
                    $stmt_update_password = $conn->prepare($sql_update_password);
                    $stmt_update_password->bind_param('si', $new_password, $user_id);

                    if ($stmt_update_password->execute()) {
                        $message = "Password updated successfully.";
                        $message_type = "success";
                    } else {
                        $message = "Error updating password.";
                        $message_type = "danger";
                    }
                }
            } else {
                $message = "New password and confirm password do not match.";
                $message_type = "danger";
            }
        }
    } else {
        $message = "Current password is incorrect.";
        $message_type = "danger";
    }
}

mysqli_close($conn); // Close the database connection
?>







<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <!-- AdminLTE CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    
  .profile-user-img {
    width: 150px;
    height: 150px;
  }

  </style>
</head>
<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <!-- Home Button -->
        <li class="nav-item d-none d-sm-inline-block">
          <a href="dashboard.php
          " class="nav-link">Home</a>
        </li>
      </ul>
      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-comments"></i>
            <span class="badge badge-danger navbar-badge">3</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <a href="#" class="dropdown-item">
              <!-- Message Start -->
              <div class="media">
                <div class="media-body">
                  <h3 class="dropdown-item-title">
                    Brad Diesel
                  </h3>
                  <p class="text-sm">Call me whenever you can...</p>
                </div>
              </div>
              <!-- Message End -->
            </a>
          </div>
        </li>
      </ul>
    </nav>
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">AdminLTE 3</span>
      </a>
      <!-- Sidebar -->
          <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Profile Link -->
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link" id="profile-btn">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>
                <!-- Sign Out Link -->
                <li class="nav-item">
                    <a href="logout.php" class="nav-link" id="sign-out-btn">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Sign Out</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    </aside>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1><?php echo isset($full_name) ? $full_name : "User"; ?> Profile</h1>
            </div>
          </div>
        </div>
      </section>
      <section class="content">
        <div class="container-fluid">
          <?php
          // Check for error messages (from both GET and session)
          if (isset($_GET['error'])) {
            $error_message = urldecode($_GET['error']);
            echo "<div class='alert alert-danger'>$error_message</div>";
          } elseif (isset($_SESSION['user_pass_error'])) {
            $error_message = $_SESSION['user_pass_error'];
            echo "<div class='alert alert-danger'>$error_message</div>";
            unset($_SESSION['user_pass_error']);  // Clear session error
          }
          // Check for success messages (from both GET and session)
          if (isset($_GET['success'])) {
            $success_message = urldecode($_GET['success']);
            echo "<div class='alert alert-success'>$success_message</div>";
          }
          ?>
          <div class="row">
            <div class="col-md-3">
              <!-- Profile Image -->
              <div class="card card-info card-outline">
                <div class="card-body box-profile">
                  <div class="text-center">
                   <?php
                    // Check if the user has a profile picture
                    if (!empty($profile_picture)) {
                      echo '<img class="profile-user-img img-fluid img-circle" src="data:image/jpeg;base64,'.base64_encode($profile_picture) . '" alt="User profile picture">';
                    } else {
                      echo '<img class="profile-user-img img-fluid img-circle" src="default_profile.jpg" alt="Blank profile picture">';
                    }
                    ?>
                  </div>
                  <h3 class="profile-username text-center"><?php echo $full_name; ?><span class="small">&nbsp;(<?php echo $username; ?>)</span></h3>
                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                      <b>Email</b> <a class="float-right"><?php echo $email; ?></a>
                    </li>
                  </ul>
                </div>
              </div>
              <!-- About Me Box -->
              <div class="card card-info">
                <div class="card-header">
                  <h3 class="card-title">About Me</h3>
                </div>
                <div class="card-body">
                  <strong><i class="bi bi-birthday-cake mr-1"></i> Birthday</strong>
                  <p class="text-muted"><?php echo $date_of_birth; ?></p>
                  <hr>
                  <strong><i class="bi bi-book mr-1"></i> Education</strong>
                  <p class="text-muted"><?php echo $education; ?></p>
                  <hr>
                  <strong><i class="bi bi-globe mr-1"></i> Social Media</strong>
                  <p class="text-muted">
                      <?php echo $social_media; ?>
                  </p>
                  <hr>
                  <strong><i class="bi bi-telephone mr-1"></i>Contact Info</strong>
                  <p class="text-muted">
                    <?php echo $contact_info; ?>
                  <hr>
                  <strong><i class="bi bi-gender-ambiguous mr-1"></i>Gender</strong>
                  <p class="text-muted">
                    <?php echo $gender; ?>
                    <hr>
                  <strong><i class="bi bi-gender-ambiguous mr-1"></i>Address</strong>
                  <p class="text-muted">
                    <?php echo $address; ?>
                </div>
              </div>
            </div>
            <div class="col-md-9">
              <div class="card card-info">
                <div class="card-header p-2">
                  <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link" href="#Change_Password" data-toggle="tab">Password</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#update_Profile" data-toggle="tab">Profile</a></li>
                    
                  </ul>
                </div>
                <div class="card-body">
                  <div class="tab-content">
                    <!-- Update Profile -->
                    <div class="tab-pane active" id="update_Profile">
                    <form action="indexdash.php" method="post" enctype="multipart/form-data">
                      <div class="form-group row">
                          <label for="full_name" class="col-sm-2 col-form-label">Name</label>
                          <div class="col-sm-10">
                              <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Name" value="<?= $full_name; ?>">
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="username" class="col-sm-2 col-form-label">Username</label>
                          <div class="col-sm-10">
                              <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" placeholder="Username" readonly="true">
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="email" class="col-sm-2 col-form-label">Email</label>
                          <div class="col-sm-10">
                              <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" placeholder="Email" readonly="true">
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="date_of_birth" class="col-sm-2 col-form-label">Birthday</label>
                          <div class="col-sm-10">
                              <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo $date_of_birth; ?>" >
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="gender" class="col-sm-2 col-form-label">Gender</label>
                          <div class="col-sm-10">
                              <select class="form-control" id="gender" name="gender" >
                                  <option value="Male" <?php if($gender == 'Male') echo 'selected'; ?>>Male</option>
                                  <option value="Female" <?php if($gender == 'Female') echo 'selected'; ?>>Female</option>
                                  <option value="Other" <?php if($gender == 'Other') echo 'selected'; ?>>Other</option>
                              </select>
                          </div>
                      </div>
                      <div class="form-group row">
                                            <label for="contact_info" class="col-sm-2 col-form-label">Contact Info</label>
                                            <div class="col-sm-10">
                                              <input type="text" class="form-control" id="contact_info" name="contact_info" value="<?php echo $contact_info; ?>" placeholder="Contact Info" >
                                            </div>
                                          </div>
                      <div class="form-group row">
                          <label for="profile_picture" class="col-sm-2 col-form-label">Profile Picture</label>
                          <div class="col-sm-10">
                              <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="address" class="col-sm-2 col-form-label">Address</label>
                          <div class="col-sm-10">
                              <input type="text" class="form-control" id="address" name="address" value="<?php echo $address; ?>" placeholder="Address" >
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="education" class="col-sm-2 col-form-label">Education</label>
                          <div class="col-sm-10">
                              <input type="text" class="form-control" id="education" name="education" value="<?php echo $education; ?>" placeholder="Education" >
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="social_media" class="col-sm-2 col-form-label">Social Media</label>
                          <div class="col-sm-10">
                              <input type="text" class="form-control" id="social_media" name="social_media" value="<?php echo $social_media; ?>" placeholder="Social Media" >
                          </div>
                      </div>
                      
                      <div class="form-group row">
                          <div class="offset-sm-2 col-sm-10">
                              <button type="submit" name="submit" class="btn btn-info">Modify</button>
                          </div>
                      </div>
                  </form>

                    </div>
                    <!-- Change Password -->
                    <div class="tab-pane" id="Change_Password">
                        <form action="#Change_password" method="post" class="form-horizontal">
                            <div class="card">
                                <div class="card-header bg-primary text-white">Update Password</div>
                                <div class="card-body">
                                    <?php if (isset($message)) : ?>
                                        <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                                            <?php echo $message; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="form-group row">
                                        <label for="current_password" class="col-sm-2 col-form-label">Current Password</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Current Password" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="new_password" class="col-sm-2 col-form-label">New Password</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="confirm_password" class="col-sm-2 col-form-label">Confirm Password</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="offset-sm-2 col-sm-10">
                                            <button type="submit" name="submit" class="btn btn-info">Modify</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
    <footer class="main-footer">
      <strong>&copy; 2024 <a href="https://rmmc.edu.ph">Ramon Magsaysay Memorial Colleges</a>.</strong>
      All rights reserved.
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 3.1.0
      </div>
    </footer>
  </div>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>
    <script>
      $(document).ready(function() {
        $('#profile-btn').on('click', function() {
          $('.edit-profile-form').toggle();
        });
      });
    </script>

  </body>
  </html>